@extends('admin.layout')

@section('page-title', 'Create Shop')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Create Shop</h1>
        <a href="/admin/shops" class="text-gray-600 hover:text-gray-900">← Back to Shops</a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button type="button" data-tab="basic" class="tab-btn active px-6 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                    Basic Information
                </button>
                <button type="button" data-tab="delivery" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Delivery & Pricing
                </button>
                <button type="button" data-tab="legal" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Legal & VAT
                </button>
                <button type="button" data-tab="bank" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Bank Details
                </button>
                <button type="button" data-tab="branding" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Branding & Social
                </button>
                <button type="button" data-tab="hours" class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Operating Hours
                </button>
            </nav>
        </div>

        <form id="shop-form" class="p-6">
            <!-- Basic Information Tab -->
            <div id="tab-basic" class="tab-content">
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
            </div>

            <!-- Delivery & Pricing Tab -->
            <div id="tab-delivery" class="tab-content hidden">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Currency</label>
                        <input type="text" id="currency" value="GBP" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Currency Symbol</label>
                        <input type="text" id="currency_symbol" value="£" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Fee</label>
                        <input type="number" id="delivery_fee" step="0.01" value="3.99" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Min Order Amount</label>
                        <input type="number" id="min_order_amount" step="0.01" value="20.00" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Free Delivery Threshold</label>
                        <input type="number" id="free_delivery_threshold" step="0.01" value="50.00" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Radius (km)</label>
                        <input type="number" id="delivery_radius_km" value="10" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="delivery_enabled" checked class="rounded">
                            <span class="text-sm font-semibold text-gray-700">Delivery Enabled</span>
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="collection_enabled" checked class="rounded">
                            <span class="text-sm font-semibold text-gray-700">Collection Enabled</span>
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="online_payment" class="rounded">
                            <span class="text-sm font-semibold text-gray-700">Online Payment Enabled</span>
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="has_halal_products" class="rounded">
                            <span class="text-sm font-semibold text-gray-700">Has Halal Products</span>
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="has_organic_products" class="rounded">
                            <span class="text-sm font-semibold text-gray-700">Has Organic Products</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Legal & VAT Tab -->
            <div id="tab-legal" class="tab-content hidden">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Legal Company Name</label>
                        <input type="text" id="legal_company_name" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Company Registration Number</label>
                        <input type="text" id="company_registration_number" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="vat_registered" class="rounded">
                            <span class="text-sm font-semibold text-gray-700">VAT Registered</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">VAT Number</label>
                        <input type="text" id="vat_number" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">VAT Rate (%)</label>
                        <input type="number" id="vat_rate" step="0.01" value="{{ config('services.vat.default_rate', 20.00) }}" readonly class="w-full border rounded px-4 py-2 bg-gray-100 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Configured in environment settings</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="prices_include_vat" checked class="rounded">
                            <span class="text-sm font-semibold text-gray-700">Prices Include VAT</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">When checked, displayed prices are VAT-inclusive. When unchecked, VAT will be added at checkout.</p>
                    </div>
                </div>
            </div>

            <!-- Bank Details Tab -->
            <div id="tab-bank" class="tab-content hidden">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Name</label>
                        <input type="text" id="bank_name" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Account Name</label>
                        <input type="text" id="bank_account_name" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Account Number</label>
                        <input type="text" id="bank_account_number" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sort Code</label>
                        <input type="text" id="bank_sort_code" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">IBAN</label>
                        <input type="text" id="bank_iban" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">SWIFT/BIC Code</label>
                        <input type="text" id="bank_swift_code" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
            </div>

            <!-- Branding & Social Tab -->
            <div id="tab-branding" class="tab-content hidden">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Primary Color</label>
                        <input type="color" id="primary_color" value="#10b981" class="w-full h-10 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Secondary Color</label>
                        <input type="color" id="secondary_color" value="#059669" class="w-full h-10 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Logo URL</label>
                        <input type="url" id="logo_url" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Favicon URL</label>
                        <input type="url" id="favicon_url" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
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
                </div>
            </div>

            <!-- Operating Hours Tab -->
            <div id="tab-hours" class="tab-content hidden">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Monday</label>
                        <input type="text" id="monday_hours" placeholder="9:00 AM - 6:00 PM" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tuesday</label>
                        <input type="text" id="tuesday_hours" placeholder="9:00 AM - 6:00 PM" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Wednesday</label>
                        <input type="text" id="wednesday_hours" placeholder="9:00 AM - 6:00 PM" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Thursday</label>
                        <input type="text" id="thursday_hours" placeholder="9:00 AM - 6:00 PM" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Friday</label>
                        <input type="text" id="friday_hours" placeholder="9:00 AM - 6:00 PM" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Saturday</label>
                        <input type="text" id="saturday_hours" placeholder="9:00 AM - 6:00 PM" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sunday</label>
                        <input type="text" id="sunday_hours" placeholder="Closed" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>
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
