// Buy X Get Y Offer Edit Page JavaScript
// Axios and toast are available globally via bootstrap.js and layout.js

// Helper function to get auth headers
function getAuthHeaders() {
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    return token ? { 'Authorization': `Bearer ${token}` } : {};
}

const offerId = new URLSearchParams(window.location.search).get('id');
let offerData = null;
let allProducts = [];
let allCategories = [];
let buyProducts = [];
let getProducts = [];
let currentModalType = 'buy'; // 'buy' or 'get'
let filteredProducts = [];

async function loadOffer() {
    try {
        const [offerRes, productsRes, categoriesRes] = await Promise.all([
            axios.get(`/api/admin/offers/${offerId}`, { headers: getAuthHeaders() }),
            axios.get('/api/admin/products?per_page=1000', { headers: getAuthHeaders() }),
            axios.get('/api/admin/categories', { headers: getAuthHeaders() })
        ]);

        offerData = offerRes.data;
        allProducts = productsRes.data.data || productsRes.data;
        allCategories = categoriesRes.data.data || categoriesRes.data;

        // Get buy and get products from the offer
        buyProducts = offerData.buy_products || offerData.products || [];
        getProducts = offerData.get_products || offerData.products || [];

        populateForm();
        loadCategorySelect();

        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('form-container').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading offer:', error);
        alert('Failed to load offer: ' + (error.response?.data?.message || error.message));
        window.location.href = '/admin/offers';
    }
}

function populateForm() {
    document.getElementById('name').value = offerData.name || '';
    document.getElementById('description').value = offerData.description || '';
    document.getElementById('buy_quantity').value = offerData.buy_quantity || 2;
    document.getElementById('get_quantity').value = offerData.get_quantity || 1;
    document.getElementById('badge_text').value = offerData.badge_text || '';
    document.getElementById('badge_color').value = offerData.badge_color || '#DC2626';
    document.getElementById('max_uses_per_customer').value = offerData.max_uses_per_customer || '';
    document.getElementById('total_usage_limit').value = offerData.total_usage_limit || '';
    document.getElementById('is_active').checked = offerData.is_active;

    // Display offer type
    const typeDisplay = offerData.type === 'bxgy_free' ? 'Buy X Get Y Free' : 'Buy X Get Y at Discount';
    document.getElementById('offer_type_display').value = typeDisplay;

    // Show discount field if bxgy_discount
    if (offerData.type === 'bxgy_discount') {
        document.getElementById('discount-field').classList.remove('hidden');
        document.getElementById('get_discount_percentage').value = offerData.get_discount_percentage || '';
    }

    if (offerData.starts_at) {
        document.getElementById('starts_at').value = new Date(offerData.starts_at).toISOString().slice(0, 16);
    }
    if (offerData.ends_at) {
        document.getElementById('ends_at').value = new Date(offerData.ends_at).toISOString().slice(0, 16);
    }

    // Check if buy and get products are the same
    const buyIds = buyProducts.map(p => p.id).sort().join(',');
    const getIds = getProducts.map(p => p.id).sort().join(',');
    if (buyIds === getIds && buyIds !== '') {
        document.getElementById('same_as_buy').checked = true;
        handleSameAsBuyChange();
    }

    renderProductSelection('buy');
    renderProductSelection('get');
}

function loadCategorySelect() {
    const filterSelect = document.getElementById('product-category-filter');
    allCategories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.name;
        filterSelect.appendChild(option);
    });
}

function handleSameAsBuyChange() {
    const sameAsBuy = document.getElementById('same_as_buy').checked;
    const getProductSelection = document.getElementById('get-product-selection');
    
    if (sameAsBuy) {
        getProductSelection.style.opacity = '0.5';
        getProductSelection.style.pointerEvents = 'none';
        // Copy buy products to get products
        getProducts = [...buyProducts];
        renderProductSelection('get');
    } else {
        getProductSelection.style.opacity = '1';
        getProductSelection.style.pointerEvents = 'auto';
    }
}

function openProductModal(type) {
    currentModalType = type;
    const modal = document.getElementById('product-modal');
    const title = document.getElementById('modal-title');
    const subtitle = document.getElementById('modal-subtitle');
    
    if (type === 'buy') {
        title.textContent = 'Select Products to Buy';
        subtitle.textContent = 'Choose which products customers must purchase';
    } else {
        title.textContent = 'Select Products to Get';
        subtitle.textContent = 'Choose which products customers will receive';
    }
    
    modal.classList.remove('hidden');
    searchProducts();
    updateSelectionCount();
}

function closeProductModal() {
    document.getElementById('product-modal').classList.add('hidden');
}

function searchProducts() {
    const searchTerm = document.getElementById('product-search').value.toLowerCase();
    const categoryFilter = document.getElementById('product-category-filter').value;
    
    filteredProducts = allProducts.filter(product => {
        const matchesSearch = !searchTerm || 
            product.name.toLowerCase().includes(searchTerm) ||
            (product.description && product.description.toLowerCase().includes(searchTerm));
        
        const matchesCategory = !categoryFilter || 
            (product.categories && product.categories.some(cat => cat.id == categoryFilter));
        
        return matchesSearch && matchesCategory;
    });
    
    renderProductsList();
}

function renderProductsList() {
    const container = document.getElementById('products-list');
    const targetProducts = currentModalType === 'buy' ? buyProducts : getProducts;
    const selectedProductIds = targetProducts.map(p => p.id);
    
    if (filteredProducts.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-8">No products found</p>';
        return;
    }
    
    container.innerHTML = filteredProducts.map(product => {
        const isSelected = selectedProductIds.includes(product.id);
        const price = product.default_variation?.price || product.variations?.[0]?.price;
        
        return `
            <label class="flex items-center gap-4 p-3 rounded-lg border-2 cursor-pointer hover:bg-gray-50 transition ${isSelected ? 'border-green-500 bg-green-50' : 'border-gray-200'}">
                <input type="checkbox" 
                    class="w-5 h-5" 
                    ${isSelected ? 'checked' : ''}
                    onchange="window.toggleProductSelection(${product.id})"
                    data-product-id="${product.id}">
                
                ${product.primary_image ? 
                    `<img src="${product.primary_image.url}" class="w-16 h-16 object-cover rounded">` : 
                    '<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                
                <div class="flex-1">
                    <p class="font-medium text-gray-900">${product.name}</p>
                    <p class="text-sm text-gray-600">${price ? '£' + price : 'N/A'}</p>
                    ${product.categories && product.categories.length > 0 ? 
                        `<p class="text-xs text-gray-500 mt-1">${product.categories.map(c => c.name).join(', ')}</p>` : ''}
                </div>
            </label>
        `;
    }).join('');
}

function toggleProductSelection(productId) {
    const product = allProducts.find(p => p.id === productId);
    if (!product) return;
    
    const targetArray = currentModalType === 'buy' ? buyProducts : getProducts;
    const index = targetArray.findIndex(p => p.id === productId);
    
    if (index > -1) {
        targetArray.splice(index, 1);
    } else {
        targetArray.push(product);
    }
    
    updateSelectionCount();
}

function selectAllVisibleProducts() {
    const targetArray = currentModalType === 'buy' ? buyProducts : getProducts;
    
    filteredProducts.forEach(product => {
        if (!targetArray.find(p => p.id === product.id)) {
            targetArray.push(product);
        }
    });
    
    renderProductsList();
    updateSelectionCount();
}

function clearCurrentSelection() {
    if (currentModalType === 'buy') {
        buyProducts = [];
    } else {
        getProducts = [];
    }
    
    renderProductsList();
    updateSelectionCount();
}

function updateSelectionCount() {
    const count = currentModalType === 'buy' ? buyProducts.length : getProducts.length;
    document.getElementById('selection-count').textContent = count;
}

function confirmProductSelection() {
    renderProductSelection(currentModalType);
    closeProductModal();
    
    // If "same as buy" is checked and we just updated buy products, update get products too
    if (document.getElementById('same_as_buy').checked && currentModalType === 'buy') {
        getProducts = [...buyProducts];
        renderProductSelection('get');
    }
}

function renderProductSelection(type) {
    const products = type === 'buy' ? buyProducts : getProducts;
    const container = document.getElementById(`${type}-products-display`);
    
    if (products.length === 0) {
        container.innerHTML = '<p class="text-gray-500">No products selected. Click "Manage" to select products.</p>';
        return;
    }
    
    container.innerHTML = `
        <div class="text-sm font-semibold text-gray-700 mb-2">${products.length} product(s) selected</div>
        <div class="grid md:grid-cols-2 gap-3">
            ${products.map(product => {
                const price = product.default_variation?.price || product.variations?.[0]?.price;
                return `
                    <div class="flex items-center gap-3 p-3 border rounded-lg bg-gray-50">
                        ${product.primary_image ? 
                            `<img src="${product.primary_image.url}" class="w-12 h-12 object-cover rounded">` : 
                            '<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                        
                        <div class="flex-1">
                            <p class="font-medium text-sm">${product.name}</p>
                            <p class="text-xs text-gray-600">${price ? '£' + price : 'N/A'}</p>
                        </div>
                        
                        <button type="button" onclick="window.removeProduct('${type}', ${product.id})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }).join('')}
        </div>
    `;
}

function removeProduct(type, productId) {
    if (type === 'buy') {
        buyProducts = buyProducts.filter(p => p.id !== productId);
    } else {
        getProducts = getProducts.filter(p => p.id !== productId);
    }
    renderProductSelection(type);
}

async function handleSubmit(event) {
    event.preventDefault();
    
    // Validation
    if (buyProducts.length === 0) {
        alert('Please select at least one "Buy" product');
        return;
    }
    
    const sameAsBuy = document.getElementById('same_as_buy').checked;
    if (!sameAsBuy && getProducts.length === 0) {
        alert('Please select at least one "Get" product or check "Same as Buy products"');
        return;
    }
    
    const formData = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value,
        buy_quantity: parseInt(document.getElementById('buy_quantity').value),
        get_quantity: parseInt(document.getElementById('get_quantity').value),
        badge_text: document.getElementById('badge_text').value,
        badge_color: document.getElementById('badge_color').value,
        starts_at: document.getElementById('starts_at').value || null,
        ends_at: document.getElementById('ends_at').value || null,
        max_uses_per_customer: document.getElementById('max_uses_per_customer').value || null,
        total_usage_limit: document.getElementById('total_usage_limit').value || null,
        is_active: document.getElementById('is_active').checked,
        buy_product_ids: buyProducts.map(p => p.id),
        get_product_ids: sameAsBuy ? buyProducts.map(p => p.id) : getProducts.map(p => p.id)
    };
    
    if (offerData.type === 'bxgy_discount') {
        formData.get_discount_percentage = parseFloat(document.getElementById('get_discount_percentage').value);
    }
    
    try {
        await axios.put(`/api/admin/offers/${offerId}`, formData, {
            headers: getAuthHeaders()
        });
        
        alert('Offer updated successfully!');
        window.location.href = '/admin/offers';
    } catch (error) {
        console.error('Error updating offer:', error);
        if (error.response?.data?.errors) {
            const errors = Object.values(error.response.data.errors).flat();
            alert('Validation errors:\n' + errors.join('\n'));
        } else {
            alert('Failed to update offer: ' + (error.response?.data?.message || error.message));
        }
    }
}

async function deleteOffer() {
    if (!confirm('Are you sure you want to delete this offer? This action cannot be undone.')) return;
    
    try {
        await axios.delete(`/api/admin/offers/${offerId}`, {
            headers: getAuthHeaders()
        });
        alert('Offer deleted successfully');
        window.location.href = '/admin/offers';
    } catch (error) {
        console.error('Error deleting offer:', error);
        alert('Failed to delete offer');
    }
}

// Expose functions needed by inline handlers
window.handleSameAsBuyChange = handleSameAsBuyChange;
window.openProductModal = openProductModal;
window.closeProductModal = closeProductModal;
window.searchProducts = searchProducts;
window.toggleProductSelection = toggleProductSelection;
window.selectAllVisibleProducts = selectAllVisibleProducts;
window.clearCurrentSelection = clearCurrentSelection;
window.confirmProductSelection = confirmProductSelection;
window.removeProduct = removeProduct;
window.handleSubmit = handleSubmit;
window.deleteOffer = deleteOffer;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadOffer();
});
