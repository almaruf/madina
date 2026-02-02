@extends('admin.layout')

@section('title', 'Add New Category')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Category</h1>
                <p class="text-gray-600 mt-1">Create a new product category</p>
            </div>
            <a href="/admin/categories" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Categories
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="createForm">
            <!-- Basic Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., Fresh Vegetables"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">This will be displayed to customers</p>
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                        URL Slug <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="slug" 
                        name="slug"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., fresh-vegetables"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Auto-generated from name, used in URLs</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description"
                        rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Brief description of this category"
                    ></textarea>
                </div>

                <!-- Parent Category -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Parent Category
                    </label>
                    <select 
                        id="parent_id" 
                        name="parent_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">None (Top Level Category)</option>
                        <!-- Populated by JavaScript -->
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Optional: Make this a subcategory</p>
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-1">
                        Display Order
                    </label>
                    <input 
                        type="number" 
                        id="order" 
                        name="order"
                        value="0"
                        min="0"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="0"
                    >
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first (0 = default)</p>
                </div>

                <!-- Status Flags -->
                <div class="space-y-3 pt-4">
                    <h4 class="text-sm font-medium text-gray-700">Status</h4>
                    
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="is_active" 
                            name="is_active"
                            checked
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                            Active (visible to customers)
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="is_featured" 
                            name="is_featured"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="is_featured" class="ml-2 text-sm text-gray-700">
                            Featured (show on homepage)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex items-center space-x-3">
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition flex items-center"
                >
                    <i class="fas fa-save mr-2"></i>Create Category
                </button>
                <a 
                    href="/admin/categories" 
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('build/admin/categories/create.js') }}"></script>
@endsection
