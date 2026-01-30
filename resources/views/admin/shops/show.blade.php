@extends('admin.layout')

@section('title', 'Shop Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/admin/shops" class="text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Shop Details</h1>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
        <p class="mt-2 text-gray-600">Loading shop details...</p>
    </div>

    <!-- Error State -->
    <div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <p class="text-red-800"></p>
    </div>

    <!-- Shop Details -->
    <div id="shop-details" class="hidden">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- Shop Header -->
            <div id="shop-header" class="p-6 border-b"></div>

            <!-- Shop Info Grid -->
            <div id="shop-info" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6"></div>

            <!-- Configuration Section -->
            <div id="shop-config" class="p-6 border-t"></div>

            <!-- Actions -->
            <div id="shop-actions" class="p-6 bg-gray-50 border-t flex gap-4"></div>
        </div>
    </div>
</div>

<script>
    const shopId = {{ request()->route('id') }};
    let currentShop = null;

    async function loadShop() {
        try {
            const response = await axios.get(`/api/admin/shops/${shopId}`);
            currentShop = response.data;
            renderShop(currentShop);
        } catch (error) {
            console.error('Error loading shop:', error);
            document.getElementById('loading').classList.add('hidden');
            const errorDiv = document.getElementById('error');
            errorDiv.classList.remove('hidden');
            errorDiv.querySelector('p').textContent = error.response?.data?.message || 'Failed to load shop details';
        }
    }

    function renderShop(shop) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('shop-details').classList.remove('hidden');

        const isArchived = shop.deleted_at !== null;

        // Header
        document.getElementById('shop-header').innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">${shop.name}</h2>
                    <p class="text-gray-600 mt-1">${shop.slug}</p>
                    ${isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>' : ''}
                    ${shop.is_active && !isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>' : ''}
                    ${!shop.is_active && !isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Inactive</span>' : ''}
                </div>
            </div>
        `;

        // Info
        document.getElementById('shop-info').innerHTML = `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Shop Name</h3>
                <p class="text-lg text-gray-900">${shop.name}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Domain</h3>
                <p class="text-lg text-gray-900">${shop.domain || 'N/A'}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Phone</h3>
                <p class="text-lg text-gray-900">${shop.phone}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Email</h3>
                <p class="text-lg text-gray-900">${shop.email}</p>
            </div>
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Address</h3>
                <p class="text-gray-900">
                    ${shop.address_line_1 || 'N/A'}<br>
                    ${shop.city || ''}, ${shop.postcode || ''}<br>
                    ${shop.country || 'United Kingdom'}
                </p>
            </div>
            ${shop.description ? `
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                    <p class="text-gray-900">${shop.description}</p>
                </div>
            ` : ''}
        `;

        // Configuration
        document.getElementById('shop-config').innerHTML = `
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Configuration</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Currency</p>
                    <p class="text-gray-900 font-medium">${shop.currency} (${shop.currency_symbol})</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Delivery Fee</p>
                    <p class="text-gray-900 font-medium">${shop.currency_symbol}${parseFloat(shop.delivery_fee || 0).toFixed(2)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Min Order Amount</p>
                    <p class="text-gray-900 font-medium">${shop.currency_symbol}${parseFloat(shop.min_order_amount || 0).toFixed(2)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Halal Products</p>
                    <p class="text-gray-900 font-medium">${shop.has_halal_products ? 'Yes' : 'No'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Specialization</p>
                    <p class="text-gray-900 font-medium">${shop.specialization || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Created</p>
                    <p class="text-gray-900 font-medium">${new Date(shop.created_at).toLocaleDateString()}</p>
                </div>
            </div>
        `;

        // Actions
        const actionsHtml = [];
        
        if (!isArchived) {
            actionsHtml.push(`
                <a href="/admin/shops/${shop.id}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Shop
                </a>
                <button onclick="toggleShopStatus()" class="px-4 py-2 ${shop.is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700'} text-white rounded-lg">
                    ${shop.is_active ? 'Deactivate' : 'Activate'} Shop
                </button>
                <button onclick="archiveShop()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Archive Shop
                </button>
            `);
        } else {
            actionsHtml.push(`
                <button onclick="restoreShop()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Restore Shop
                </button>
                <button onclick="permanentlyDeleteShop()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Permanently Delete
                </button>
            `);
        }

        document.getElementById('shop-actions').innerHTML = actionsHtml.join('');
    }

    async function toggleShopStatus() {
        const newStatus = !currentShop.is_active;
        const action = newStatus ? 'activate' : 'deactivate';
        
        if (!confirm(`Are you sure you want to ${action} this shop?`)) {
            return;
        }

        try {
            await axios.patch(`/api/admin/shops/${shopId}`, { is_active: newStatus });
            toast.success(`Shop ${action}d successfully!`);
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            console.error('Error updating shop status:', error);
            toast.error(error.response?.data?.message || 'Failed to update shop status');
        }
    }

    async function archiveShop() {
        if (!confirm('Are you sure you want to archive this shop? All shop data will be hidden but preserved.')) {
            return;
        }

        try {
            await axios.delete(`/api/admin/shops/${shopId}`);
            toast.success('Shop archived successfully!');
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            console.error('Error archiving shop:', error);
            toast.error(error.response?.data?.message || 'Failed to archive shop');
        }
    }

    async function restoreShop() {
        try {
            await axios.post(`/api/admin/shops/${shopId}/restore`);
            toast.success('Shop restored successfully!');
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            console.error('Error restoring shop:', error);
            toast.error(error.response?.data?.message || 'Failed to restore shop');
        }
    }

    async function permanentlyDeleteShop() {
        if (!confirm('Are you sure you want to PERMANENTLY delete this shop? This action cannot be undone and will delete all associated data!')) {
            return;
        }

        const confirmText = prompt('Type "DELETE" to confirm permanent deletion:');
        if (confirmText !== 'DELETE') {
            toast.warning('Deletion cancelled');
            return;
        }

        try {
            await axios.delete(`/api/admin/shops/${shopId}/force`);
            toast.success('Shop permanently deleted!');
            setTimeout(() => window.location.href = '/admin/shops', 1000);
        } catch (error) {
            console.error('Error deleting shop:', error);
            toast.error(error.response?.data?.message || 'Failed to delete shop');
        }
    }

    // Load shop on page load
    loadShop();
</script>
@endsection
