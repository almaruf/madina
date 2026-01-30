<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app(\App\Services\ShopConfigService::class)->name() }} - Home</title>
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
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition">
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

    <script>
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
        
        // Update on page load and when storage changes
        updateCartCount();
        window.addEventListener('storage', updateCartCount);
    </script>
</body>
</html>
