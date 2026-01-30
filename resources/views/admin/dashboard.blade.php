@extends('admin.layout')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Total Orders</p>
        <p id="total-orders" class="text-3xl font-bold text-green-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Pending Orders</p>
        <p id="pending-orders" class="text-3xl font-bold text-orange-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Today's Orders</p>
        <p id="today-orders" class="text-3xl font-bold text-blue-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Total Revenue</p>
        <p id="total-revenue" class="text-3xl font-bold text-purple-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Total Products</p>
        <p id="total-products" class="text-2xl font-bold text-indigo-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Total Customers</p>
        <p id="total-customers" class="text-2xl font-bold text-pink-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-xl font-bold mb-4">Recent Orders</h3>
    <div id="recent-orders">
        <p class="text-gray-600 animate-pulse">Loading orders...</p>
    </div>
</div>

<script>
    async function loadDashboardStats() {
        try {
            const response = await axios.get('/api/admin/dashboard/stats');
            const data = response.data;
            
            // Update stats
            document.getElementById('total-orders').textContent = data.total_orders;
            document.getElementById('pending-orders').textContent = data.pending_orders;
            document.getElementById('today-orders').textContent = data.today_orders;
            document.getElementById('total-revenue').textContent = '£' + data.total_revenue;
            document.getElementById('total-products').textContent = data.total_products;
            document.getElementById('total-customers').textContent = data.total_customers;
            
            // Update recent orders
            const ordersContainer = document.getElementById('recent-orders');
            
            if (data.recent_orders && data.recent_orders.length > 0) {
                ordersContainer.innerHTML = `
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${data.recent_orders.map(order => `
                                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/orders/${order.id}'">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${order.order_number}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">${order.user?.name || 'Guest'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">${order.items?.length || 0} items</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">£${parseFloat(order.total).toFixed(2)}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded ${getStatusColor(order.status)}">
                                                ${order.status}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">${new Date(order.created_at).toLocaleDateString()}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                ordersContainer.innerHTML = '<p class="text-gray-600">No orders yet</p>';
            }
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
            toast.error('Failed to load dashboard data');
        }
    }
    
    function getStatusColor(status) {
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'confirmed': 'bg-blue-100 text-blue-800',
            'processing': 'bg-indigo-100 text-indigo-800',
            'ready': 'bg-purple-100 text-purple-800',
            'out_for_delivery': 'bg-orange-100 text-orange-800',
            'delivered': 'bg-green-100 text-green-800',
            'completed': 'bg-green-100 text-green-800',
            'cancelled': 'bg-red-100 text-red-800',
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }
    
    loadDashboardStats();
</script>
@endsection
