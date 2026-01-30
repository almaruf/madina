<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app(\App\Services\ShopConfigService::class)->name() }} - Online Grocery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-green-600">{{ app(\App\Services\ShopConfigService::class)->name() }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/shop/products" class="text-gray-700 hover:text-green-600 font-medium">Shop</a>
                    <a href="/admin/login" class="text-gray-700 hover:text-green-600 font-medium">Admin</a>
                    <a href="/shop/login" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 font-medium">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <div class="relative bg-gradient-to-r from-green-600 to-blue-600 text-white">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <h2 class="text-5xl font-bold mb-4">
                Welcome to {{ app(\App\Services\ShopConfigService::class)->name() }}
            </h2>
            <p class="text-2xl mb-8 text-gray-100">
                {{ app(\App\Services\ShopConfigService::class)->description() }}
            </p>
            <a href="/shop/products" class="inline-block bg-white text-green-600 px-8 py-4 rounded-lg text-lg font-bold hover:bg-gray-100 transition shadow-lg">
                Start Shopping Now
            </a>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Featured Categories -->
        <section class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Shop by Category</h2>
                <a href="/shop/products" class="text-green-600 hover:text-green-700 font-semibold">View All →</a>
            </div>
            <div id="featured-categories" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="animate-pulse bg-gray-200 h-48 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-48 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-48 rounded-lg"></div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Featured Products</h2>
                <a href="/shop/products" class="text-green-600 hover:text-green-700 font-semibold">View All →</a>
            </div>
            <div id="featured-products" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <!-- Loading placeholders -->
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
            </div>
        </section>

        <!-- Most Bought Products -->
        <section class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Popular Products</h2>
                <a href="/shop/products" class="text-green-600 hover:text-green-700 font-semibold">View All →</a>
            </div>
            <div id="popular-products" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Loading placeholders -->
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
                <div class="animate-pulse bg-gray-200 h-64 rounded-lg"></div>
            </div>
        </section>

        <!-- Features Grid -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="text-green-600 text-4xl mb-4">🛒</div>
                <h3 class="text-xl font-bold mb-2">Easy Shopping</h3>
                <p class="text-gray-600">Browse our extensive range of fresh groceries and everyday essentials with an intuitive interface.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="text-green-600 text-4xl mb-4">🚚</div>
                <h3 class="text-xl font-bold mb-2">Fast Delivery</h3>
                <p class="text-gray-600">Choose your preferred delivery or collection time slot that suits your schedule perfectly.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="text-green-600 text-4xl mb-4">✅</div>
                <h3 class="text-xl font-bold mb-2">Quality Products</h3>
                <p class="text-gray-600">All our products are sourced from trusted suppliers and meet high quality standards.</p>
            </div>
        </div>

        <div class="mt-16 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-8 text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Shop Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <p class="text-gray-600 mb-2"><strong>📍 Location:</strong></p>
                    <p class="text-gray-900">{{ app(\App\Services\ShopConfigService::class)->fullAddress() }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-2"><strong>📞 Contact:</strong></p>
                    <p class="text-gray-900">{{ app(\App\Services\ShopConfigService::class)->phone() }}</p>
                    <p class="text-gray-900">{{ app(\App\Services\ShopConfigService::class)->email() }}</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white mt-16 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="font-bold mb-4">{{ app(\App\Services\ShopConfigService::class)->name() }}</h3>
                    <p class="text-gray-400">{{ app(\App\Services\ShopConfigService::class)->fullAddress() }}</p>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Quick Links</h3>
                    <ul class="text-gray-400 space-y-2">
                        <li><a href="/shop/products" class="hover:text-white transition">Shop</a></li>
                        <li><a href="/shop/login" class="hover:text-white transition">My Account</a></li>
                        <li><a href="/admin/login" class="hover:text-white transition">Admin</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Contact</h3>
                    <p class="text-gray-400">📞 {{ app(\App\Services\ShopConfigService::class)->phone() }}</p>
                    <p class="text-gray-400">📧 {{ app(\App\Services\ShopConfigService::class)->email() }}</p>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-8 text-center text-gray-400">
                <p>&copy; 2026 {{ app(\App\Services\ShopConfigService::class)->name() }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        async function loadFeaturedCategories() {
            try {
                const response = await axios.get('/api/categories?featured=1&limit=3');
                console.log('Categories API response:', response.data);
                const categories = response.data.data || response.data;
                console.log('Categories array:', categories);
                renderFeaturedCategories(categories);
            } catch (error) {
                console.error('Failed to load categories:', error);
                document.getElementById('featured-categories').innerHTML = '<p class="text-red-600 col-span-3 text-center">Error loading categories</p>';
            }
        }

        function renderFeaturedCategories(categories) {
            const container = document.getElementById('featured-categories');
            if (categories.length === 0) {
                container.innerHTML = '<p class="text-gray-600 col-span-3 text-center">No featured categories</p>';
                return;
            }

            container.innerHTML = categories.map(cat => `
                <a href="/shop/products?category=${cat.slug}" class="block group">
                    <div class="relative h-48 bg-gradient-to-br from-green-400 to-blue-500 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition">
                        ${cat.image_url ? `<img src="${cat.image_url}" class="w-full h-full object-cover" alt="${cat.name}">` : ''}
                        <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-30 transition flex items-center justify-center">
                            <div class="text-center text-white">
                                <h3 class="text-2xl font-bold mb-2">${cat.name}</h3>
                                <p class="text-sm">${cat.description || 'Explore now'}</p>
                            </div>
                        </div>
                    </div>
                </a>
            `).join('');
        }

        async function loadFeaturedProducts() {
            try {
                const response = await axios.get('/api/products?featured=1&limit=12');
                console.log('Featured products API response:', response.data);
                const products = response.data.data || response.data;
                console.log('Featured products array:', products);
                renderProducts('featured-products', products);
            } catch (error) {
                console.error('Failed to load featured products:', error);
                document.getElementById('featured-products').innerHTML = '<p class="text-red-600 col-span-full text-center">Error loading products</p>';
            }
        }

        async function loadPopularProducts() {
            try {
                const response = await axios.get('/api/products?popular=1&limit=15');
                console.log('Popular products API response:', response.data);
                const products = response.data.data || response.data;
                console.log('Popular products array:', products);
                renderProducts('popular-products', products);
            } catch (error) {
                console.error('Failed to load popular products:', error);
                document.getElementById('popular-products').innerHTML = '<p class="text-red-600 col-span-full text-center">Error loading products</p>';
            }
        }

        function renderProducts(containerId, products) {
            const container = document.getElementById(containerId);
            if (products.length === 0) {
                container.innerHTML = '<p class="text-gray-600 col-span-full text-center">No products available</p>';
                return;
            }

            container.innerHTML = products.map(product => {
                const variation = product.variations && product.variations[0];
                const price = variation ? variation.price : 'N/A';
                const image = product.primary_image || product.primaryImage || (product.images && product.images[0]) || null;

                return `
                    <a href="/shop/products/${product.slug}" class="block group">
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden h-full">
                            <div class="relative h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                                ${image && image.url ? 
                                    `<img src="${image.url}" alt="${product.name}" class="w-full h-full object-cover group-hover:scale-110 transition">` :
                                    `<div class="text-gray-400 text-4xl">📦</div>`
                                }
                                ${product.is_on_sale ? '<span class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded">SALE</span>' : ''}
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-sm line-clamp-2 mb-2 group-hover:text-green-600 transition">${product.name}</h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-green-600 font-bold text-lg">£${parseFloat(price).toFixed(2)}</span>
                                    <button class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                `;
            }).join('');
        }

        loadFeaturedCategories();
        loadFeaturedProducts();
        loadPopularProducts();
    </script>
</body>
</html>
