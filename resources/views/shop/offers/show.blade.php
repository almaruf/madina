@extends('shop.layout')

@section('title', 'Offer - ' . app(\App\Services\ShopConfigService::class)->name())

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Back Button -->
    <a href="/" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 mb-6">
        <i class="fas fa-arrow-left"></i>
        Back to Home
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Left Column: Offer Title and Products -->
        <div class="lg:col-span-3">
            <!-- Offer Header -->
            <h1 class="text-4xl font-bold text-gray-900 mb-8" id="offer-name"></h1>

            <!-- Products Grid -->
            <div>
                <h2 class="text-2xl font-bold mb-6">Products on Sale</h2>
                <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="col-span-full text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
                        <p class="mt-4 text-gray-600">Loading products...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: All Offers -->
        <div class="lg:col-span-1">
            <!-- All Offers Section -->
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">All Offers</h3>
                <div id="all-offers-list" class="space-y-3 max-h-96 overflow-y-auto">
                    <div class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                        <p class="mt-2 text-gray-600 text-sm">Loading offers...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Variation Modal -->
<div id="variation-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold" id="variation-modal-title">Select Variation</h3>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeVariationModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="variation-modal-options" class="space-y-3"></div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Quantity</label>
                <input type="number" id="variation-qty" min="1" value="1" class="w-24 border border-gray-300 rounded-lg px-3 py-2">
            </div>
        </div>
        <div class="px-6 py-4 border-t flex items-center justify-end gap-3">
            <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50" onclick="closeVariationModal()">Cancel</button>
            <button id="variation-add-btn" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700" onclick="confirmVariationAdd()">Add to Cart</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const offerId = new URLSearchParams(window.location.search).get('id') || window.location.pathname.split('/').pop();
    let offerData = null;
    let offerProducts = [];
    let allAvailableOffers = [];
    let currentProduct = null;

    async function loadOffer() {
        try {
            const response = await axios.get(`/api/offers/${offerId}`);
            offerData = response.data.data || response.data;

            // Populate offer title
            document.getElementById('offer-name').textContent = offerData.name;

            // For BXGY offers, show buy products (qualifying products)
            if (offerData.type === 'bxgy_free' || offerData.type === 'bxgy_discount') {
                offerProducts = offerData.buy_products || [];
            } else {
                offerProducts = offerData.products || [];
            }
            renderProducts();
            
            // Load all available offers
            loadAllOffers();
        } catch (error) {
            console.error('Error loading offer:', error);
            document.getElementById('products-grid').innerHTML = '<p class="text-red-600">Failed to load offer</p>';
        }
    }

    async function loadAllOffers() {
        try {
            const response = await axios.get('/api/offers/active');
            allAvailableOffers = response.data || [];
            renderAllOffers();
        } catch (error) {
            console.error('Error loading all offers:', error);
        }
    }

    function getDiscountText(offer) {
        switch (offer.type) {
            case 'percentage_discount':
                return offer.discount_value + '% OFF';
            case 'fixed_discount':
                return '£' + parseFloat(offer.discount_value).toFixed(2) + ' OFF';
            case 'bxgy_free':
                return `Buy ${offer.buy_quantity} Get ${offer.get_quantity} Free`;
            case 'multibuy':
                return `${offer.buy_quantity} for £${parseFloat(offer.bundle_price).toFixed(2)}`;
            case 'bxgy_discount':
                return `Buy ${offer.buy_quantity} Get ${offer.get_quantity} @ ${offer.get_discount_percentage}% OFF`;
            case 'flash_sale':
                return offer.discount_value + '% Flash Sale';
            case 'bundle':
                return 'Bundle Deal';
            default:
                return 'Special Offer';
        }
    }

    function getOfferTerms(offer) {
        const terms = [];
        if (offer.min_purchase_amount) {
            terms.push(`Minimum purchase: £${parseFloat(offer.min_purchase_amount).toFixed(2)}`);
        }
        if (offer.max_uses_per_customer) {
            terms.push(`Max uses per customer: ${offer.max_uses_per_customer}`);
        }
        if (offer.total_usage_limit) {
            terms.push(`Total limit: ${offer.total_usage_limit} uses`);
        }
        return terms.length > 0 ? 'Terms: ' + terms.join(' • ') : '';
    }

    function formatOfferType(type) {
        const types = {
            'percentage_discount': 'Percentage Discount',
            'fixed_discount': 'Fixed Discount',
            'bxgy_free': 'Buy X Get Y Free',
            'multibuy': 'Multi-Buy Deal',
            'bxgy_discount': 'Buy X Get Y at Discount',
            'flash_sale': 'Flash Sale',
            'bundle': 'Bundle Deal'
        };
        return types[type] || type;
    }

    function renderProducts() {
        const grid = document.getElementById('products-grid');
        
        if (!offerProducts || offerProducts.length === 0) {
            grid.innerHTML = '<p class="col-span-full text-center text-gray-600 py-12">No products in this offer</p>';
            return;
        }

        grid.innerHTML = offerProducts.map(product => {
            const imageUrl = product.primary_image?.url || null;
            const defaultVariation = product.variations?.find(v => v.is_default) || product.variations?.[0];
            const originalPrice = defaultVariation?.price ? parseFloat(defaultVariation.price).toFixed(2) : 'N/A';
            
            // Calculate discounted price
            let discountedPrice = originalPrice;
            let showDiscount = false;
            if (originalPrice !== 'N/A' && offerData) {
                if (offerData.type === 'percentage_discount') {
                    discountedPrice = (parseFloat(originalPrice) * (1 - offerData.discount_value / 100)).toFixed(2);
                    showDiscount = true;
                } else if (offerData.type === 'fixed_discount') {
                    discountedPrice = Math.max(0, parseFloat(originalPrice) - parseFloat(offerData.discount_value)).toFixed(2);
                    showDiscount = true;
                }
            }

            // BXGY offer badge
            let offerBadge = '';
            if (offerData.type === 'bxgy_free') {
                offerBadge = `<div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 rounded text-xs font-bold">Buy ${offerData.buy_quantity} Get ${offerData.get_quantity} FREE</div>`;
            } else if (offerData.type === 'bxgy_discount') {
                offerBadge = `<div class="absolute top-2 right-2 bg-orange-600 text-white px-2 py-1 rounded text-xs font-bold">Buy ${offerData.buy_quantity} Get ${offerData.get_quantity} @ ${offerData.get_discount_percentage}% OFF</div>`;
            }

            return `
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                <div class="aspect-square bg-gray-200 overflow-hidden relative">
                    ${imageUrl ? `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover hover:scale-110 transition duration-300">` : '<div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image text-3xl"></i></div>'}
                    ${offerBadge}
                </div>
                <div class="p-3 flex flex-col flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2">${product.name}</h3>
                    <div class="mb-2">
                        ${showDiscount && discountedPrice !== originalPrice && originalPrice !== 'N/A' ? `
                        <div class="flex items-center gap-2">
                            <p class="text-green-600 font-bold text-lg">£${discountedPrice}</p>
                            <p class="text-gray-400 line-through text-sm">£${originalPrice}</p>
                        </div>
                        ` : `<p class="text-green-600 font-bold text-lg">£${originalPrice}</p>`}
                    </div>
                    ${product.variations && product.variations.length > 1 ? 
                        `<button onclick="openVariationModal(${JSON.stringify(product).replace(/"/g, '&quot;')})" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition text-sm">Select & Add</button>` :
                        `<button onclick="quickAddToCart(${product.id}, ${defaultVariation?.id || 'null'})" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition text-sm"><i class="fas fa-shopping-cart mr-1"></i>Add</button>`
                    }
                </div>
            </div>
            `;
        }).join('');
    }

    function renderAllOffers() {
        const list = document.getElementById('all-offers-list');
        
        if (!allAvailableOffers || allAvailableOffers.length === 0) {
            list.innerHTML = '<p class="text-center text-gray-600 py-4">No offers available</p>';
            return;
        }

        list.innerHTML = allAvailableOffers.map(offer => {
            const isActive = parseInt(offerId) === offer.id;
            return `
            <a href="/offers/${offer.id}" class="block p-3 rounded-lg border ${isActive ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:border-green-400'} transition">
                <h4 class="font-semibold text-sm text-gray-900 line-clamp-1">${offer.name}</h4>
                <p class="text-xs text-gray-600 mt-1">${getDiscountText(offer)}</p>
                ${offer.ends_at ? `<p class="text-xs text-gray-500 mt-1">${new Date(offer.ends_at).toLocaleDateString()}</p>` : ''}
            </a>
            `;
        }).join('');
    }

    function openVariationModal(product) {
        currentProduct = product;
        document.getElementById('variation-modal-title').textContent = `Select Variation - ${product.name}`;
        const options = product.variations || [];
        
        const optionsHtml = options.map(variation => {
            const price = parseFloat(variation.price).toFixed(2);
            const stock = variation.stock_quantity ?? null;
            const outOfStock = stock !== null && stock <= 0;
            
            return `
            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 ${outOfStock ? 'opacity-50 cursor-not-allowed' : ''}">
                <input type="radio" name="variation" value="${variation.id}" class="w-4 h-4" ${!outOfStock ? '' : 'disabled'}>
                <span class="ml-3 flex-1">
                    <div class="font-semibold">${variation.name}</div>
                    <div class="text-sm text-gray-600">£${price}</div>
                </span>
                ${outOfStock ? '<span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded">Out of Stock</span>' : ''}
            </label>
            `;
        }).join('');

        document.getElementById('variation-modal-options').innerHTML = optionsHtml;
        document.getElementById('variation-qty').value = 1;
        document.getElementById('variation-modal').classList.remove('hidden');
    }

    function closeVariationModal() {
        document.getElementById('variation-modal').classList.add('hidden');
        currentProduct = null;
    }

    function confirmVariationAdd() {
        const selected = document.querySelector('input[name="variation"]:checked');
        if (!selected) {
            alert('Please select a variation');
            return;
        }

        const qty = parseInt(document.getElementById('variation-qty').value) || 1;
        quickAddToCart(currentProduct.id, selected.value, qty);
        closeVariationModal();
    }

    function quickAddToCart(productId, variationId, quantity = 1) {
        const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
        const existing = cart.find(item => item.product_id === productId && item.variation_id === variationId);

        if (existing) {
            existing.quantity += quantity;
            // Update offer info when adding from an offer page
            if (offerData && offerData.type) {
                existing.offer_id = offerData.id;
                existing.offer_type = offerData.type;
            }
        } else {
            const cartItem = { product_id: productId, variation_id: variationId, quantity };
            
            // Add offer info for any offer type
            if (offerData && offerData.type) {
                cartItem.offer_id = offerData.id;
                cartItem.offer_type = offerData.type;
            }
            
            cart.push(cartItem);
        }

        localStorage.setItem('shopping_cart', JSON.stringify(cart));
        showCartMessage(`Added to cart!`);
        updateCartCount();
    }

    function showCartMessage(message) {
        // Create a temporary notification that appears and fades out
        const notification = document.createElement('div');
        notification.className = 'fixed top-20 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-pulse';
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove after 2 seconds
        setTimeout(() => {
            notification.remove();
        }, 2000);
    }

    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
        const count = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
        const badge = document.getElementById('cart-count');
        if (badge) {
            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
        }
    }

    loadOffer();
    updateCartCount();
</script>
@endsection
