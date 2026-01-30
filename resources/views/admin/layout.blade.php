<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ app(\App\Services\ShopConfigService::class)->name() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.active { transform: translateX(0); }
        }
        
        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        }
        
        .toast {
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            min-width: 300px;
            color: white;
            font-weight: 500;
        }
        
        .toast.success { background: #10b981; }
        .toast.error { background: #ef4444; }
        .toast.warning { background: #f59e0b; }
        .toast.info { background: #3b82f6; }
        
        .toast-icon { font-size: 20px; flex-shrink: 0; }
        .toast-close {
            margin-left: auto;
            cursor: pointer;
            opacity: 0.8;
            font-size: 18px;
            flex-shrink: 0;
        }
        .toast-close:hover { opacity: 1; }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .toast.removing {
            animation: slideOut 0.3s ease-in forwards;
        }
    </style>
</head>
<body class="bg-gray-100">
    <script>
        // Authentication and Axios setup - runs after axios is loaded
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        
        if (!token) {
            window.location.href = '/admin/login';
        } else {
            // Store in localStorage for persistence
            localStorage.setItem('auth_token', token);
            
            // Configure axios globally
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            axios.defaults.headers.common['Accept'] = 'application/json';
            axios.defaults.headers.common['Content-Type'] = 'application/json';
            
            // Request interceptor to ensure token is always sent
            axios.interceptors.request.use(
                config => {
                    const currentToken = localStorage.getItem('auth_token');
                    if (currentToken) {
                        config.headers.Authorization = `Bearer ${currentToken}`;
                    }
                    return config;
                },
                error => Promise.reject(error)
            );
            
            // Set up error interceptor
            axios.interceptors.response.use(
                response => response,
                error => {
                    // Handle 401 Unauthorized errors
                    if (error.response?.status === 401) {
                        console.warn('Token expired or invalid, redirecting to login...');
                        localStorage.removeItem('auth_token');
                        sessionStorage.removeItem('auth_token');
                        // Use replace to prevent back button from returning to protected page
                        window.location.replace('/admin/login');
                        return Promise.reject(error);
                    }
                    
                    // Handle 403 Forbidden errors
                    if (error.response?.status === 403) {
                        console.error('Access forbidden:', error);
                        toast.error('You do not have permission to perform this action');
                        return Promise.reject(error);
                    }
                    
                    return Promise.reject(error);
                }
            );
            
            // Verify token is valid on page load
            axios.get('/api/auth/user')
                .then(() => {
                    console.log('Authentication verified');
                })
                .catch(error => {
                    if (error.response?.status === 401) {
                        console.warn('Invalid token detected on page load');
                        localStorage.removeItem('auth_token');
                        sessionStorage.removeItem('auth_token');
                        window.location.replace('/admin/login');
                    }
                });
        }
    </script>
    
    <!-- Toast Notification Container -->
    <div id="toast-container" class="toast-container"></div>
    
    <!-- Mobile Menu Toggle -->
    <button id="mobile-menu-btn" class="fixed md:hidden bottom-6 right-6 bg-green-600 text-white p-4 rounded-full shadow-lg z-40 hover:bg-green-700">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar fixed md:static w-64 h-screen bg-gray-900 text-white overflow-y-auto z-30 md:z-0 flex flex-col">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-store text-green-500"></i>
                    Admin Panel
                </h1>
                <p class="text-gray-400 text-sm mt-2">{{ app(\App\Services\ShopConfigService::class)->name() }}</p>
            </div>

            <nav class="flex-1 p-4 space-y-2">
                <a href="/admin" class="nav-link flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->is('admin') && !request()->is('admin/*') ? 'bg-green-600' : '' }}">
                    <i class="fas fa-home w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/admin/products" class="nav-link flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->is('admin/products*') ? 'bg-green-600' : '' }}">
                    <i class="fas fa-box w-5"></i>
                    <span>Products</span>
                </a>
                <a href="/admin/orders" class="nav-link flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->is('admin/orders*') ? 'bg-green-600' : '' }}">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span>Orders</span>
                </a>
                <a href="/admin/categories" class="nav-link flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->is('admin/categories*') ? 'bg-green-600' : '' }}">
                    <i class="fas fa-list w-5"></i>
                    <span>Categories</span>
                </a>
                <a href="/admin/users" class="nav-link flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->is('admin/users*') ? 'bg-green-600' : '' }}">
                    <i class="fas fa-users w-5"></i>
                    <span>Users</span>
                </a>
                <a href="/admin/delivery-slots" class="nav-link flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->is('admin/delivery-slots*') ? 'bg-green-600' : '' }}">
                    <i class="fas fa-clock w-5"></i>
                    <span>Delivery Slots</span>
                </a>

                <div class="pt-4 border-t border-gray-700 mt-4">
                    <a href="/admin/shops" class="nav-link flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->is('admin/shops*') ? 'bg-green-600' : '' }}">
                        <i class="fas fa-store-alt w-5"></i>
                        <span>Shops</span>
                    </a>
                    <a href="/admin/admin-users" class="nav-link flex items-center gap-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->is('admin/admin-users*') ? 'bg-green-600' : '' }}">
                        <i class="fas fa-users-cog w-5"></i>
                        <span>Admin Users</span>
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-gray-700">
                <button id="logout-btn" class="w-full flex items-center gap-3 px-4 py-3 bg-red-600 hover:bg-red-700 rounded transition font-semibold">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation Bar -->
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-2 text-gray-700">
                        <i class="fas fa-user-circle text-2xl"></i>
                        <div>
                            <p class="font-semibold text-sm">Admin User</p>
                            <p class="text-xs text-gray-500">Super Admin</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-4 md:p-6">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Toast Notification System
        const toast = {
            success: (message) => showToast(message, 'success'),
            error: (message) => showToast(message, 'error'),
            warning: (message) => showToast(message, 'warning'),
            info: (message) => showToast(message, 'info')
        };
        
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const toastEl = document.createElement('div');
            toastEl.className = `toast ${type}`;
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            toastEl.innerHTML = `
                <i class="fas ${icons[type]} toast-icon"></i>
                <span class="flex-1">${message}</span>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(toastEl);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                removeToast(toastEl);
            }, 4000);
        }
        
        function removeToast(toastEl) {
            toastEl.classList.add('removing');
            setTimeout(() => {
                toastEl.remove();
            }, 300);
        }

        // Mobile menu and navigation handlers
        document.getElementById('mobile-menu-btn').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });

        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    document.getElementById('sidebar').classList.remove('active');
                }
            });
        });

        document.getElementById('logout-btn').addEventListener('click', async () => {
            try {
                await axios.post('/api/admin/logout');
                localStorage.removeItem('auth_token');
                window.location.href = '/admin/login';
            } catch (error) {
                localStorage.removeItem('auth_token');
                window.location.href = '/admin/login';
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
