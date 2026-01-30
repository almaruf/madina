@extends('admin.layout')

@section('title', 'Shops')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Shops</h2>
    <a href="{{ route('admin.shops.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
        + Add Shop
    </a>
</div>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200" id="shops-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Shops will be loaded here -->
        </tbody>
    </table>
</div>

<script>
    async function loadShops() {
        try {
            const response = await axios.get('/api/admin/shops');
            const shops = response.data.data;
            const tbody = document.querySelector('#shops-table tbody');
            
            if (shops.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No shops found</td></tr>';
                return;
            }
            
            tbody.innerHTML = shops.map(shop => `
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${shop.name}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${shop.domain || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${shop.city || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${shop.phone || 'N/A'}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${shop.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${shop.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="/admin/shops/${shop.id}/edit" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <button onclick="toggleShopStatus(${shop.id}, ${!shop.is_active})" class="text-${shop.is_active ? 'red' : 'green'}-600 hover:text-${shop.is_active ? 'red' : 'green'}-900">
                            ${shop.is_active ? 'Deactivate' : 'Activate'}
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading shops:', error);
            alert('Failed to load shops');
        }
    }
    
    async function toggleShopStatus(id, activate) {
        try {
            await axios.patch(`/api/admin/shops/${id}`, { is_active: activate });
            alert(`Shop ${activate ? 'activated' : 'deactivated'} successfully`);
            loadShops();
        } catch (error) {
            console.error('Error updating shop status:', error);
            alert('Failed to update shop status');
        }
    }
    
    loadShops();
</script>
@endsection
