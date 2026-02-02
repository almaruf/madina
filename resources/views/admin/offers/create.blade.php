@extends('admin.layout')

@section('title', 'Create Offer')
@section('page-title', 'Create Offer')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Create New Offer</h2>
                <a href="/admin/offers" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form id="offer-form" class="p-6 space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Offer Name *</label>
                    <input type="text" id="offer-name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Offer Type *</label>
                    <select id="offer-type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" onchange="updateFormFields()">
                        <option value="">Select type...</option>
                        <option value="percentage_discount">Percentage Discount</option>
                        <option value="fixed_discount">Fixed Amount Discount</option>
                        <option value="bxgy_free">Buy X Get Y Free</option>
                        <option value="multibuy">Multi-Buy Deal</option>
                        <option value="bxgy_discount">Buy X Get Y at Discount</option>
                        <option value="flash_sale">Flash Sale</option>
                        <option value="bundle">Bundle Deal</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea id="offer-description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
            </div>

            <!-- Offer Value Fields (Dynamic based on type) -->
            <div id="offer-fields" class="space-y-4">
                <div id="field-discount-value" class="hidden">
                    <label class="block text-sm font-medium mb-1">Discount Value</label>
                    <input type="number" id="discount-value" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Percentage (e.g., 20 for 20%) or fixed amount</p>
                </div>

                <div id="field-bxgy" class="hidden grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Buy Quantity</label>
                        <input type="number" id="buy-quantity" min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Get Quantity</label>
                        <input type="number" id="get-quantity" min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>

                <div id="field-get-discount" class="hidden">
                    <label class="block text-sm font-medium mb-1">Get Discount Percentage</label>
                    <input type="number" id="get-discount-percentage" step="0.01" min="0" max="100" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Discount percentage for the "get" items</p>
                </div>

                <div id="field-bundle-price" class="hidden">
                    <label class="block text-sm font-medium mb-1">Bundle Price</label>
                    <input type="number" id="bundle-price" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Special price for the bundle</p>
                </div>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Start Date & Time</label>
                    <input type="datetime-local" id="starts-at" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">End Date & Time</label>
                    <input type="datetime-local" id="ends-at" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>

            <!-- Badge Customization -->
            <div class="border-t pt-4">
                <h3 class="font-semibold mb-3">Badge Display</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Badge Text</label>
                        <input type="text" id="badge-text" placeholder="e.g., 20% OFF" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Badge Color</label>
                        <input type="color" id="badge-color" value="#DC2626" class="w-full h-10 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Conditions & Limits -->
            <div class="border-t pt-4">
                <h3 class="font-semibold mb-3">Conditions & Limits</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Min Purchase Amount</label>
                        <input type="number" id="min-purchase-amount" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Max Uses Per Customer</label>
                        <input type="number" id="max-uses-per-customer" min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Total Usage Limit</label>
                        <input type="number" id="total-usage-limit" min="1" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Priority & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Priority</label>
                    <input type="number" id="priority" value="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Higher priority shows first</p>
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="is-active" checked class="w-4 h-4">
                        <span class="text-sm font-medium">Active</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                <a href="/admin/offers" class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-medium flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Create Offer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    @vite('resources/js/admin/offers/create.js')
@endsection
