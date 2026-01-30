@extends('admin.layout')

@section('title', 'Orders')

@section('content')
<h2 class="text-3xl font-bold mb-6">Orders</h2>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200" id="orders-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Orders will be loaded here -->
        </tbody>
    </table>
</div>

<script>
    async function loadOrders() {
        try {
            const response = await axios.get('/api/admin/orders');
            const orders = response.data.data;
            const tbody = document.querySelector('#orders-table tbody');
            
            if (orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No orders found</td></tr>';
                return;
            }
            
            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">#${order.id}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${order.user?.name || order.user?.phone || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">£${order.total_amount.toFixed(2)}</td>
                    <td class="px-6 py-4">
                        <select onchange="updateOrderStatus(${order.id}, this.value)" class="text-xs rounded px-2 py-1 border ${getStatusColor(order.status)}">
                            ${getStatusOptions(order.status)}
                        </select>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${getPaymentColor(order.payment_status)}">
                            ${order.payment_status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">${new Date(order.created_at).toLocaleDateString()}</td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="viewOrder(${order.id})" class="text-blue-600 hover:text-blue-900">View</button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading orders:', error);
            alert('Failed to load orders');
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
            'refunded': 'bg-gray-100 text-gray-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }
    
    function getPaymentColor(status) {
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'paid': 'bg-green-100 text-green-800',
            'failed': 'bg-red-100 text-red-800',
            'refunded': 'bg-gray-100 text-gray-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }
    
    function getStatusOptions(currentStatus) {
        const statuses = ['pending', 'confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered', 'completed', 'cancelled', 'refunded'];
        return statuses.map(status => 
            `<option value="${status}" ${status === currentStatus ? 'selected' : ''}>${status.replace('_', ' ')}</option>`
        ).join('');
    }
    
    async function updateOrderStatus(orderId, newStatus) {
        try {
            await axios.patch(`/api/admin/orders/${orderId}`, { status: newStatus });
            alert('Order status updated successfully');
        } catch (error) {
            console.error('Error updating order status:', error);
            alert('Failed to update order status');
            loadOrders();
        }
    }
    
    function viewOrder(id) {
        window.location.href = `/admin/orders/${id}`;
    }
    
    loadOrders();
</script>
@endsection
