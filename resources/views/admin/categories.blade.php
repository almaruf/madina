@extends('admin.layout')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Categories</h2>
    <button onclick="showCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
        + Add Category
    </button>
</div>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200" id="categories-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Categories will be loaded here -->
        </tbody>
    </table>
</div>

<script>
    async function loadCategories() {
        try {
            const response = await axios.get('/api/admin/categories');
            const categories = response.data.data;
            const tbody = document.querySelector('#categories-table tbody');
            
            if (categories.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No categories found</td></tr>';
                return;
            }
            
            tbody.innerHTML = categories.map(category => `
                <tr>
                    <td class="px-6 py-4">
                        ${category.image ? `<img src="${category.image}" alt="${category.name}" class="w-16 h-16 object-cover rounded">` : '<div class="w-16 h-16 bg-gray-200 rounded"></div>'}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${category.name}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${category.slug}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${category.products_count || 0}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${category.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="editCategory(${category.id})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                        <button onclick="deleteCategory(${category.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading categories:', error);
            alert('Failed to load categories');
        }
    }
    
    function showCreateModal() {
        alert('Category creation form coming soon!');
    }
    
    function editCategory(id) {
        alert(`Edit category ${id} - coming soon!`);
    }
    
    async function deleteCategory(id) {
        if (!confirm('Are you sure you want to delete this category?')) return;
        
        try {
            await axios.delete(`/api/admin/categories/${id}`);
            alert('Category deleted successfully');
            loadCategories();
        } catch (error) {
            console.error('Error deleting category:', error);
            alert('Failed to delete category');
        }
    }
    
    loadCategories();
</script>
@endsection
