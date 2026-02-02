@extends('admin.layout')

@section('title', 'Create Buy X Get Y Offer')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Create Buy X Get Y Offer</h1>
            <p class="text-gray-600 mt-1">Set up a promotional offer where customers get free or discounted items</p>
        </div>
        <a href="/admin/offers" class="text-green-600 hover:text-green-700 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to Offers
        </a>
    </div>

    <form id="offer-form" class="space-y-6" onsubmit="handleSubmit(event)">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Basic Information</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Offer Name *</label>
                    <input type="text" id="name" required placeholder="e.g., Buy 2 Get 1 Free" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="description" rows="3" placeholder="Tell customers about this offer" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>
            </div>
        </div>

        <!-- Offer Type Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Offer Type</h2>
            
            <div class="space-y-4">
                <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition" onclick="selectOfferType('bxgy_free')">
                    <input type="radio" name="offer_type" value="bxgy_free" class="mt-1 mr-3" checked onchange="handleOfferTypeChange()">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900">Buy X Get Y Free</div>
                        <div class="text-sm text-gray-600 mt-1">Customer gets items completely free (e.g., Buy 2 Get 1 Free)</div>
                    </div>
                </label>

                <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition" onclick="selectOfferType('bxgy_discount')">
                    <input type="radio" name="offer_type" value="bxgy_discount" class="mt-1 mr-3" onchange="handleOfferTypeChange()">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900">Buy X Get Y at Discount</div>
                        <div class="text-sm text-gray-600 mt-1">Customer gets items at a discounted price (e.g., Buy 2 Get 1 at 50% off)</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Offer Configuration -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Offer Configuration</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Buy Quantity *</label>
                    <input type="number" id="buy_quantity" min="1" required placeholder="e.g., 2" value="2" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-sm text-gray-500 mt-1">Number of items customer must buy</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Get Quantity *</label>
                    <input type="number" id="get_quantity" min="1" required placeholder="e.g., 1" value="1" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-sm text-gray-500 mt-1">Number of items customer receives</p>
                </div>

                <!-- Discount Percentage (only for bxgy_discount) -->
                <div id="discount-field" class="hidden md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Discount on "Get" Items (%) *</label>
                    <input type="number" id="get_discount_percentage" min="0" max="100" step="0.01" placeholder="e.g., 50" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-sm text-gray-500 mt-1">Percentage discount applied to the "get" items (0-100)</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Badge Text</label>
                    <input type="text" id="badge_text" placeholder="e.g., Buy 2 Get 1 FREE" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-sm text-gray-500 mt-1">Text shown on product cards</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Badge Color</label>
                    <input type="color" id="badge_color" class="w-full border rounded-lg px-2 py-2 h-10 focus:ring-2 focus:ring-green-500" value="#DC2626">
                </div>
            </div>
        </div>

        <!-- Product Selection for "Buy" -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <i class="fas fa-shopping-cart text-blue-600"></i>
                Products to Buy (Qualifying Products)
            </h2>
            
            <p class="text-sm text-gray-600 mb-4">Select which products the customer must purchase to qualify for this offer</p>
            
            <button type="button" onclick="openProductModal('buy')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Add "Buy" Products
            </button>
            
            <div id="buy-products-display" class="mt-4 space-y-2">
                <p class="text-gray-500">No products selected. Click "Add" to select products.</p>
            </div>
        </div>

        <!-- Product Selection for "Get" -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <i class="fas fa-gift text-green-600"></i>
                Products to Get (Reward Products)
            </h2>
            
            <p class="text-sm text-gray-600 mb-4">Select which products the customer will receive free or at a discount</p>
            
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="same_as_buy" class="w-5 h-5" onchange="handleSameAsBuyChange()">
                    <span class="text-sm font-semibold text-gray-700">Same as "Buy" products (most common for BOGO offers)</span>
                </label>
            </div>
            
            <div id="get-product-selection">
                <button type="button" onclick="openProductModal('get')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add "Get" Products
                </button>
                
                <div id="get-products-display" class="mt-4 space-y-2">
                    <p class="text-gray-500">No products selected. Click "Add" to select products.</p>
                </div>
            </div>
        </div>

        <!-- Validity Period -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Validity Period</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date & Time</label>
                    <input type="datetime-local" id="starts_at" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-sm text-gray-500 mt-1">When the offer begins</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">End Date & Time</label>
                    <input type="datetime-local" id="ends_at" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-sm text-gray-500 mt-1">When the offer ends</p>
                </div>
            </div>
        </div>

        <!-- Usage Limits -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Usage Limits (Optional)</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Max Uses Per Customer</label>
                    <input type="number" id="max_uses_per_customer" min="1" placeholder="e.g., 3" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-sm text-gray-500 mt-1">Leave blank for unlimited</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Usage Limit</label>
                    <input type="number" id="total_usage_limit" min="1" placeholder="e.g., 100" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-sm text-gray-500 mt-1">Leave blank for unlimited</p>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Status</h2>
            
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" id="is_active" class="w-5 h-5" checked>
                <span class="text-sm font-semibold text-gray-700">Active (customers can use this offer)</span>
            </label>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold inline-flex items-center gap-2">
                <i class="fas fa-save"></i>
                Create Offer
            </button>
            <a href="/admin/offers" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- Product Selection Modal -->
<div id="product-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="border-b p-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold" id="modal-title">Select Products</h3>
                <p class="text-sm text-gray-600 mt-1" id="modal-subtitle"></p>
            </div>
            <button type="button" onclick="closeProductModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Search & Filter -->
        <div class="border-b p-4 space-y-3">
            <input type="text" id="product-search" placeholder="Search products..." 
                class="w-full border rounded-lg px-4 py-2" onkeyup="searchProducts()">
            
            <div class="flex gap-2">
                <select id="product-category-filter" class="flex-1 border rounded-lg px-4 py-2" onchange="searchProducts()">
                    <option value="">All Categories</option>
                </select>
                
                <button type="button" onclick="selectAllVisibleProducts()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Select All
                </button>
                
                <button type="button" onclick="clearCurrentSelection()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                    Clear
                </button>
            </div>
        </div>

        <!-- Products List -->
        <div id="products-list" class="overflow-y-auto flex-1 p-4">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
                <p class="text-gray-600 mt-4">Loading products...</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t p-4 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                <span id="selection-count">0</span> products selected
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeProductModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Cancel
                </button>
                <button type="button" onclick="confirmProductSelection()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite('resources/js/admin/offers/create-bxgy.js')
@endsection
