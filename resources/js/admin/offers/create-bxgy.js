// Buy X Get Y Offer Create Page JavaScript
// Axios and toast are available globally via bootstrap.js and layout.js

// Helper function to get auth headers
function getAuthHeaders() {
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    return token ? { 'Authorization': `Bearer ${token}` } : {};
}

let allProducts = [];
let allCategories = [];
let buyProducts = [];
let getProducts = [];
let currentModalType = 'buy'; // 'buy' or 'get'
let filteredProducts = [];

// Load initial data
async function loadInitialData() {
    try {
        const [productsRes, categoriesRes] = await Promise.all([
            axios.get('/api/admin/products', { headers: getAuthHeaders() }),
            axios.get('/api/admin/categories', { headers: getAuthHeaders() })
        ]);

        allProducts = productsRes.data.data || productsRes.data;
        allCategories = categoriesRes.data.data || categoriesRes.data;

        // Populate category filter
        const categorySelect = document.getElementById('product-category-filter');
        allCategories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            categorySelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading data:', error);
        window.toast.error('Failed to load products and categories');
    }
}

function selectOfferType(type) {
    document.querySelectorAll('input[name="offer_type"]').forEach(radio => {
        radio.checked = radio.value === type;
    });
    handleOfferTypeChange();
}

function handleOfferTypeChange() {
    const offerType = document.querySelector('input[name="offer_type"]:checked').value;
    const discountField = document.getElementById('discount-field');
    
    if (offerType === 'bxgy_discount') {
        discountField.classList.remove('hidden');
        document.getElementById('get_discount_percentage').required = true;
    } else {
        discountField.classList.add('hidden');
        document.getElementById('get_discount_percentage').required = false;
    }
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
    const selectedProductIds = currentModalType === 'buy' ? 
        buyProducts.map(p => p.id) : 
        getProducts.map(p => p.id);
    
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
        container.innerHTML = '<p class="text-gray-500">No products selected. Click "Add" to select products.</p>';
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
        window.toast.error('Please select at least one "Buy" product');
        return;
    }
    
    const sameAsBuy = document.getElementById('same_as_buy').checked;
    if (!sameAsBuy && getProducts.length === 0) {
        window.toast.error('Please select at least one "Get" product or check "Same as Buy products"');
        return;
    }
    
    const offerType = document.querySelector('input[name="offer_type"]:checked').value;
    
    const formData = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value,
        type: offerType,
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
    
    if (offerType === 'bxgy_discount') {
        formData.get_discount_percentage = parseFloat(document.getElementById('get_discount_percentage').value);
    }
    
    try {
        await axios.post('/api/admin/offers/bxgy', formData, {
            headers: getAuthHeaders()
        });
        
        window.toast.success('Offer created successfully!');
        window.location.href = '/admin/offers';
    } catch (error) {
        console.error('Error creating offer:', error);
        if (error.response?.data?.errors) {
            const errors = Object.values(error.response.data.errors).flat();
            window.toast.error('Validation errors:\n' + errors.join('\n'));
        } else {
            window.toast.error('Failed to create offer: ' + (error.response?.data?.message || error.message));
        }
    }
}

// Expose functions needed by inline handlers
window.selectOfferType = selectOfferType;
window.handleOfferTypeChange = handleOfferTypeChange;
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

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadInitialData();
});
