<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ app(\App\Services\ShopConfigService::class)->name() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">
    <script>
        // Check authentication on page load
        let token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        console.log('Checking token on dashboard load...');
        console.log('Token found:', token ? 'YES' : 'NO');
        
        if (!token) {
            console.log('No token found, redirecting to login...');
            window.location.href = '/admin/login';
        } else {
            console.log('Token found, setting up axios...');
            // Store in localStorage for persistence
            localStorage.setItem('auth_token', token);
            
            // Set authorization header
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            axios.defaults.headers.common['X-XSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            // Redirect to login if unauthorized
            axios.interceptors.response.use(
                response => response,
                error => {
                    if (error.response?.status === 401 || error.response?.status === 403) {
                        console.error('Unauthorized, redirecting to login...');
                        localStorage.removeItem('auth_token');
                        sessionStorage.removeItem('auth_token');
                        window.location.href = '/admin/login';
                    }
                    return Promise.reject(error);
                }
            );
        }
    </script>
    
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">{{ app(\App\Services\ShopConfigService::class)->name() }} - Admin</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/admin/orders" class="hover:underline">Orders</a>
                    <a href="/admin/products" class="hover:underline">Products</a>
                    <a href="/admin/delivery-slots" class="hover:underline">Delivery Slots</a>
                    <button id="logout-btn" class="bg-green-700 px-4 py-2 rounded hover:bg-green-800">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-3xl font-bold mb-8">Dashboard</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Total Orders</p>
                <p class="text-3xl font-bold text-green-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Pending Orders</p>
                <p class="text-3xl font-bold text-orange-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Today's Orders</p>
                <p class="text-3xl font-bold text-blue-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Total Revenue</p>
                <p class="text-3xl font-bold text-purple-600">£0.00</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Recent Orders</h3>
            <p class="text-gray-600">No orders yet</p>
        </div>
    </main>
    
    <script>
        document.getElementById('logout-btn').addEventListener('click', async () => {
            try {
                await axios.post('/api/admin/logout');
            } catch (error) {
                // Ignore logout errors
            } finally {
                localStorage.removeItem('auth_token');
                window.location.href = '/admin/login';
            }
        });
    </script>
</body>
</html>
