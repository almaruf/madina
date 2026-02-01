<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', app(\App\Services\ShopConfigService::class)->name())</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <i class="fas fa-store text-green-600 text-xl"></i>
                    <a href="/" class="text-2xl font-bold text-green-600 hover:text-green-700">{{ app(\App\Services\ShopConfigService::class)->name() }}</a>
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="/" class="text-gray-700 hover:text-green-600 font-medium">Home</a>
                    <a href="/products" class="text-gray-700 hover:text-green-600 font-medium">Shop</a>
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

    @yield('content')

    <footer class="bg-gray-800 text-white mt-16 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="font-bold mb-4">{{ app(\App\Services\ShopConfigService::class)->name() }}</h3>
                    <p class="text-gray-400">{{ app(\App\Services\ShopConfigService::class)->fullAddress() }}</p>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Contact</h3>
                    <p class="text-gray-400"><i class="fas fa-phone"></i> {{ app(\App\Services\ShopConfigService::class)->phone() }}</p>
                    <p class="text-gray-400"><i class="fas fa-envelope"></i> {{ app(\App\Services\ShopConfigService::class)->email() }}</p>
                </div>
                <div>
                    <h3 class="font-bold mb-4">Quick Links</h3>
                    <ul class="text-gray-400 space-y-2">
                        <li><a href="/" class="hover:text-white">Home</a></li>
                        <li><a href="/cart" class="hover:text-white">Cart</a></li>
                        <li><a href="/account" class="hover:text-white">Account</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ app(\App\Services\ShopConfigService::class)->name() }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

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
            
            if (token && logoutBtn) {
                logoutBtn.classList.remove('hidden');
            } else if (logoutBtn) {
                logoutBtn.classList.add('hidden');
            }
        }
        
        // Logout function
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async (e) => {
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
        }
        
        // Update on page load and when storage changes
        updateCartCount();
        checkAuthStatus();
        window.addEventListener('storage', () => {
            updateCartCount();
            checkAuthStatus();
        });
    </script>
</body>
</html>
