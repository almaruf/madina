// Admin Offers JS
let currentFilter = 'active';
let allOffers = [];
let currentOfferId = null;

async function loadOffers() {
    try {
        let url = '/api/admin/offers';
        if (currentFilter !== 'all') {
            url += `?status=${currentFilter}`;
        }
        const response = await axios.get(url);
        allOffers = response.data.data || response.data;
        renderOffers();
    } catch (error) {
        console.error('Error loading offers:', error);
        document.getElementById('offers-list').innerHTML = '<div class="col-span-full text-center text-red-600">Failed to load offers</div>';
    }
}

function renderOffers() {
    const container = document.getElementById('offers-list');
    
    if (allOffers.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center py-12 text-gray-600">No offers found</div>';
        return;
    }

    container.innerHTML = allOffers.map(offer => {
        const isValid = offer.is_active && (!offer.ends_at || new Date(offer.ends_at) > new Date());
        const statusClass = isValid ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
        const statusText = isValid ? 'Active' : offer.is_active ? 'Expired' : 'Inactive';

        return `
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">${offer.name}</h3>
                        <span class="text-xs font-medium px-2 py-1 rounded ${statusClass}">${statusText}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="window.editOffer(${offer.id})" class="text-blue-600 hover:text-blue-800" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="window.toggleOfferStatus(${offer.id})" class="text-gray-600 hover:text-gray-800" title="${offer.is_active ? 'Suspend' : 'Activate'}">
                            <i class="fas fa-${offer.is_active ? 'pause' : 'play'}-circle"></i>
                        </button>
                        <button onclick="window.deleteOffer(${offer.id})" class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                ${offer.badge_text ? `
                    <div class="mb-3">
                        <span class="inline-block px-3 py-1 rounded text-white text-sm font-bold" style="background-color: ${offer.badge_color}">
                            ${offer.badge_text}
                        </span>
                    </div>
                ` : ''}

                <p class="text-sm text-gray-600 mb-3">${offer.description || 'No description'}</p>

                <div class="text-sm text-gray-500 space-y-1">
                    <div><strong>Type:</strong> ${formatOfferType(offer.type)}</div>
                    <div><strong>Products:</strong> ${offer.products_count || 0}</div>
                    ${offer.starts_at ? `<div><strong>Starts:</strong> ${new Date(offer.starts_at).toLocaleDateString()}</div>` : ''}
                    ${offer.ends_at ? `<div><strong>Ends:</strong> ${new Date(offer.ends_at).toLocaleDateString()}</div>` : ''}
                    ${offer.current_usage_count ? `<div><strong>Used:</strong> ${offer.current_usage_count}${offer.total_usage_limit ? `/${offer.total_usage_limit}` : ''}</div>` : ''}
                </div>

                <div class="mt-4 pt-4 border-t">
                    <button onclick="window.manageProducts(${offer.id})" class="text-green-600 hover:text-green-700 text-sm font-medium">
                        <i class="fas fa-box"></i> Manage Products
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function formatOfferType(type) {
    const types = {
        'percentage_discount': 'Percentage Discount',
        'fixed_discount': 'Fixed Discount',
        'bxgy_free': 'Buy X Get Y Free',
        'multibuy': 'Multi-Buy Deal',
        'bxgy_discount': 'Buy X Get Y at Discount',
        'flash_sale': 'Flash Sale',
        'bundle': 'Bundle Deal'
    };
    return types[type] || type;
}

function filterOffers(status) {
    currentFilter = status;
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.remove('border-green-600', 'text-green-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    event.target.classList.remove('border-transparent', 'text-gray-500');
    event.target.classList.add('border-green-600', 'text-green-600');
    loadOffers();
}

function editOffer(id) {
    const offer = allOffers.find(o => o.id === id);
    if (!offer) {
        window.location.href = `/admin/offers/edit/percentage-discount?id=${id}`;
        return;
    }
    
    // Route to appropriate editor based on offer type
    if (offer.type === 'bxgy_free' || offer.type === 'bxgy_discount') {
        window.location.href = `/admin/offers/edit/bxgy?id=${id}`;
    } else if (offer.type === 'percentage_discount') {
        window.location.href = `/admin/offers/edit/percentage-discount?id=${id}`;
    } else {
        // Default to percentage-discount for other types
        window.location.href = `/admin/offers/edit/percentage-discount?id=${id}`;
    }
}

async function toggleOfferStatus(id) {
    try {
        await axios.post(`/api/admin/offers/${id}/toggle-status`);
        toast.success('Offer status updated');
        loadOffers();
    } catch (error) {
        console.error('Error toggling status:', error);
        toast.error('Failed to update offer status');
    }
}

async function deleteOffer(id) {
    if (!confirm('Are you sure you want to delete this offer? This action cannot be undone.')) return;
    
    try {
        await axios.delete(`/api/admin/offers/${id}`);
        toast.success('Offer deleted successfully');
        loadOffers();
    } catch (error) {
        console.error('Error deleting offer:', error);
        toast.error('Failed to delete offer');
    }
}

async function manageProducts(id) {
    currentOfferId = id;
    document.getElementById('products-modal').classList.remove('hidden');
    await loadAvailableProducts();
    await loadOfferProducts(id);
}

function closeProductsModal() {
    document.getElementById('products-modal').classList.add('hidden');
    currentOfferId = null;
}

async function loadAvailableProducts() {
    try {
        const response = await axios.get('/api/admin/products');
        const products = response.data.data;
        
        const html = products.map(product => `
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded border">
                <div class="flex items-center gap-3">
                    ${product.primary_image ? `<img src="${product.primary_image.url}" class="w-12 h-12 object-cover rounded">` : '<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                    <div>
                        <p class="font-medium text-sm">${product.name}</p>
                        <p class="text-xs text-gray-500">${product.default_variation?.price ? '£' + product.default_variation.price : ''}</p>
                    </div>
                </div>
                <button onclick="window.addProductToOffer(${product.id})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                    Add
                </button>
            </div>
        `).join('');
        
        document.getElementById('available-products').innerHTML = html || '<p class="text-gray-500 text-center py-4">No products available</p>';
    } catch (error) {
        console.error('Error loading products:', error);
        document.getElementById('available-products').innerHTML = '<p class="text-red-500 text-center py-4">Failed to load products</p>';
    }
}

async function loadOfferProducts(offerId) {
    try {
        const response = await axios.get(`/api/admin/offers/${offerId}/products`);
        const products = response.data.data;
        
        const html = products.map(product => `
            <div class="flex items-center justify-between p-3 bg-green-50 rounded border border-green-200">
                <div class="flex items-center gap-3">
                    ${product.primary_image ? `<img src="${product.primary_image.url}" class="w-12 h-12 object-cover rounded">` : '<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                    <div>
                        <p class="font-medium text-sm">${product.name}</p>
                        <p class="text-xs text-gray-500">${product.default_variation?.price ? '£' + product.default_variation.price : ''}</p>
                    </div>
                </div>
                <button onclick="window.removeProductFromOffer(${product.id})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                    Remove
                </button>
            </div>
        `).join('');
        
        document.getElementById('offer-products').innerHTML = html || '<p class="text-gray-500 text-center py-4">No products assigned</p>';
    } catch (error) {
        console.error('Error loading offer products:', error);
        document.getElementById('offer-products').innerHTML = '<p class="text-red-500 text-center py-4">Failed to load products</p>';
    }
}

async function addProductToOffer(productId) {
    try {
        await axios.post(`/api/admin/offers/${currentOfferId}/products`, { product_id: productId });
        await loadOfferProducts(currentOfferId);
        await loadAvailableProducts();
        toast.success('Product added to offer');
    } catch (error) {
        console.error('Error adding product:', error);
        toast.error('Failed to add product to offer');
    }
}

async function removeProductFromOffer(productId) {
    try {
        await axios.delete(`/api/admin/offers/${currentOfferId}/products/${productId}`);
        await loadOfferProducts(currentOfferId);
        await loadAvailableProducts();
        toast.success('Product removed from offer');
    } catch (error) {
        console.error('Error removing product:', error);
        toast.error('Failed to remove product from offer');
    }
}

// Expose functions to window for onclick handlers in rendered HTML
window.editOffer = editOffer;
window.toggleOfferStatus = toggleOfferStatus;
window.deleteOffer = deleteOffer;
window.manageProducts = manageProducts;
window.closeProductsModal = closeProductsModal;
window.addProductToOffer = addProductToOffer;
window.removeProductFromOffer = removeProductFromOffer;
window.filterOffers = filterOffers;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Filter tabs
    document.getElementById('filter-active').addEventListener('click', () => filterOffers('active'));
    document.getElementById('filter-inactive').addEventListener('click', () => filterOffers('inactive'));
    document.getElementById('filter-expired').addEventListener('click', () => filterOffers('expired'));
    
    // Modal close buttons
    document.getElementById('close-modal-top').addEventListener('click', closeProductsModal);
    document.getElementById('close-modal-bottom').addEventListener('click', closeProductsModal);
    
    loadOffers();
});
