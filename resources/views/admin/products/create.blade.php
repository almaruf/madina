@extends('admin.layout')

@section('page-title', 'Create Product')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Create Product</h1>
        <a href="/admin/products" class="text-gray-600 hover:text-gray-900">← Back to Products</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form id="product-form">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                    <input type="text" id="name" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug *</label>
                    <input type="text" id="slug" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">URL-friendly identifier</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Short Description</label>
                    <textarea id="short_description" rows="2" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500" placeholder="Brief product summary"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="description" rows="4" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <!-- Categories -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Categories *</label>
                    <select id="categories" multiple class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500" size="5">
                        <option value="">Loading categories...</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                </div>

                <!-- Product Type & Details -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Product Type</label>
                    <select id="type" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <option value="standard">Standard</option>
                        <option value="meat">Meat</option>
                        <option value="fresh">Fresh</option>
                        <option value="frozen">Frozen</option>
                        <option value="perishable">Perishable</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Brand</label>
                    <input type="text" id="brand" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Variation (Simple - Default) -->
                <div class="md:col-span-2 border-t pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Default Variation</h3>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Size/Weight *</label>
                    <input type="text" id="variation_size" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500" placeholder="e.g., 500g, 1kg, 1L">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                    <select id="variation_unit" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <option value="piece">Piece</option>
                        <option value="kg">Kilogram (kg)</option>
                        <option value="g">Gram (g)</option>
                        <option value="l">Liter (L)</option>
                        <option value="ml">Milliliter (ml)</option>
                        <option value="pack">Pack</option>
                        <option value="box">Box</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Price (£) *</label>
                    <input type="number" id="variation_price" step="0.01" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Compare At Price (£)</label>
                    <input type="number" id="variation_compare_at_price" step="0.01" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Original price (for discounts)</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Stock Quantity *</label>
                    <input type="number" id="variation_stock_quantity" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500" value="0">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">SKU</label>
                    <input type="text" id="variation_sku" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Barcode</label>
                    <input type="text" id="variation_barcode" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Flags -->
                <div class="md:col-span-2 border-t pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Options</h3>
                </div>

                <div class="md:col-span-2 space-y-3">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="is_active" checked class="rounded">
                        <span class="text-sm font-semibold text-gray-700">Active (Visible on store)</span>
                    </label>
                    
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="is_featured" class="rounded">
                        <span class="text-sm font-semibold text-gray-700">Featured Product</span>
                    </label>
                    
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="is_halal" class="rounded">
                        <span class="text-sm font-semibold text-gray-700">Halal Certified</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-6 border-t mt-6">
                <button type="submit" id="submit-btn" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Create Product
                </button>
                <a href="/admin/products" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/admin/products/create.js')
@endsection
