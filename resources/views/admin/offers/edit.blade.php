@extends('admin.layout')

@section('title', 'Edit Offer')
@section('page-title', 'Edit Offer')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Edit Offer</h2>
                <a href="/admin/offers" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <div id="loading-container" class="p-12 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Loading offer...</p>
        </div>

        <form id="offer-form" class="p-6 space-y-6 hidden">
            <input type="hidden" id="offer-id">
            
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
                        <input type="checkbox" id="is-active" class="w-4 h-4">
                        <span class="text-sm font-medium">Active</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t">
                <button type="button" onclick="deleteOffer()" class="px-6 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 font-medium flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    Delete Offer
                </button>
                <div class="flex gap-3">
                    <a href="/admin/offers" class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-medium flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Update Offer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// No global token or axios config here. Always pass token in headers per request.
// Get offer ID from URL
const urlParams = new URLSearchParams(window.location.search);
const offerId = urlParams.get('id') || window.location.pathname.split('/').pop();

function updateFormFields() {
    const type = document.getElementById('offer-type').value;
    
    // Hide all dynamic fields
    document.getElementById('field-discount-value').classList.add('hidden');
    document.getElementById('field-bxgy').classList.add('hidden');
    document.getElementById('field-get-discount').classList.add('hidden');
    document.getElementById('field-bundle-price').classList.add('hidden');
    
    // Show relevant fields based on type
    switch(type) {
        case 'percentage_discount':
        case 'fixed_discount':
        case 'flash_sale':
            document.getElementById('field-discount-value').classList.remove('hidden');
            break;
        case 'bxgy_free':
            document.getElementById('field-bxgy').classList.remove('hidden');
            break;
        case 'multibuy':
            document.getElementById('field-bxgy').classList.remove('hidden');
            document.getElementById('field-bundle-price').classList.remove('hidden');
            break;
        case 'bxgy_discount':
            document.getElementById('field-bxgy').classList.remove('hidden');
            document.getElementById('field-get-discount').classList.remove('hidden');
            break;
        case 'bundle':
            document.getElementById('field-bundle-price').classList.remove('hidden');
            break;
    }
}

async function loadOffer() {
    try {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        const response = await axios.get(`/api/admin/offers/${offerId}`,
            { headers: { 'Authorization': `Bearer ${token}` } });
        const offer = response.data; // API returns offer directly, not wrapped
        
        document.getElementById('offer-id').value = offer.id;
        document.getElementById('offer-name').value = offer.name;
        document.getElementById('offer-description').value = offer.description || '';
        document.getElementById('offer-type').value = offer.type;
        document.getElementById('discount-value').value = offer.discount_value || '';
        document.getElementById('buy-quantity').value = offer.buy_quantity || '';
        document.getElementById('get-quantity').value = offer.get_quantity || '';
        document.getElementById('get-discount-percentage').value = offer.get_discount_percentage || '';
        document.getElementById('bundle-price').value = offer.bundle_price || '';
        document.getElementById('badge-text').value = offer.badge_text || '';
        document.getElementById('badge-color').value = offer.badge_color || '#DC2626';
        document.getElementById('min-purchase-amount').value = offer.min_purchase_amount || '';
        document.getElementById('max-uses-per-customer').value = offer.max_uses_per_customer || '';
        document.getElementById('total-usage-limit').value = offer.total_usage_limit || '';
        document.getElementById('priority').value = offer.priority || 0;
        document.getElementById('is-active').checked = offer.is_active;
        
        // Format dates for datetime-local input
        if (offer.starts_at) {
            document.getElementById('starts-at').value = new Date(offer.starts_at).toISOString().slice(0, 16);
        }
        if (offer.ends_at) {
            document.getElementById('ends-at').value = new Date(offer.ends_at).toISOString().slice(0, 16);
        }
        
        updateFormFields();
        
        document.getElementById('loading-container').classList.add('hidden');
        document.getElementById('offer-form').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading offer:', error);
        const message = error.response?.data?.message || error.message;
        toast.error('Failed to load offer: ' + message);
        document.getElementById('loading-container').classList.add('hidden');
        document.getElementById('offer-form').classList.remove('hidden');
    }
}

document.getElementById('offer-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('offer-name').value,
        description: document.getElementById('offer-description').value,
        type: document.getElementById('offer-type').value,
        discount_value: document.getElementById('discount-value').value || null,
        buy_quantity: document.getElementById('buy-quantity').value || null,
        get_quantity: document.getElementById('get-quantity').value || null,
        get_discount_percentage: document.getElementById('get-discount-percentage').value || null,
        bundle_price: document.getElementById('bundle-price').value || null,
        starts_at: document.getElementById('starts-at').value || null,
        ends_at: document.getElementById('ends-at').value || null,
        badge_text: document.getElementById('badge-text').value || null,
        badge_color: document.getElementById('badge-color').value,
        min_purchase_amount: document.getElementById('min-purchase-amount').value || null,
        max_uses_per_customer: document.getElementById('max-uses-per-customer').value || null,
        total_usage_limit: document.getElementById('total-usage-limit').value || null,
        priority: document.getElementById('priority').value || 0,
        is_active: document.getElementById('is-active').checked,
    };

    try {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        await axios.put(`/api/admin/offers/${offerId}`, data, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        toast.success('Offer updated successfully!');
        setTimeout(() => {
            window.location.href = '/admin/offers';
        }, 1000);
    } catch (error) {
        console.error('Error updating offer:', error);
        const message = error.response?.data?.message || error.message;
        toast.error('Failed to update offer: ' + message);
    }
});

async function deleteOffer() {
    if (!confirm('Are you sure you want to delete this offer? This action cannot be undone.')) {
        return;
    }
    
    try {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        await axios.delete(`/api/admin/offers/${offerId}`,
            { headers: { 'Authorization': `Bearer ${token}` } });
        toast.success('Offer deleted successfully!');
        setTimeout(() => {
            window.location.href = '/admin/offers';
        }, 1000);
    } catch (error) {
        console.error('Error deleting offer:', error);
        toast.error('Failed to delete offer');
    }
}

// Load offer data on page load
loadOffer();
</script>
@endsection
