@extends('admin.layout')

@section('page-title', 'Create Shop')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Create Shop</h1>
        <a href="/admin/shops" class="text-gray-600 hover:text-gray-900">← Back to Shops</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form id="shop-form">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Shop Name *</label>
                    <input type="text" id="name" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Slug *</label>
                    <input type="text" id="slug" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">URL-friendly identifier</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="description" rows="3" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tagline</label>
                    <input type="text" id="tagline" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Domain</label>
                    <input type="text" id="domain" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Business Type</label>
                    <input type="text" id="business_type" value="grocery" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Specialization</label>
                    <select id="specialization" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <option value="general">General</option>
                        <option value="halal">Halal</option>
                        <option value="organic">Organic</option>
                        <option value="asian">Asian</option>
                        <option value="african">African</option>
                        <option value="caribbean">Caribbean</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone *</label>
                    <input type="tel" id="phone" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                    <input type="email" id="email" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Support Email</label>
                    <input type="email" id="support_email" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp Number</label>
                    <input type="tel" id="whatsapp_number" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address Line 1 *</label>
                    <input type="text" id="address_line_1" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address Line 2</label>
                    <input type="text" id="address_line_2" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">City *</label>
                    <input type="text" id="city" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Postcode *</label>
                    <input type="text" id="postcode" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Country</label>
                    <input type="text" id="country" value="United Kingdom" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-6 border-t mt-6">
                <button type="submit" id="submit-btn" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Create Shop
                </button>
                <a href="/admin/shops" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/admin/shops/create.js')
@endsection
