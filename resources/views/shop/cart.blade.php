@extends('shop.layout')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <a href="/" class="text-green-600 hover:text-green-700 mb-4 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to Shopping
        </a>
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
            this.render();
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
                });

                // Store validated items with details for later use
                localStorage.setItem('cart_validated', JSON.stringify(response.data.items));
                
                this.renderItems(response.data.items);
                this.updateSummary(response.data.items);
                document.getElementById('checkout-btn').disabled = false;
            } catch (error) {
                console.error('Cart validation error:', error);
                
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

            const itemsHtml = items.map(item => {
                const quantity = item.quantity || 1;
                const price = item.price || 0;
                const total = item.total || (price * quantity);
                
                return `
                <div class="flex gap-4 pb-4 mb-4 border-b border-gray-200">
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
                        <p class="text-green-600 font-bold mt-1">£${price.toFixed(2)}</p>
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center gap-2">
                        <button onclick="cart.updateQuantity(${item.product_id}, ${item.variation_id}, ${quantity - 1})" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300 transition">−</button>
                        <input type="number" min="1" value="${quantity}" onchange="cart.updateQuantity(${item.product_id}, ${item.variation_id}, this.value)" class="w-12 text-center border border-gray-300 rounded px-2 py-1">
                        <button onclick="cart.updateQuantity(${item.product_id}, ${item.variation_id}, ${quantity + 1})" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300 transition">+</button>
                    </div>

                    <!-- Item Total -->
                    <div class="text-right">
                        <p class="font-bold text-lg">£${total.toFixed(2)}</p>
                        <button onclick="cart.removeItem(${item.product_id}, ${item.variation_id})" class="text-red-600 hover:text-red-700 text-sm mt-2 font-medium">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            `;
            }).join('');

            container.innerHTML = itemsHtml || '<p class="text-gray-600">Your cart is empty</p>';
        }

        updateSummary(items) {
            const subtotal = items.reduce((sum, item) => {
                const itemTotal = item.total || ((item.price || 0) * (item.quantity || 1));
                return sum + itemTotal;
            }, 0);
            
            const deliveryFee = subtotal > 0 ? this.deliveryFee : 0;
            const total = subtotal + deliveryFee;

            document.getElementById('subtotal').textContent = '£' + subtotal.toFixed(2);
            document.getElementById('delivery-fee').textContent = '£' + deliveryFee.toFixed(2);
            document.getElementById('total').textContent = '£' + total.toFixed(2);
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
