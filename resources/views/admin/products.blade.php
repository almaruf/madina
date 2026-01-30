@extends('admin.layout')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Products</h2>
    <button onclick="showCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
        + Add Product
    </button>
</div>

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
    <table class="min-w-full divide-y divide-gray-200" id="products-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Products will be loaded here -->
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
        loadProducts();
    }
    
    async function loadProducts() {
        try {
            const url = currentTab === 'archived' ? '/api/admin/products?archived=1' : '/api/admin/products';
            const response = await axios.get(url);
            const products = response.data.data || response.data;
            const tbody = document.querySelector('#products-table tbody');
            
            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No products found</td></tr>';
                return;
            }
            
            tbody.innerHTML = products.map(product => {
                const price = product.variations?.[0]?.price ? parseFloat(product.variations[0].price).toFixed(2) : '0.00';
                const stock = product.variations?.[0]?.stock || 0;
                const categories = product.categories?.map(c => c.name).join(', ') || 'N/A';
                
                return `
                    <tr>
                        <td class="px-6 py-4">
                            <img src="${product.primary_image?.url || '/placeholder.png'}" alt="${product.name}" class="w-16 h-16 object-cover rounded">
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${product.name}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">${categories}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">£${price}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${stock}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded ${product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${product.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="/admin/products/${product.slug}" class="text-blue-600 hover:text-blue-900">View Details</a>
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (error) {
            console.error('Error loading products:', error);
            toast.error('Failed to load products');
        }
    }
    
    function showCreateModal() {
        toast.info('Product creation form coming soon!');
    }
    
    loadProducts();
</script>
@endsection
