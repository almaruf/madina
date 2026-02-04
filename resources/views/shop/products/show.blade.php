<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - {{ app(\App\Services\ShopConfigService::class)->name() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header Navigation -->
    <header class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <i class="fas fa-store text-green-600 text-2xl"></i>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ app(\App\Services\ShopConfigService::class)->name() }}
                </h1>
            </a>
            
            <a href="/cart" class="relative p-2 text-gray-600 hover:text-green-600 transition" title="Shopping Cart">
                <i class="fas fa-shopping-cart text-2xl"></i>
                <span id="cart-count" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center hidden">
                    0
                </span>
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Product Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <!-- Image Gallery -->
            <div x-data="{ currentImage: 0, images: {{ $product->images->count() }} }">
                <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden mb-4">
                    @if($product->images->count() > 0)
                        @foreach($product->images as $index => $image)
                        <img x-show="currentImage === {{ $index }}" 
                             src="{{ $image->signed_url ?? $image->url }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover">
                        @endforeach
                    @elseif($product->primaryImage)
                        <img src="{{ $product->primaryImage->signed_url ?? $product->primaryImage->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @endif
                </div>

                <!-- Thumbnail Gallery -->
                @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $index => $image)
                    <button @click="currentImage = {{ $index }}" 
                            :class="currentImage === {{ $index }} ? 'ring-2 ring-green-600' : ''"
                            class="aspect-square bg-gray-200 rounded overflow-hidden hover:opacity-75 transition">
                        <img src="{{ $image->signed_thumbnail_url ?? $image->thumbnail_url ?? $image->signed_url }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <div class="mb-4">
                    @foreach($product->categories as $category)
                    <span class="inline-block bg-gray-200 text-gray-700 text-sm px-3 py-1 rounded-full mr-2">
                        {{ $category->name }}
                    </span>
                    @endforeach
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

                @if($product->description)
                <p class="text-gray-600 mb-6">{{ $product->description }}</p>
                @endif

                <!-- Price & Variations -->
                <div x-data="{ selectedVariation: {{ $product->variations->first()->id ?? 0 }} }" class="mb-6">
                    @if($product->variations->count() > 0)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Size:</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->variations as $variation)
                            <button @click="document.querySelectorAll('button[data-variation]').forEach(b => b.removeAttribute('data-active')); this.setAttribute('data-active', 'true')" 
                                    data-variation="{{ $variation->id }}"
                                    :class="this.hasAttribute('data-active') ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                    class="px-4 py-2 border-2 border-gray-300 rounded-lg font-medium transition @if($loop->first)bg-green-600 text-white data-active @endif"
                                    @if($loop->first)data-active="true"@endif>
                                {{ $variation->name }}
                                <span class="text-sm">- £{{ number_format($variation->price, 2) }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-4xl font-bold text-green-600 mb-6" id="price-display">
                        £{{ number_format($product->variations->first()->price ?? 0, 2) }}
                    </div>
                    @endif

                    <!-- Add to Cart -->
                    <div x-data="{ quantity: 1 }" class="flex items-center space-x-4 mb-6">
                        <div class="flex items-center border-2 border-gray-300 rounded-lg overflow-hidden">
                            <button @click="quantity = Math.max(1, quantity - 1)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 transition">-</button>
                            <input type="number" x-model.number="quantity" min="1" class="w-16 text-center border-none focus:outline-none">
                            <button @click="quantity++" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 transition">+</button>
                        </div>
                        <button @click="addToCart({{ $product->id }}, document.querySelector('button[data-variation]').getAttribute('data-variation'), quantity)" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg transition">
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Additional Info -->
                @if($product->is_halal)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <p class="text-green-800 font-semibold">✓ Halal Certified</p>
                </div>
                @endif

                @if($product->type === 'meat')
                <div class="text-sm text-gray-600">
                    @if($product->meat_type)
                    <p><strong>Type:</strong> {{ ucfirst($product->meat_type) }}</p>
                    @endif
                    @if($product->cut_type)
                    <p><strong>Cut:</strong> {{ ucfirst($product->cut_type) }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <section>
            <h2 class="text-2xl font-bold mb-6">Related Products</h2>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                @foreach($relatedProducts as $related)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    <a href="{{ route('shop.products.show', $related->slug) }}" class="block">
                        <div class="aspect-square bg-gray-200 rounded-t-lg overflow-hidden">
                            @if($related->primaryImage)
                            <img src="{{ $related->primaryImage->url }}" alt="{{ $related->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="font-semibold text-sm text-gray-900 mb-1 line-clamp-2">{{ $related->name }}</h3>
                            @if($related->variations->count() > 0)
                            <p class="text-base font-bold text-green-600">
                                £{{ number_format($related->variations->first()->price, 2) }}
                            </p>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} {{ app(\App\Services\ShopConfigService::class)->name() }}. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Variation data
        const variations = {!! $product->variations->map(fn($v) => ['id' => $v->id, 'price' => $v->price])->toJson() !!};
        
        // Update cart count
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
        
        // Update price based on selected variation
        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('button[data-variation]');
            buttons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    // Remove active state from all buttons
                    buttons.forEach(b => {
                        b.classList.remove('bg-green-600', 'text-white');
                        b.classList.add('bg-white', 'text-gray-700');
                        b.removeAttribute('data-active');
                    });
                    
                    // Add active state to clicked button
                    e.target.closest('button[data-variation]').classList.remove('bg-white', 'text-gray-700');
                    e.target.closest('button[data-variation]').classList.add('bg-green-600', 'text-white');
                    e.target.closest('button[data-variation]').setAttribute('data-active', 'true');
                    
                    // Update price
                    const variationId = parseInt(e.target.closest('button[data-variation]').getAttribute('data-variation'));
                    const variation = variations.find(v => v.id === variationId);
                    if (variation) {
                        document.getElementById('price-display').textContent = '£' + variation.price.toFixed(2);
                    }
                });
            });
        });
        
        function addToCart(productId, variationId, quantity) {
            // Get existing cart
            const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            
            // Ensure variationId is a number
            variationId = parseInt(variationId);
            quantity = parseInt(quantity);
            
            // Find if product already in cart
            const existing = cart.find(item => item.product_id === productId && item.variation_id === variationId);
            
            if (existing) {
                existing.quantity += quantity;
            } else {
                cart.push({
                    product_id: productId,
                    variation_id: variationId,
                    quantity: quantity
                });
            }
            
            // Save cart
            localStorage.setItem('shopping_cart', JSON.stringify(cart));
            
            // Update cart count
            updateCartCount();
            
            // Show notification
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = '✓ Added to Cart!';
            button.classList.add('bg-green-700');
            
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('bg-green-700');
            }, 2000);
        }
        
        // Update on page load and when storage changes
        updateCartCount();
        window.addEventListener('storage', updateCartCount);
    </script>
</body>
</html>
