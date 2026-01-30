@extends('admin.layout')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Categories</h2>
    <button onclick="showCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
        + Add Category
    </button>
</div>

<!-- Tabs -->
<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8">
        <button onclick="switchTab('active')" id="active-tab" class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
            Active
        </button>
        <button onclick="switchTab('archived')" id="archived-tab" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
            Archived
        </button>
    </nav>
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
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Categories will be loaded here -->
        </tbody>
    </table>
</div>

<script>
    let currentTab = 'active';

    function switchTab(tab) {
        currentTab = tab;
        
        // Update tab styling
        const activeTab = document.getElementById('active-tab');
        const archivedTab = document.getElementById('archived-tab');
        
        if (tab === 'active') {
            activeTab.classList.add('border-blue-500', 'text-blue-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            archivedTab.classList.remove('border-blue-500', 'text-blue-600');
            archivedTab.classList.add('border-transparent', 'text-gray-500');
        } else {
            archivedTab.classList.add('border-blue-500', 'text-blue-600');
            archivedTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.remove('border-blue-500', 'text-blue-600');
            activeTab.classList.add('border-transparent', 'text-gray-500');
        }
        
        loadCategories();
    }

    async function loadCategories() {
        try {
            const url = currentTab === 'archived' ? '/api/admin/categories?archived=1' : '/api/admin/categories';
            const response = await axios.get(url);
            const categories = response.data.data || response.data;
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
                    <td class="px-6 py-4 text-sm text-right">
                        <a href="/admin/categories/${category.slug}" class="text-blue-600 hover:text-blue-900 font-medium">
                            View Details
                        </a>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading categories:', error);
            toast.error('Failed to load categories');
        }
    }
    
    function showCreateModal() {
        toast.info('Category creation form coming soon!');
    }
    
    loadCategories();
</script>
@endsection

