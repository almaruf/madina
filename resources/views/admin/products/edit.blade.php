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

        <!-- Variations -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold">Product Variations</h2>
                <button type="button" onclick="addVariation()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-plus mr-2"></i>Add Variation
                </button>
            </div>
            
            <div id="variations-container" class="space-y-4">
                <!-- Variations will be loaded here -->
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

@vite('resources/js/admin/products/edit.js')
@endsection
