@extends('admin.layout')

@section('title', 'Orders')

@section('content')
<h2 class="text-3xl font-bold mb-6">Orders</h2>

<!-- Tabs -->
<div class="mb-4 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8">
        <button onclick="switchTab('active')" id="tab-active" class="tab-button border-b-2 border-green-600 py-2 px-1 text-sm font-medium text-green-600">
            Active
        </button>
        <button onclick="switchTab('archived')" id="tab-archived" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
            Archived
        </button>
    </nav>
</div>

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
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Orders will be loaded here -->
        </tbody>
    </table>
</div>

<script>
    let currentTab = 'active';
    
    function switchTab(tab) {
        currentTab = tab;
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-green-600', 'text-green-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        document.getElementById(`tab-${tab}`).classList.remove('border-transparent', 'text-gray-500');
        document.getElementById(`tab-${tab}`).classList.add('border-green-600', 'text-green-600');
        loadOrders();
    }
    
    async function loadOrders() {
        try {
            const url = currentTab === 'archived' ? '/api/admin/orders?archived=1' : '/api/admin/orders';
            const response = await axios.get(url);
            const orders = response.data.data || response.data;
            const tbody = document.querySelector('#orders-table tbody');
            
            if (orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No orders found</td></tr>';
                return;
            }
            
            tbody.innerHTML = orders.map(order => {
                const totalAmount = parseFloat(order.total || 0).toFixed(2);
                const userName = order.user?.name || order.user?.phone || 'N/A';
                const createdDate = new Date(order.created_at).toLocaleDateString();
                
                return `
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#${order.id}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${userName}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">£${totalAmount}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded ${getStatusColor(order.status)}">
                                ${order.status.replace('_', ' ')}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded ${getPaymentColor(order.payment_status)}">
                                ${order.payment_status}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">${createdDate}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="/admin/orders/${order.id}" class="text-blue-600 hover:text-blue-900">View Details</a>
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (error) {
            console.error('Error loading orders:', error);
            toast.error('Failed to load orders');
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
    

    
    loadOrders();
</script>
@endsection
