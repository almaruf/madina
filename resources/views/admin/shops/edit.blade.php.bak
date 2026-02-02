@extends('admin.layout')

@section('page-title', 'Edit Shop')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Edit Shop</h1>
        <a href="/admin/shops" class="text-gray-600 hover:text-gray-900">← Back to Shops</a>
    </div>

    <form id="shop-form" class="bg-white rounded-lg shadow p-6 space-y-6" onsubmit="saveShop(event)">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Shop Name *</label>
                <input type="text" id="name" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Slug *</label>
                <input type="text" id="slug" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-500 mt-1">URL-friendly identifier (e.g., my-shop)</p>
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
                <p class="text-xs text-gray-500 mt-1">Full domain (e.g., shop.example.com)</p>
            </div>

            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone *</label>
                <div class="flex gap-2">
                    <select id="phone-prefix" class="border rounded px-4 py-2 bg-white" style="width: 80px;">
                        <option value="+44">+44</option>
                    </select>
                    <input type="tel" id="phone" placeholder="7911123456" required inputmode="numeric" pattern="[0-9]*" class="flex-1 border rounded px-4 py-2 focus:ring-2 focus:ring-green-500" maxlength="11">
                </div>
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

            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Address</h3>
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
                <input type="text" id="country" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Settings</h3>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Business Type</label>
                <select id="business_type" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <option value="">Select type</option>
                    <option value="grocery">Grocery</option>
                    <option value="supermarket">Supermarket</option>
                    <option value="convenience">Convenience Store</option>
                    <option value="specialty">Specialty Store</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Specialization</label>
                <input type="text" id="specialization" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500" placeholder="e.g., Halal, Organic">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Currency</label>
                <input type="text" id="currency" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Currency Symbol</label>
                <input type="text" id="currency_symbol" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery & Pricing</h3>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Order Amount</label>
                <input type="number" step="0.01" min="0" id="min_order_amount" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Fee</label>
                <input type="number" step="0.01" min="0" id="delivery_fee" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Free Delivery Threshold</label>
                <input type="number" step="0.01" min="0" id="free_delivery_threshold" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Radius (km)</label>
                <input type="number" step="0.1" min="0" id="delivery_radius_km" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Features</h3>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="has_halal_products" class="rounded text-green-600">
                    <span class="text-sm text-gray-700">Halal Products</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="has_organic_products" class="rounded text-green-600">
                    <span class="text-sm text-gray-700">Organic Products</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="has_international_products" class="rounded text-green-600">
                    <span class="text-sm text-gray-700">International Products</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="delivery_enabled" class="rounded text-green-600">
                    <span class="text-sm text-gray-700">Delivery Enabled</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="collection_enabled" class="rounded text-green-600">
                    <span class="text-sm text-gray-700">Collection Enabled</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="online_payment" class="rounded text-green-600">
                    <span class="text-sm text-gray-700">Online Payment</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="loyalty_program" class="rounded text-green-600">
                    <span class="text-sm text-gray-700">Loyalty Program</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="reviews_enabled" class="rounded text-green-600">
                    <span class="text-sm text-gray-700">Reviews Enabled</span>
                </label>
            </div>

            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Branding</h3>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Primary Color</label>
                <input type="color" id="primary_color" class="w-full h-10 border rounded">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Secondary Color</label>
                <input type="color" id="secondary_color" class="w-full h-10 border rounded">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Logo URL</label>
                <input type="url" id="logo_url" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cover Image URL</label>
                <input type="url" id="cover_image_url" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-500 mt-1">Shop banner/cover image</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Favicon URL</label>
                <input type="url" id="favicon_url" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Media</h3>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook URL</label>
                <input type="url" id="facebook_url" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Instagram URL</label>
                <input type="url" id="instagram_url" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter URL</label>
                <input type="url" id="twitter_url" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
            </div>

            <div class="md:col-span-2 border-t pt-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="is_active" class="rounded text-green-600">
                    <span class="text-sm font-semibold text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="button" onclick="window.location='/admin/shops'"
                    class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300">Cancel</button>
            <button type="submit"
                    class="px-6 py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700">Save Changes</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
    @vite('resources/js/admin/shops/edit.js')
@endsection
