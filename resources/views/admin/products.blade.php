@extends('admin.layout')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Products</h2>
    <button onclick="showCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
        + Add Product
    </button>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Products will be loaded here -->
        </tbody>
    </table>
</div>

<script>
    async function loadProducts() {
        try {
            const response = await axios.get('/api/admin/products');
            const products = response.data.data;
            const tbody = document.querySelector('#products-table tbody');
            
            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No products found</td></tr>';
                return;
            }
            
            tbody.innerHTML = products.map(product => `
                <tr>
                    <td class="px-6 py-4">
                        <img src="${product.primary_image?.url || '/placeholder.png'}" alt="${product.name}" class="w-16 h-16 object-cover rounded">
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${product.name}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${product.categories?.map(c => c.name).join(', ') || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">£${product.variations?.[0]?.price.toFixed(2) || '0.00'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${product.variations?.[0]?.stock || 0}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${product.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="editProduct(${product.id})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                        <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading products:', error);
            alert('Failed to load products');
        }
    }
    
    function showCreateModal() {
        alert('Product creation form coming soon!');
    }
    
    function editProduct(id) {
        window.location.href = `/admin/products/${id}/edit`;
    }
    
    async function deleteProduct(id) {
        if (!confirm('Are you sure you want to delete this product?')) return;
        
        try {
            await axios.delete(`/api/admin/products/${id}`);
            alert('Product deleted successfully');
            loadProducts();
        } catch (error) {
            console.error('Error deleting product:', error);
            alert('Failed to delete product');
        }
    }
    
    loadProducts();
</script>
@endsection
