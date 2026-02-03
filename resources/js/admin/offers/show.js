// Offer Show Page JavaScript
const offerId = document.querySelector('[data-offer-id]')?.dataset.offerId;
let currentOfferId = offerId; // For modal functions
let offerData = null;

// Load offer details
async function loadOffer() {
    if (!offerId) {
        showError('Offer ID not found');
        return;
    }

    try {
        const response = await axios.get(`/api/admin/offers/${offerId}`);
        offerData = response.data;
        
        // Load products with categories if they exist
        if (offerData.products && offerData.products.length > 0) {
            const productIds = offerData.products.map(p => p.id);
            const productsResponse = await axios.get('/api/admin/products', {
                params: { ids: productIds.join(',') }
            });
            // Merge categories into products
            const productsWithCategories = productsResponse.data.data || productsResponse.data;
            offerData.products = offerData.products.map(p => {
                const fullProduct = productsWithCategories.find(fp => fp.id === p.id);
                return { ...p, categories: fullProduct?.categories || [] };
            });
        }
        
        renderOffer();
        hideLoading();
    } catch (error) {
        console.error('Error loading offer:', error);
        showError(error.response?.data?.message || 'Failed to load offer details');
    }
}

function renderOffer() {
    const isValid = offerData.is_active && (!offerData.ends_at || new Date(offerData.ends_at) > new Date());
    const statusClass = isValid ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
    const statusText = isValid ? 'Active' : offerData.is_active ? 'Expired' : 'Inactive';

    // Header
    document.getElementById('offer-header').innerHTML = `
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">${offerData.name}</h2>
                <span class="inline-block px-3 py-1 rounded text-sm font-medium ${statusClass}">${statusText}</span>
                ${offerData.badge_text ? `
                    <span class="inline-block ml-2 px-3 py-1 rounded text-white text-sm font-bold" style="background-color: ${offerData.badge_color}">
                        ${offerData.badge_text}
                    </span>
                ` : ''}
            </div>
        </div>
        ${offerData.description ? `<p class="mt-4 text-gray-700">${offerData.description}</p>` : ''}
    `;

    // Info
    document.getElementById('offer-info').innerHTML = `
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Type</h3>
            <p class="text-lg text-gray-900">${formatOfferType(offerData.type)}</p>
        </div>
        ${offerData.discount_value ? `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Discount Value</h3>
                <p class="text-lg text-gray-900">${offerData.discount_value}${offerData.type.includes('percentage') ? '%' : '£'}</p>
            </div>
        ` : ''}
        ${offerData.buy_quantity ? `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Buy Quantity</h3>
                <p class="text-lg text-gray-900">${offerData.buy_quantity}</p>
            </div>
        ` : ''}
        ${offerData.get_quantity ? `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Get Quantity</h3>
                <p class="text-lg text-gray-900">${offerData.get_quantity}</p>
            </div>
        ` : ''}
        ${offerData.starts_at ? `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Start Date</h3>
                <p class="text-gray-900">${new Date(offerData.starts_at).toLocaleString()}</p>
            </div>
        ` : ''}
        ${offerData.ends_at ? `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">End Date</h3>
                <p class="text-gray-900">${new Date(offerData.ends_at).toLocaleString()}</p>
            </div>
        ` : ''}
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Priority</h3>
            <p class="text-gray-900">${offerData.priority || 0}</p>
        </div>
        ${offerData.total_usage_limit ? `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Usage</h3>
                <p class="text-gray-900">${offerData.current_usage_count || 0} / ${offerData.total_usage_limit}</p>
            </div>
        ` : ''}
    `;

    // Products section
    renderProductsSection();

    // Actions
    const actionsHtml = `
        <button id="edit-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-edit"></i> Edit Offer
        </button>
        <button id="manage-products-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-box"></i> Manage Products
        </button>
        <button id="toggle-status-btn" class="px-4 py-2 ${offerData.is_active ? 'bg-yellow-600' : 'bg-green-600'} text-white rounded-lg hover:${offerData.is_active ? 'bg-yellow-700' : 'bg-green-700'}">
            <i class="fas fa-${offerData.is_active ? 'pause' : 'play'}-circle"></i> ${offerData.is_active ? 'Suspend' : 'Activate'}
        </button>
        <button id="delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            <i class="fas fa-trash"></i> Delete
        </button>
    `;
    
    document.getElementById('offer-actions').innerHTML = actionsHtml;
    attachEventListeners();
}

function renderProductsSection() {
    const products = offerData.products || [];
    const productsCount = products.length;
    
    let html = `
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">Associated Products</h2>
                <p class="text-sm text-gray-600">${productsCount} product${productsCount !== 1 ? 's' : ''}</p>
            </div>
        </div>
    `;
    
    if (products.length > 0) {
        html += `
            <div class="space-y-2">
                ${products.map(product => {
                    const defaultVariation = product.variations?.find(v => v.is_default) || product.variations?.[0];
                    const price = defaultVariation ? `£${parseFloat(defaultVariation.price).toFixed(2)}` : 'N/A';
                    const imageUrl = product.primary_image?.url;
                    
                    return `
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer" onclick="window.location='/admin/products/${product.slug}'">
                            <div class="flex-shrink-0">
                                ${imageUrl 
                                    ? `<img src="${imageUrl}" class="w-16 h-16 object-cover rounded" alt="${product.name}">`
                                    : '<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'
                                }
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">${product.name}</p>
                                <p class="text-sm text-gray-500">${product.categories?.map(c => c.name).join(', ') || 'Uncategorized'}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="font-semibold text-gray-900">${price}</p>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    } else {
        html += `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-2"></i>
                <p>No products associated with this offer</p>
            </div>
        `;
    }
    
    document.getElementById('products-section').innerHTML = html;
}

function attachEventListeners() {
    document.getElementById('edit-btn')?.addEventListener('click', editOffer);
    document.getElementById('manage-products-btn')?.addEventListener('click', manageProducts);
    document.getElementById('toggle-status-btn')?.addEventListener('click', toggleStatus);
    document.getElementById('delete-btn')?.addEventListener('click', deleteOffer);
}

function editOffer() {
    // Route to appropriate editor based on offer type
    if (offerData.type === 'bxgy_free' || offerData.type === 'bxgy_discount') {
        window.location.href = `/admin/offers/edit/bxgy?id=${offerId}`;
    } else if (offerData.type === 'percentage_discount') {
        window.location.href = `/admin/offers/edit/percentage-discount?id=${offerId}`;
    } else {
        window.location.href = `/admin/offers/edit/percentage-discount?id=${offerId}`;
    }
}

function manageProducts() {
    document.getElementById('products-modal').classList.remove('hidden');
    loadAvailableProducts();
    loadOfferProducts(currentOfferId);
}

function closeProductsModal() {
    document.getElementById('products-modal').classList.add('hidden');
}

async function loadAvailableProducts() {
    try {
        const response = await axios.get('/api/admin/products');
        const allProducts = response.data.data;
        
        // Get current offer products to filter them out
        const offerResponse = await axios.get(`/api/admin/offers/${currentOfferId}/products`);
        const offerProductIds = offerResponse.data.data.map(p => p.id);
        
        // Filter out products that are already in the offer
        const availableProducts = allProducts.filter(product => !offerProductIds.includes(product.id));
        
        const html = availableProducts.map(product => `
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
        await loadOffer(); // Refresh the main offer display
        window.toast.success('Product added to offer');
    } catch (error) {
        console.error('Error adding product:', error);
        window.toast.error('Failed to add product to offer');
    }
}

async function removeProductFromOffer(productId) {
    try {
        await axios.delete(`/api/admin/offers/${currentOfferId}/products/${productId}`);
        await loadOfferProducts(currentOfferId);
        await loadAvailableProducts();
        await loadOffer(); // Refresh the main offer display
        window.toast.success('Product removed from offer');
    } catch (error) {
        console.error('Error removing product:', error);
        window.toast.error('Failed to remove product from offer');
    }
}

async function toggleStatus() {
    try {
        await axios.post(`/api/admin/offers/${offerId}/toggle-status`);
        window.toast.success('Offer status updated');
        loadOffer(); // Reload to show updated status
    } catch (error) {
        console.error('Error toggling status:', error);
        window.toast.error('Failed to update offer status');
    }
}

async function deleteOffer() {
    if (!confirm('Are you sure you want to delete this offer? This action cannot be undone.')) return;
    
    try {
        await axios.delete(`/api/admin/offers/${offerId}`);
        window.toast.success('Offer deleted successfully');
        setTimeout(() => window.location.href = '/admin/offers', 1500);
    } catch (error) {
        console.error('Error deleting offer:', error);
        window.toast.error('Failed to delete offer');
    }
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

function hideLoading() {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('offer-details').classList.remove('hidden');
}

function showError(message) {
    document.getElementById('loading').classList.add('hidden');
    const errorDiv = document.getElementById('error');
    errorDiv.querySelector('p').textContent = message;
    errorDiv.classList.remove('hidden');
}

// Expose functions to window for onclick handlers
window.addProductToOffer = addProductToOffer;
window.removeProductFromOffer = removeProductFromOffer;

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Modal close buttons
    document.getElementById('close-modal-top').addEventListener('click', closeProductsModal);
    document.getElementById('close-modal-bottom').addEventListener('click', closeProductsModal);
    
    loadOffer();
});
