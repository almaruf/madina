@extends('shop.layout')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold mt-4">Shopping Cart</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div id="cart-items-container" class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-600">Loading cart...</p>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                
                <div class="space-y-3 mb-6 border-b border-gray-200 pb-4">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span id="subtotal">£0.00</span>
                    </div>
                    <div class="flex justify-between text-green-600">
                        <span>Discounts:</span>
                        <span id="discounts">-£0.00</span>
                    </div>
                    <div id="vat-line" class="flex justify-between text-sm text-gray-600" style="display: none;">
                        <span id="vat-label">VAT (20%):</span>
                        <span id="vat-amount">£0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Delivery Fee:</span>
                        <span id="delivery-fee">£0.00</span>
                    </div>
                </div>

                <div class="flex justify-between text-xl font-bold mb-6">
                    <span>Total:</span>
                    <span id="total">£0.00</span>
                </div>

                <button id="checkout-btn" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-credit-card"></i>
                    Proceed to Checkout
                </button>

                <a href="/" class="block text-center text-green-600 hover:text-green-700 mt-3 font-medium">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Cart management
    class ShoppingCart {
        constructor() {
            this.items = this.loadCart();
            this.deliveryFee = 5.00; // Default delivery fee
            this.shopConfig = null;
            this.init();
        }

        async init() {
            await this.loadShopConfig();
            this.render();
        }

        async loadShopConfig() {
            try {
                const response = await axios.get('/api/shop/config');
                this.shopConfig = response.data;
                this.deliveryFee = parseFloat(this.shopConfig.delivery_fee) || 5.00;
            } catch (error) {
                console.error('Failed to load shop config:', error);
                // Use defaults
                this.shopConfig = {
                    currency_symbol: '£',
                    vat: { registered: false, rate: 20.00, prices_include_vat: true }
                };
            }
        }

        loadCart() {
            const saved = localStorage.getItem('shopping_cart');
            return saved ? JSON.parse(saved) : [];
        }

        saveCart() {
            localStorage.setItem('shopping_cart', JSON.stringify(this.items));
            // Trigger storage event for other windows/tabs
            window.dispatchEvent(new Event('cartUpdated'));
        }

        addItem(productId, variationId, quantity = 1) {
            const existing = this.items.find(item => 
                item.product_id === productId && item.variation_id === variationId
            );

            if (existing) {
                existing.quantity += quantity;
            } else {
                this.items.push({
                    product_id: productId,
                    variation_id: variationId,
                    quantity: quantity
                });
            }

            this.saveCart();
            this.render();
        }

        removeItem(productId, variationId) {
            this.items = this.items.filter(item =>
                !(item.product_id === productId && item.variation_id === variationId)
            );

            this.saveCart();
            this.render();
        }

        updateQuantity(productId, variationId, quantity) {
            const item = this.items.find(item =>
                item.product_id === productId && item.variation_id === variationId
            );

            if (item) {
                item.quantity = Math.max(1, parseInt(quantity));
                this.saveCart();
                this.render();
            }
        }

        getSubtotal() {
            return this.items.reduce((sum, item) => sum + (item.quantity || 0), 0);
        }

        async render() {
            const container = document.getElementById('cart-items-container');

            if (this.items.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-cart text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-600 mb-4 text-lg">Your cart is empty</p>
                        <a href="/" class="text-green-600 hover:text-green-700 font-semibold inline-flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            Start Shopping
                        </a>
                    </div>
                `;
                document.getElementById('checkout-btn').disabled = true;
                this.updateSummary([]);
                localStorage.removeItem('cart_validated'); // Clear cache when empty
                return;
            }

            // Validate cart with server (if axios available)
            if (typeof axios === 'undefined') {
                // Fallback: render items without validation
                this.renderItems(this.items);
                this.updateSummary(this.items);
                document.getElementById('checkout-btn').disabled = false;
                return;
            }

            try {
                const response = await axios.post('/api/cart/validate', {
                    items: this.items
                }, {
                    timeout: 10000 // 10 second timeout
                });

                // Store validated items with details for later use
                localStorage.setItem('cart_validated', JSON.stringify(response.data.items));
                
                console.log('Cart validation success:', response.data.items);
                this.renderItems(response.data.items);
                this.updateSummary(response.data.items);
                document.getElementById('checkout-btn').disabled = false;
            } catch (error) {
                console.error('Cart validation error:', error);
                console.error('Error response:', error.response?.data);
                console.error('Error status:', error.response?.status);
                
                if (error.response?.status === 422 && error.response?.data?.items) {
                    // Some items unavailable
                    const items = error.response.data.items || [];
                    const errors = error.response.data.errors || [];
                    
                    // Store partially validated items
                    localStorage.setItem('cart_validated', JSON.stringify(items));
                    
                    this.renderItems(items);
                    this.updateSummary(items);
                    
                    // Show errors
                    if (errors.length > 0) {
                        const errorHtml = errors
                            .map(err => `<div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-2"><i class="fas fa-exclamation-circle"></i> ${err}</div>`)
                            .join('');
                        
                        container.innerHTML = `
                            <div class="mb-4">
                                ${errorHtml}
                            </div>
                            ${container.innerHTML}
                        `;
                    }
                    
                    document.getElementById('checkout-btn').disabled = items.length === 0;
                } else {
                    // Try to use previously validated items if available
                    let cachedItems = [];
                    try {
                        const cached = localStorage.getItem('cart_validated');
                        if (cached) {
                            cachedItems = JSON.parse(cached);
                        }
                    } catch (e) {
                        console.log('Cache read error:', e);
                    }
                    
                    if (cachedItems.length > 0) {
                        console.log('Using cached cart validation');
                        this.renderItems(cachedItems);
                        this.updateSummary(cachedItems);
                    } else {
                        // Last resort: show error message
                        container.innerHTML = `
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded mb-4">
                                <i class="fas fa-exclamation-triangle"></i> Unable to load cart details. 
                                <a href="/" class="font-semibold hover:underline">Click here to start shopping again</a>
                            </div>
                        `;
                    }
                    
                    document.getElementById('checkout-btn').disabled = cachedItems.length === 0;
                }
            }
        }

        renderItems(items) {
            const container = document.getElementById('cart-items-container');
            if (!items || items.length === 0) {
                container.innerHTML = '<p class="text-gray-600">Your cart is empty</p>';
                return;
            }

            let itemsHtml = '';
            items.forEach((item, idx) => {
                const quantity = item.quantity || 1;
                const price = parseFloat(item.price) || 0;
                const discountedUnitPrice = parseFloat(item.discounted_unit_price || price) || price;
                const discountAmount = parseFloat(item.discount_amount || 0) || 0;
                const total = parseFloat(item.discounted_total || item.total) || (price * quantity);
                const originalTotal = (price * quantity);
                const hasDiscount = discountAmount > 0 && total < originalTotal;
                const itemKey = `${item.product_id}_${item.variation_id}`;
                const offerLabel = item.offer?.badge_text || item.offer?.name || null;
                const offerType = item.offer?.type || null;
                const isBXGY = offerType === 'bxgy_free' || offerType === 'bxgy_discount';
                const isDiscount = offerType === 'percentage_discount' || offerType === 'fixed_discount';
                
                // BXGY and discount offer display
                let bxgyDetails = '';
                if (isBXGY && item.offer) {
                    if (offerType === 'bxgy_free') {
                        bxgyDetails = `<p class="text-xs text-green-700 font-semibold mt-1"><i class="fas fa-gift"></i> Buy ${item.offer.buy_quantity} Get ${item.offer.get_quantity} FREE</p>`;
                    } else if (offerType === 'bxgy_discount') {
                        bxgyDetails = `<p class="text-xs text-orange-700 font-semibold mt-1"><i class="fas fa-tag"></i> Buy ${item.offer.buy_quantity} Get ${item.offer.get_quantity} @ ${item.offer.get_discount_percentage}% OFF</p>`;
                    }
                } else if (isDiscount && item.offer) {
                    if (offerType === 'percentage_discount') {
                        bxgyDetails = `<p class="text-xs text-blue-700 font-semibold mt-1"><i class="fas fa-percent"></i> ${item.offer.discount_value}% OFF</p>`;
                    } else if (offerType === 'fixed_discount') {
                        bxgyDetails = `<p class="text-xs text-blue-700 font-semibold mt-1"><i class="fas fa-pound-sign"></i> £${item.offer.discount_value} OFF</p>`;
                    }
                }
                
                itemsHtml += `
                <div class="flex gap-4 pb-4 mb-4 border-b border-gray-200" data-item-key="${itemKey}">
                    <!-- Product Image -->
                    <div class="w-20 h-20 flex-shrink-0 bg-gray-200 rounded-lg overflow-hidden">
                        ${item.image_url ? `<img src="${item.image_url}" alt="${item.product_name}" class="w-full h-full object-cover">` : '<div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image text-2xl"></i></div>'}
                    </div>

                    <!-- Product Details -->
                    <div class="flex-1">
                        <a href="/products/${item.product_slug || ''}" class="text-lg font-semibold text-gray-900 hover:text-green-600">
                            ${item.product_name || 'Unknown Product'}
                        </a>
                        <p class="text-sm text-gray-600">${item.variation_name || ''}</p>
                        ${offerLabel ? `<span class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full text-white mt-1" style="background-color: ${item.offer?.badge_color || '#DC2626'};">${offerLabel}</span>` : ''}
                        ${bxgyDetails}
                        ${hasDiscount ? `
                            <div class="mt-1">
                                <p class="text-green-600 font-bold">£${discountedUnitPrice.toFixed(2)}</p>
                                <p class="text-xs text-gray-500 line-through">£${price.toFixed(2)}</p>
                                <p class="text-xs text-green-700 font-semibold">You save £${discountAmount.toFixed(2)}</p>
                            </div>
                        ` : `
                            <p class="text-green-600 font-bold mt-1">£${price.toFixed(2)}</p>
                        `}
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center gap-2">
                        <button class="btn-decrease bg-gray-200 px-2 py-1 rounded hover:bg-gray-300 transition">−</button>
                        <input type="number" class="qty-input w-12 text-center border border-gray-300 rounded px-2 py-1" min="1" value="${quantity}" data-product-id="${item.product_id}" data-variation-id="${item.variation_id}">
                        <button class="btn-increase bg-gray-200 px-2 py-1 rounded hover:bg-gray-300 transition">+</button>
                    </div>

                    <!-- Item Total -->
                    <div class="text-right">
                        ${hasDiscount ? `
                            <p class="font-bold text-lg">£${total.toFixed(2)}</p>
                            <p class="text-xs text-gray-500 line-through">£${originalTotal.toFixed(2)}</p>
                        ` : `
                            <p class="font-bold text-lg">£${total.toFixed(2)}</p>
                        `}
                        <button class="btn-remove text-red-600 hover:text-red-700 text-sm mt-2 font-medium" data-product-id="${item.product_id}" data-variation-id="${item.variation_id}">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            `;
            });

            container.innerHTML = itemsHtml;

            // Attach event listeners
            container.querySelectorAll('.btn-decrease').forEach((btn, idx) => {
                btn.addEventListener('click', (e) => {
                    const input = e.target.closest('div').querySelector('.qty-input');
                    const productId = parseInt(input.dataset.productId);
                    const variationId = parseInt(input.dataset.variationId);
                    const newQty = Math.max(1, parseInt(input.value) - 1);
                    this.updateQuantity(productId, variationId, newQty);
                });
            });

            container.querySelectorAll('.btn-increase').forEach((btn, idx) => {
                btn.addEventListener('click', (e) => {
                    const input = e.target.closest('div').querySelector('.qty-input');
                    const productId = parseInt(input.dataset.productId);
                    const variationId = parseInt(input.dataset.variationId);
                    const newQty = parseInt(input.value) + 1;
                    this.updateQuantity(productId, variationId, newQty);
                });
            });

            container.querySelectorAll('.qty-input').forEach((input) => {
                input.addEventListener('change', (e) => {
                    const productId = parseInt(input.dataset.productId);
                    const variationId = parseInt(input.dataset.variationId);
                    const newQty = Math.max(1, parseInt(input.value));
                    this.updateQuantity(productId, variationId, newQty);
                });
            });

            container.querySelectorAll('.btn-remove').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    const productId = parseInt(btn.dataset.productId);
                    const variationId = parseInt(btn.dataset.variationId);
                    this.removeItem(productId, variationId);
                });
            });
        }

        updateSummary(items) {
            const subtotal = items.reduce((sum, item) => {
                const itemTotal = item.discounted_total || item.total || ((item.price || 0) * (item.quantity || 1));
                return sum + itemTotal;
            }, 0);

            const discounts = items.reduce((sum, item) => {
                const itemDiscount = item.discount_amount || 0;
                return sum + itemDiscount;
            }, 0);
            
            const deliveryFee = subtotal > 0 ? this.deliveryFee : 0;
            const currencySymbol = this.shopConfig?.currency_symbol || '£';
            
            // Calculate VAT if prices DON'T include VAT (need to add VAT on top)
            let vatAmount = 0;
            if (this.shopConfig?.vat?.prices_include_vat === false) {
                const vatRate = parseFloat(this.shopConfig.vat.rate) || 20.00;
                
                // Prices exclude VAT - add VAT on top
                vatAmount = subtotal * (vatRate / 100);
                
                // Update VAT display
                document.getElementById('vat-label').textContent = `VAT (${vatRate}%):`;
                document.getElementById('vat-amount').textContent = currencySymbol + vatAmount.toFixed(2);
                document.getElementById('vat-line').style.display = 'flex';
            } else {
                // VAT already included in prices or not applicable
                document.getElementById('vat-line').style.display = 'none';
            }
            
            // Calculate total (add VAT if prices don't include it)
            const total = subtotal + vatAmount + deliveryFee;

            document.getElementById('subtotal').textContent = currencySymbol + subtotal.toFixed(2);
            document.getElementById('discounts').textContent = '-' + currencySymbol + discounts.toFixed(2);
            document.getElementById('delivery-fee').textContent = currencySymbol + deliveryFee.toFixed(2);
            document.getElementById('total').textContent = currencySymbol + total.toFixed(2);
        }
    }

    // Initialize cart
    const cart = new ShoppingCart();

    // Debug helper - clear cart cache if needed
    window.clearCartCache = function() {
        localStorage.removeItem('cart_validated');
        location.reload();
    };

    // Checkout button
    document.getElementById('checkout-btn').addEventListener('click', () => {
        // Store cart for checkout page
        localStorage.setItem('cart_for_checkout', JSON.stringify(cart.items));
        window.location.href = '/checkout';
    });
</script>
@endsection
