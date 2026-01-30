@extends('admin.layout')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Edit Product</h1>
        <a href="/admin/products/{{ request()->route('slug') }}" class="text-green-600 hover:text-green-700 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to Product
        </a>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
        <p class="text-gray-600 mt-4">Loading product...</p>
    </div>

    <!-- Edit Form -->
    <form id="product-form" class="hidden space-y-6" onsubmit="handleSubmit(event)">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Basic Information</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                    <input type="text" id="name" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Product Type *</label>
                    <select id="type" required class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500" onchange="handleTypeChange()">
                        <option value="standard">Standard</option>
                        <option value="meat">Meat</option>
                        <option value="frozen">Frozen</option>
                        <option value="fresh">Fresh</option>
                        <option value="perishable">Perishable</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">SKU</label>
                    <input type="text" id="sku" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="description" rows="4" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Short Description</label>
                    <textarea id="short_description" rows="2" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>
            </div>
        </div>

        <!-- Meat Product Details -->
        <div id="meat-fields" class="hidden bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Meat Product Details</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Meat Type</label>
                    <select id="meat_type" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <option value="">Select type</option>
                        <option value="beef">Beef</option>
                        <option value="lamb">Lamb</option>
                        <option value="chicken">Chicken</option>
                        <option value="fish">Fish</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cut Type</label>
                    <input type="text" id="cut_type" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500" placeholder="e.g., Fillet, Breast, Leg">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_halal" class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                    <label for="is_halal" class="ml-2 text-sm font-semibold text-gray-700">Halal Certified</label>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Categories</h2>
            <div id="categories-container" class="grid md:grid-cols-3 gap-3">
                <!-- Categories will be loaded here -->
            </div>
        </div>

        <!-- Product Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Additional Details</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Brand</label>
                    <input type="text" id="brand" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Country of Origin</label>
                    <input type="text" id="country_of_origin" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ingredients</label>
                    <textarea id="ingredients" rows="2" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Allergen Information</label>
                    <textarea id="allergen_info" rows="2" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Storage Instructions</label>
                    <textarea id="storage_instructions" rows="2" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>
            </div>
        </div>

        <!-- Status Toggles -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Status</h2>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-semibold text-gray-700">Active</label>
                    <input type="checkbox" id="is_active" class="toggle-switch">
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="text-sm font-semibold text-gray-700">Featured</label>
                    <input type="checkbox" id="is_featured" class="toggle-switch">
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="text-sm font-semibold text-gray-700">On Sale</label>
                    <input type="checkbox" id="is_on_sale" class="toggle-switch">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
            <a href="/admin/products/{{ request()->route('slug') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    const productSlug = '{{ request()->route("slug") }}';
    let productData = null;
    let allCategories = [];

    async function loadProduct() {
        try {
            const [productRes, categoriesRes] = await Promise.all([
                axios.get(`/api/admin/products/${productSlug}`),
                axios.get('/api/admin/categories')
            ]);
            
            productData = productRes.data;
            allCategories = categoriesRes.data.data || categoriesRes.data;
            
            populateForm();
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('product-form').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading product:', error);
            toast.error('Failed to load product details');
            setTimeout(() => window.location.href = '/admin/products', 2000);
        }
    }

    function populateForm() {
        // Basic info
        document.getElementById('name').value = productData.name || '';
        document.getElementById('type').value = productData.type || 'standard';
        document.getElementById('sku').value = productData.sku || '';
        document.getElementById('description').value = productData.description || '';
        document.getElementById('short_description').value = productData.short_description || '';
        
        // Meat fields
        if (productData.type === 'meat') {
            document.getElementById('meat-fields').classList.remove('hidden');
            document.getElementById('meat_type').value = productData.meat_type || '';
            document.getElementById('cut_type').value = productData.cut_type || '';
            document.getElementById('is_halal').checked = productData.is_halal || false;
        }
        
        // Additional details
        document.getElementById('brand').value = productData.brand || '';
        document.getElementById('country_of_origin').value = productData.country_of_origin || '';
        document.getElementById('ingredients').value = productData.ingredients || '';
        document.getElementById('allergen_info').value = productData.allergen_info || '';
        document.getElementById('storage_instructions').value = productData.storage_instructions || '';
        
        // Status
        document.getElementById('is_active').checked = productData.is_active || false;
        document.getElementById('is_featured').checked = productData.is_featured || false;
        document.getElementById('is_on_sale').checked = productData.is_on_sale || false;
        
        // Load categories
        loadCategories();
    }

    function loadCategories() {
        const container = document.getElementById('categories-container');
        const selectedIds = (productData.categories || []).map(c => c.id);
        
        container.innerHTML = allCategories.map(cat => `
            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="checkbox" name="categories[]" value="${cat.id}" 
                    ${selectedIds.includes(cat.id) ? 'checked' : ''}
                    class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                <span class="ml-3 text-sm font-medium">${cat.name}</span>
            </label>
        `).join('');
    }

    function handleTypeChange() {
        const type = document.getElementById('type').value;
        const meatFields = document.getElementById('meat-fields');
        
        if (type === 'meat') {
            meatFields.classList.remove('hidden');
        } else {
            meatFields.classList.add('hidden');
        }
    }

    async function handleSubmit(e) {
        e.preventDefault();
        
        const selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked'))
            .map(input => parseInt(input.value));
        
        if (selectedCategories.length === 0) {
            toast.error('Please select at least one category');
            return;
        }
        
        const formData = {
            name: document.getElementById('name').value,
            type: document.getElementById('type').value,
            sku: document.getElementById('sku').value || null,
            description: document.getElementById('description').value || null,
            short_description: document.getElementById('short_description').value || null,
            brand: document.getElementById('brand').value || null,
            country_of_origin: document.getElementById('country_of_origin').value || null,
            ingredients: document.getElementById('ingredients').value || null,
            allergen_info: document.getElementById('allergen_info').value || null,
            storage_instructions: document.getElementById('storage_instructions').value || null,
            is_active: document.getElementById('is_active').checked,
            is_featured: document.getElementById('is_featured').checked,
            is_on_sale: document.getElementById('is_on_sale').checked,
            categories: selectedCategories
        };
        
        // Add meat fields if type is meat
        if (formData.type === 'meat') {
            formData.meat_type = document.getElementById('meat_type').value || null;
            formData.cut_type = document.getElementById('cut_type').value || null;
            formData.is_halal = document.getElementById('is_halal').checked;
        }
        
        try {
            const token = localStorage.getItem('auth_token');
            await axios.patch(`/api/admin/products/${productSlug}`, formData, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            toast.success('Product updated successfully');
            setTimeout(() => window.location.href = `/admin/products/${productSlug}`, 1500);
        } catch (error) {
            console.error('Error updating product:', error);
            const message = error.response?.data?.message || 'Failed to update product';
            toast.error(message);
        }
    }

    loadProduct();
</script>
@endsection
