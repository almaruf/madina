@extends('admin.layout')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Edit Category</h1>
        <a href="/admin/categories/{{ request()->route('slug') }}" class="text-green-600 hover:text-green-700 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to Category
        </a>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
        <p class="text-gray-600 mt-4">Loading category...</p>
    </div>

    <!-- Edit Form -->
    <form id="category-form" class="hidden space-y-6" onsubmit="handleSubmit(event)">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Category Information</h2>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category Name *</label>
                    <input type="text" id="name" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug</label>
                    <input type="text" id="slug" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate from name</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Parent Category</label>
                    <select id="parent_id" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <option value="">None (Top Level)</option>
                        <!-- Categories will be loaded here -->
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="description" rows="4" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Image URL</label>
                    <input type="url" id="image" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500" placeholder="https://...">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Display Order</label>
                    <input type="number" id="order" min="0" value="0" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                </div>
            </div>
        </div>

        <!-- Status Toggles -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Status</h2>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Active</label>
                        <p class="text-xs text-gray-500">Show this category on the website</p>
                    </div>
                    <input type="checkbox" id="is_active" class="toggle-switch">
                </div>
                
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Featured</label>
                        <p class="text-xs text-gray-500">Display in featured categories section</p>
                    </div>
                    <input type="checkbox" id="is_featured" class="toggle-switch">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
            <a href="/admin/categories/{{ request()->route('slug') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    const categorySlug = '{{ request()->route("slug") }}';
    let categoryData = null;
    let allCategories = [];

    async function loadCategory() {
        try {
            const [categoryRes, categoriesRes] = await Promise.all([
                axios.get(`/api/admin/categories/${categorySlug}`),
                axios.get('/api/admin/categories')
            ]);
            
            categoryData = categoryRes.data;
            allCategories = (categoriesRes.data.data || categoriesRes.data).filter(c => c.slug !== categorySlug);
            
            populateForm();
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('category-form').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading category:', error);
            toast.error('Failed to load category details');
            setTimeout(() => window.location.href = '/admin/categories', 2000);
        }
    }

    function populateForm() {
        document.getElementById('name').value = categoryData.name || '';
        document.getElementById('slug').value = categoryData.slug || '';
        document.getElementById('description').value = categoryData.description || '';
        document.getElementById('image').value = categoryData.image || '';
        document.getElementById('order').value = categoryData.order || 0;
        document.getElementById('is_active').checked = categoryData.is_active || false;
        document.getElementById('is_featured').checked = categoryData.is_featured || false;
        
        // Load parent categories
        const parentSelect = document.getElementById('parent_id');
        parentSelect.innerHTML = '<option value="">None (Top Level)</option>' + 
            allCategories.map(cat => `
                <option value="${cat.id}" ${cat.id === categoryData.parent_id ? 'selected' : ''}>
                    ${cat.name}
                </option>
            `).join('');
    }

    async function handleSubmit(e) {
        e.preventDefault();
        
        const formData = {
            name: document.getElementById('name').value,
            slug: document.getElementById('slug').value || null,
            parent_id: document.getElementById('parent_id').value || null,
            description: document.getElementById('description').value || null,
            image: document.getElementById('image').value || null,
            order: parseInt(document.getElementById('order').value) || 0,
            is_active: document.getElementById('is_active').checked,
            is_featured: document.getElementById('is_featured').checked
        };
        
        try {
            const token = localStorage.getItem('auth_token');
            await axios.patch(`/api/admin/categories/${categorySlug}`, formData, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            toast.success('Category updated successfully');
            setTimeout(() => window.location.href = `/admin/categories/${categorySlug}`, 1500);
        } catch (error) {
            console.error('Error updating category:', error);
            const message = error.response?.data?.message || 'Failed to update category';
            toast.error(message);
        }
    }

    loadCategory();
</script>
@endsection
