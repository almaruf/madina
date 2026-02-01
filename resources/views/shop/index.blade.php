<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app(\App\Services\ShopConfigService::class)->name() }} - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header Navigation -->
    <nav class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <i class="fas fa-store text-green-600 text-xl"></i>
                    <a href="/" class="text-2xl font-bold text-green-600 hover:text-green-700">{{ app(\App\Services\ShopConfigService::class)->name() }}</a>
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="/" class="text-gray-700 hover:text-green-600 font-medium">Home</a>
                    <a href="/shop/products" class="text-gray-700 hover:text-green-600 font-medium">Shop</a>
                    <a href="/account" class="text-gray-700 hover:text-green-600 font-medium">Account</a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="/cart" class="relative text-gray-700 hover:text-green-600 transition p-2 flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center hidden">
                            0
                        </span>
                    </a>
                    <button id="logout-btn" class="hidden text-gray-700 hover:text-red-600 font-medium transition" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Banners Section -->
        @if($banners->count() > 0)
        <section class="mb-12">
            <div class="relative overflow-hidden rounded-lg shadow-lg" x-data="{ currentSlide: 0, slides: {{ $banners->count() }} }">
                @foreach($banners as $index => $banner)
                <div x-show="currentSlide === {{ $index }}" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-full"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     class="relative h-96">
                    <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center">
                        <div class="p-8 text-white max-w-2xl">
                            @if($banner->title)
                            <h2 class="text-4xl font-bold mb-4">{{ $banner->title }}</h2>
                            @endif                                    
                            @if($banner->description)
                            <p class="text-lg mb-6">{{ $banner->description }}</p>
                            @endif
                            @if($banner->link)
                            <a href="{{ $banner->link }}" class="bg-white text-gray-900 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                                Shop Now
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if($banners->count() > 1)
                <!-- Navigation Buttons -->
                <button @click="currentSlide = (currentSlide - 1 + slides) % slides" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white p-2 rounded-full shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button @click="currentSlide = (currentSlide + 1) % slides"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white p-2 rounded-full shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <!-- Indicators -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                    @foreach($banners as $index => $banner)
                    <button @click="currentSlide = {{ $index }}" 
                            :class="currentSlide === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                            class="w-3 h-3 rounded-full transition"></button>
                    @endforeach
                </div>
                @endif
            </div>
        </section>
        @endif

        <!-- Offers Section -->
        @if($activeOffers && $activeOffers->count() > 0)
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                <i class="fas fa-tags text-red-600"></i>
                Special Offers
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($activeOffers as $offer)
                    @foreach($offer->products->take(4) as $product)
                    <div class="bg-white rounded-lg shadow hover:shadow-xl transition relative overflow-hidden">
                        <!-- Offer Badge -->
                        @if($offer->badge_text)
                        <div class="absolute top-2 right-2 z-10 px-3 py-1 rounded-full text-white text-xs font-bold shadow-lg" 
                             style="background-color: {{ $offer->badge_color ?? '#DC2626' }};">
                            {{ $offer->badge_text }}
                        </div>
                        @endif
                        
                        <a href="/products/{{ $product->slug }}" class="block">
                            <div class="aspect-square bg-gray-200 rounded-t-lg overflow-hidden">
                                @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover hover:scale-110 transition duration-300">
                                @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                                
                                @if($product->default_variation)
                                <div class="mb-3">
                                    @php
                                        $originalPrice = $product->default_variation->price;
                                        $discountedPrice = $offer->getDiscountedPrice($originalPrice, 1);
                                    @endphp
                                    
                                    @if($discountedPrice < $originalPrice)
                                    <div class="flex items-center gap-2">
                                        <p class="text-xl font-bold text-red-600">
                                            £{{ number_format($discountedPrice, 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500 line-through">
                                            £{{ number_format($originalPrice, 2) }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-green-600 font-semibold mt-1">
                                        Save £{{ number_format($originalPrice - $discountedPrice, 2) }}
                                    </p>
                                    @else
                                    <p class="text-xl font-bold text-green-600">
                                        £{{ number_format($originalPrice, 2) }}
                                    </p>
                                    @endif
                                </div>
                                @endif
                                
                                <!-- Offer Description -->
                                @if($offer->description)
                                <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $offer->description }}</p>
                                @endif
                            </div>
                        </a>
                        
                        <div class="px-4 pb-4">
                            <button onclick="addToCartFromCard({{ $product->id }}, this)" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
            
            <!-- View All Offers Button -->
            <div class="text-center mt-8">
                <a href="/shop/products?offers=1" class="inline-block bg-white border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white font-bold px-8 py-3 rounded-lg transition shadow-md hover:shadow-lg">
                    View All Offers
                </a>
            </div>
        </section>
        @endif

        <!-- Featured Categories -->
        @if($featuredCategories->count() > 0)
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6">Featured Categories</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($featuredCategories as $category)
                <a href="/products?category={{ $category->slug }}" 
                   class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                    <div class="aspect-video bg-gray-200">
                        @if($category->image)
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        @endif
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                        <div class="text-white">
                            <h3 class="text-xl font-bold mb-1">{{ $category->name }}</h3>
                            @if($category->description)
                            <p class="text-sm text-gray-200">{{ Str::limit($category->description, 60) }}</p>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @if($otherCategories->count() > 0)
            <div class="mt-6 flex flex-wrap gap-3">
                @foreach($otherCategories as $cat)
                    <a href="/shop/products?category={{ $cat->slug }}" class="inline-block bg-white border border-green-600 text-green-700 hover:bg-green-600 hover:text-white font-semibold px-5 py-2 rounded-lg shadow-sm transition">{{ $cat->name }}</a>
                @endforeach
            </div>
            @endif
        </section>
        @endif

        <!-- Featured Products -->
        @if($featuredProducts->count() > 0)
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6">Featured Products</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    <a href="/products/{{ $product->slug }}" class="block">
                        <div class="aspect-square bg-gray-200 rounded-t-lg overflow-hidden">
                            @if($product->primaryImage)
                            <img src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                            @if($product->variations->count() > 0)
                            <p class="text-lg font-bold text-green-600">
                                £{{ number_format($product->variations->first()->price, 2) }}
                            </p>
                            @endif
                        </div>
                    </a>
                    <div class="px-4 pb-4">
                        <button onclick="addToCartFromCard({{ $product->id }}, this)" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition">
                            Add to Cart
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Popular Products (Most Bought) -->
        @if($popularProducts->count() > 0)
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6">Most Popular</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($popularProducts as $product)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    <a href="/products/{{ $product->slug }}" class="block">
                        <div class="aspect-square bg-gray-200 rounded-t-lg overflow-hidden">
                            @if($product->primaryImage)
                            <img src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="font-semibold text-sm text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                            @if($product->variations->count() > 0)
                            <p class="text-base font-bold text-green-600">
                                £{{ number_format($product->variations->first()->price, 2) }}
                            </p>
                            <div class="mt-2">
                                <button class="add-to-cart-btn bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded w-full"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-has-variations="{{ $product->variations->count() > 1 ? '1' : '0' }}"
                                    data-variations='@json($product->variations->map(fn($v) => ["id"=>$v->id,"name"=>$v->name,"price"=>$v->price]))'>
                                    Add to Cart
                                </button>
                            </div>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </section>
        @endif

    <!-- Variation Modal (copied from products.blade.php) -->
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
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} {{ app(\App\Services\ShopConfigService::class)->name() }}. All rights reserved.</p>
        </div>
    </footer>

    <script>
                // Add to Cart logic for Most Popular section
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            const productId = parseInt(this.dataset.productId);
                            const hasVariations = this.dataset.hasVariations === '1';
                            const variations = JSON.parse(this.dataset.variations);
                            if (hasVariations) {
                                // Show modal for variation selection
                                openVariationModal(productId, variations);
                            } else {
                                // Add first variation to cart
                                const variationId = variations[0].id;
                                addToCart(productId, variationId, 1);
                            }
                        });
                    });
                });

                function openVariationModal(productId, variations) {
                    const modal = document.getElementById('variation-modal');
                    const optionsDiv = document.getElementById('variation-modal-options');
                    optionsDiv.innerHTML = '';
                    variations.forEach(v => {
                        const btn = document.createElement('button');
                        btn.className = 'w-full text-left px-4 py-3 border rounded mb-2 flex justify-between items-center bg-white hover:bg-green-50';
                        btn.textContent = v.name + ' - £' + parseFloat(v.price).toFixed(2);
                        btn.onclick = function() {
                            document.getElementById('variation-add-btn').dataset.productId = productId;
                            document.getElementById('variation-add-btn').dataset.variationId = v.id;
                            document.getElementById('variation-qty').value = 1;
                        };
                        optionsDiv.appendChild(btn);
                    });
                    // Default to first variation
                    if (variations.length > 0) {
                        document.getElementById('variation-add-btn').dataset.productId = productId;
                        document.getElementById('variation-add-btn').dataset.variationId = variations[0].id;
                        document.getElementById('variation-qty').value = 1;
                    }
                    modal.classList.remove('hidden');
                }

                function closeVariationModal() {
                    document.getElementById('variation-modal').classList.add('hidden');
                }

                function confirmVariationAdd() {
                    const productId = parseInt(document.getElementById('variation-add-btn').dataset.productId);
                    const variationId = parseInt(document.getElementById('variation-add-btn').dataset.variationId);
                    const qty = parseInt(document.getElementById('variation-qty').value) || 1;
                    addToCart(productId, variationId, qty);
                    closeVariationModal();
                }

                function addToCart(productId, variationId, quantity) {
                    const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
                    const existing = cart.find(item => item.product_id === productId && item.variation_id === variationId);
                    if (existing) {
                        existing.quantity += quantity;
                    } else {
                        cart.push({ product_id: productId, variation_id: variationId, quantity });
                    }
                    localStorage.setItem('shopping_cart', JSON.stringify(cart));
                    updateCartCount();
                }
        // --- Cart Count ---
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.getElementById('cart-count');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
        updateCartCount();
        window.addEventListener('storage', updateCartCount);

        // --- Add to Cart Modal Logic (from products.blade.php) ---
        let featuredProducts = @json($featuredProducts);
        let currentProduct = null;

        function addToCartFromCard(productId, btn) {
            const product = featuredProducts.find(item => item.id === productId);
            if (!product) return;
            const variations = product.variations || [];
            if (variations.length === 1) {
                addToCart(product.id, variations[0].id, 1, btn);
                return;
            }
            currentProduct = product;
            openVariationModal(product);
        }

        function openVariationModal(product) {
            document.getElementById('variation-modal-title').textContent = `Select Variation - ${product.name}`;
            const options = product.variations || [];
            const optionsHtml = options.map((variation, index) => {
                const price = parseFloat(variation.price).toFixed(2);
                const stock = variation.stock_quantity ?? variation.stock ?? null;
                const outOfStock = stock !== null && stock <= 0;
                return `
                    <label class="flex items-start p-3 border border-gray-300 rounded-lg ${outOfStock ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-green-50'}">
                        <input type="radio" name="variation_id" value="${variation.id}" class="mt-1 w-4 h-4" ${index === 0 && !outOfStock ? 'checked' : ''} ${outOfStock ? 'disabled' : ''}>
                        <div class="ml-3 flex-1">
                            <div class="font-semibold">${variation.name || 'Standard'}</div>
                            <div class="text-sm text-gray-600">£${price}</div>
                            ${stock !== null ? `<div class="text-xs text-gray-500">${stock} in stock</div>` : ''}
                        </div>
                    </label>
                `;
            }).join('');
            document.getElementById('variation-modal-options').innerHTML = optionsHtml || '<p class="text-gray-600">No variations available.</p>';
            document.getElementById('variation-qty').value = 1;
            document.getElementById('variation-modal').classList.remove('hidden');
            const hasAvailable = options.some(v => {
                const stock = v.stock_quantity ?? v.stock ?? null;
                return stock === null || stock > 0;
            });
            document.getElementById('variation-add-btn').disabled = !hasAvailable;
            document.getElementById('variation-add-btn').classList.toggle('opacity-50', !hasAvailable);
        }

        function closeVariationModal() {
            document.getElementById('variation-modal').classList.add('hidden');
            currentProduct = null;
        }

        function confirmVariationAdd() {
            if (!currentProduct) return;
            const selected = document.querySelector('input[name="variation_id"]:checked');
            if (!selected) return;
            const qty = Math.max(1, parseInt(document.getElementById('variation-qty').value || '1'));
            addToCart(currentProduct.id, parseInt(selected.value), qty);
            closeVariationModal();
        }

        function addToCart(productId, variationId, quantity = 1, btn = null) {
            const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            variationId = parseInt(variationId);
            quantity = parseInt(quantity);
            const existing = cart.find(item => item.product_id === productId && item.variation_id === variationId);
            if (existing) {
                existing.quantity += quantity;
            } else {
                cart.push({ product_id: productId, variation_id: variationId, quantity });
            }
            localStorage.setItem('shopping_cart', JSON.stringify(cart));
            updateCartCount();
            if (btn) {
                const originalText = btn.textContent;
                btn.textContent = '✓ Added!';
                btn.classList.add('bg-green-700');
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.classList.remove('bg-green-700');
                }, 1500);
            }
        }
    </script>

    <script>
        // Update cart count in navigation
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.getElementById('cart-count');
            
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
        
        // Check auth status
        function checkAuthStatus() {
            const token = localStorage.getItem('auth_token');
            const logoutBtn = document.getElementById('logout-btn');
            
            if (token) {
                logoutBtn.classList.remove('hidden');
            } else {
                logoutBtn.classList.add('hidden');
            }
        }
        
        // Logout function
        document.getElementById('logout-btn').addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                const token = localStorage.getItem('auth_token');
                await axios.post('/api/auth/logout', {}, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
            } catch (error) {
                console.error('Logout error:', error);
            }
            localStorage.removeItem('auth_token');
            localStorage.removeItem('shopping_cart');
            checkAuthStatus();
            window.location.href = '/';
        });
        
        // Update on page load and when storage changes
        updateCartCount();
        checkAuthStatus();
        window.addEventListener('storage', () => {
            updateCartCount();
            checkAuthStatus();
        });
    </script>
</html>
