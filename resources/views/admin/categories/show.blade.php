@extends('admin.layout')

@section('title', 'Category Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/admin/categories" class="text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Category Details</h1>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
        <p class="mt-2 text-gray-600">Loading category details...</p>
    </div>

    <!-- Error State -->
    <div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <p class="text-red-800"></p>
    </div>

    <!-- Category Details -->
    <div id="category-details" class="hidden">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- Category Header -->
            <div id="category-header" class="p-6 border-b"></div>

            <!-- Category Info -->
            <div id="category-info" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6"></div>

            <!-- Products Count -->
            <div id="products-section" class="p-6 border-t"></div>

            <!-- Actions -->
            <div id="category-actions" class="p-6 bg-gray-50 border-t flex gap-4"></div>
        </div>
    </div>
</div>

<script>
    const categorySlug = '{{ $slug }}';
    let currentCategory = null;

    async function loadCategory() {
        try {
            const response = await axios.get(`/api/admin/categories/${categorySlug}`);
            currentCategory = response.data;
            renderCategory(currentCategory);
        } catch (error) {
            console.error('Error loading category:', error);
            document.getElementById('loading').classList.add('hidden');
            const errorDiv = document.getElementById('error');
            errorDiv.classList.remove('hidden');
            errorDiv.querySelector('p').textContent = error.response?.data?.message || 'Failed to load category details';
        }
    }

    function renderCategory(category) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('category-details').classList.remove('hidden');

        const isArchived = category.deleted_at !== null;

        // Header
        document.getElementById('category-header').innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">${category.name}</h2>
                    ${isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>' : ''}
                    ${category.is_active && !isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>' : ''}
                    ${!category.is_active && !isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Inactive</span>' : ''}
                </div>
            </div>
        `;

        // Info
        document.getElementById('category-info').innerHTML = `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Category Name</h3>
                <p class="text-lg text-gray-900">${category.name}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Slug</h3>
                <p class="text-lg text-gray-900">${category.slug}</p>
            </div>
            ${category.description ? `
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                    <p class="text-gray-900">${category.description}</p>
                </div>
            ` : ''}
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
                <p class="text-gray-900">${category.is_active ? 'Active' : 'Inactive'}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Created</h3>
                <p class="text-gray-900">${new Date(category.created_at).toLocaleString()}</p>
            </div>
        `;

        // Products section
        const productsCount = category.products_count || 0;
        document.getElementById('products-section').innerHTML = `
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Products</h3>
            <p class="text-gray-700">${productsCount} product${productsCount !== 1 ? 's' : ''} in this category</p>
        `;

        // Actions
        const actionsHtml = [];
        
        if (!isArchived) {
            actionsHtml.push(`
                <a href="/admin/categories/${categorySlug}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Category
                </a>
                <button onclick="confirmArchiveCategory()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Archive Category
                </button>
            `);
        } else {
            actionsHtml.push(`
                <button onclick="restoreCategory()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Restore Category
                </button>
                <button onclick="confirmPermanentDeleteCategory()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Permanently Delete
                </button>
            `);
        }

        document.getElementById('category-actions').innerHTML = actionsHtml.join('');
    }

    function confirmArchiveCategory() {
        toast.warning('Click Archive again to confirm', 3000);
        const btn = event.target;
        btn.textContent = 'Confirm Archive';
        btn.onclick = archiveCategory;
        setTimeout(() => {
            btn.textContent = 'Archive';
            btn.onclick = confirmArchiveCategory;
        }, 3000);
    }

    async function archiveCategory() {
        try {
            await axios.delete(`/api/admin/categories/${categorySlug}`);
            toast.success('Category archived successfully!');
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            console.error('Error archiving category:', error);
            toast.error(error.response?.data?.message || 'Failed to archive category');
        }
    }

    async function restoreCategory() {
        try {
            await axios.post(`/api/admin/categories/${categorySlug}/restore`);
            toast.success('Category restored successfully!');
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            console.error('Error restoring category:', error);
            toast.error(error.response?.data?.message || 'Failed to restore category');
        }
    }

    function confirmPermanentDeleteCategory() {
        toast.warning('Click Delete again to PERMANENTLY delete', 3000);
        const btn = event.target;
        btn.textContent = 'Confirm Delete';
        btn.onclick = permanentlyDeleteCategory;
        setTimeout(() => {
            btn.textContent = 'Permanent Delete';
            btn.onclick = confirmPermanentDeleteCategory;
        }, 3000);
    }

    async function permanentlyDeleteCategory() {
        try {
            await axios.delete(`/api/admin/categories/${categorySlug}/force`);
            toast.success('Category permanently deleted!');
            setTimeout(() => window.location.href = '/admin/categories', 1000);
        } catch (error) {
            console.error('Error deleting category:', error);
            toast.error(error.response?.data?.message || 'Failed to delete category');
        }
    }

    // Load category on page load
    loadCategory();
</script>
@endsection
