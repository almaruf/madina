// Percentage Discount Offer Edit Page JavaScript
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
let selectedProducts = [];

async function loadOffer() {
    try {
        const [offerRes, categoriesRes] = await Promise.all([
            axios.get(`/api/admin/offers/${offerId}`, { headers: getAuthHeaders() }),
            axios.get('/api/admin/categories', { headers: getAuthHeaders() })
        ]);
        
        offerData = offerRes.data;
        allCategories = categoriesRes.data.data || categoriesRes.data;
        selectedProducts = (offerData.products || []).map(p => p.id);
        
        populateForm();
        loadCategorySelect();
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('form-container').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading offer:', error);
        window.toast.error('Failed to load offer: ' + (error.response?.data?.message || error.message));
    }
}

function populateForm() {
    const setIfExists = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.value = value || '';
    };

    const setCheckedIfExists = (id, checked) => {
        const el = document.getElementById(id);
        if (el) el.checked = checked;
    };

    setIfExists('name', offerData.name);
    setIfExists('description', offerData.description);
    setIfExists('badge_text', offerData.badge_text);
    setIfExists('badge_color', offerData.badge_color || '#DC2626');
    setIfExists('discount_value', offerData.discount_value);
    setIfExists('min_purchase_amount', offerData.min_purchase_amount);
    setIfExists('max_uses_per_customer', offerData.max_uses_per_customer);
    setIfExists('total_usage_limit', offerData.total_usage_limit);
    setCheckedIfExists('is_active', offerData.is_active);
    
    if (offerData.starts_at) {
        setIfExists('starts_at', new Date(offerData.starts_at).toISOString().slice(0, 16));
    }
    if (offerData.ends_at) {
        setIfExists('ends_at', new Date(offerData.ends_at).toISOString().slice(0, 16));
    }

    // Determine scope from offer
    if (offerData.scope) {
        const scopeRadio = document.querySelector(`input[name="product-scope"][value="${offerData.scope}"]`);
        if (scopeRadio) {
            scopeRadio.checked = true;
            
            if (offerData.scope === 'category' && offerData.category_id) {
                const categorySelect = document.getElementById('selected-category');
                if (categorySelect) categorySelect.value = offerData.category_id;
            }
        }
    } else {
        // Default to 'all' if no scope specified
        const allRadio = document.querySelector('input[name="product-scope"][value="all"]');
        if (allRadio) allRadio.checked = true;
    }

    // Call after scope is set
    handleProductScopeChange();
    renderSelectedProducts();
}

function loadCategorySelect() {
    const select = document.getElementById('selected-category');
    const filterSelect = document.getElementById('product-category-filter');
    
    allCategories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.name;
        select.appendChild(option);
        
        const filterOption = document.createElement('option');
        filterOption.value = cat.id;
        filterOption.textContent = cat.name;
        filterSelect.appendChild(filterOption);
    });
}

function handleProductScopeChange() {
    const scopeRadio = document.querySelector('input[name="product-scope"]:checked');
    if (!scopeRadio) return;
    
    const scope = scopeRadio.value;
    const categorySelection = document.getElementById('category-selection');
    const productSelection = document.getElementById('product-selection');
    
    if (categorySelection) categorySelection.classList.add('hidden');
    if (productSelection) productSelection.classList.add('hidden');
    
    if (scope === 'category' && categorySelection) {
        categorySelection.classList.remove('hidden');
    } else if (scope === 'selected' && productSelection) {
        productSelection.classList.remove('hidden');
    }
}

async function openProductModal() {
    try {
        const res = await axios.get('/api/admin/products?per_page=1000', {
            headers: getAuthHeaders()
        });
        allProducts = res.data.data || res.data;
        renderProductsList(allProducts);
        document.getElementById('product-modal').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading products:', error);
        window.toast.error('Failed to load products');
    }
}

function closeProductModal() {
    document.getElementById('product-modal').classList.add('hidden');
}

function renderProductsList(products) {
    const list = document.getElementById('products-list');
    list.innerHTML = products.map(product => {
        const imageUrl = product.primary_image?.url || null;
        const defaultVariation = product.variations?.find(v => v.is_default) || product.variations?.[0];
        const priceText = defaultVariation?.price ? `£${parseFloat(defaultVariation.price).toFixed(2)}` : 'No price';
        const categories = (product.categories || [])
            .map(c => `<span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">${c.name}</span>`)
            .join('');

        return `
        <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 mb-2">
            <input type="checkbox" value="${product.id}" class="product-checkbox mt-1"
                ${selectedProducts.includes(product.id) ? 'checked' : ''}>
            <div class="w-14 h-14 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                ${imageUrl ? `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover">` : '<div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>'}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="font-semibold truncate">${product.name}</div>
                    <div class="text-sm font-semibold text-green-600">${priceText}</div>
                </div>
                <div class="text-xs text-gray-500">SKU: ${product.sku || 'N/A'}</div>
                ${categories ? `<div class="flex flex-wrap gap-1 mt-2">${categories}</div>` : ''}
            </div>
        </label>
        `;
    }).join('');
}

function searchProducts() {
    const query = document.getElementById('product-search').value.toLowerCase();
    const categoryId = document.getElementById('product-category-filter').value;
    
    const filtered = allProducts.filter(p => {
        const matchesSearch = p.name.toLowerCase().includes(query) || 
                            (p.sku && p.sku.toLowerCase().includes(query));
        const matchesCategory = !categoryId || p.categories?.some(c => c.id == categoryId);
        return matchesSearch && matchesCategory;
    });
    
    renderProductsList(filtered);
}

function confirmProductSelection() {
    selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
        .map(checkbox => parseInt(checkbox.value));
    
    renderSelectedProducts();
    closeProductModal();
}

function renderSelectedProducts() {
    const container = document.getElementById('selected-products-display');
    const sourceProducts = allProducts.length > 0 ? allProducts : (offerData?.products || []);
    const selected = sourceProducts.filter(p => selectedProducts.includes(p.id));
    
    container.innerHTML = selected.length > 0 
        ? selected.map(p => `
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                <span class="text-sm">${p.name}</span>
                <button type="button" onclick="window.removeProduct(${p.id})" class="text-red-600 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `).join('')
        : '<p class="text-gray-500 text-sm">No products selected</p>';
}

function removeProduct(productId) {
    selectedProducts = selectedProducts.filter(id => id !== productId);
    renderSelectedProducts();
}

function clearProductSelection() {
    selectedProducts = [];
    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false);
}

async function handleSubmit(e) {
    e.preventDefault();
    
    const scope = document.querySelector('input[name="product-scope"]:checked').value;

    if (scope === 'selected' && selectedProducts.length === 0) {
        window.toast.error('Please select at least one product');
        return;
    }

    if (scope === 'category' && !document.getElementById('selected-category').value) {
        window.toast.error('Please select a category');
        return;
    }

    const formData = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value || null,
        badge_text: document.getElementById('badge_text').value || null,
        badge_color: document.getElementById('badge_color').value,
        discount_value: document.getElementById('discount_value').value,
        min_purchase_amount: document.getElementById('min_purchase_amount').value || null,
        max_uses_per_customer: document.getElementById('max_uses_per_customer').value || null,
        total_usage_limit: document.getElementById('total_usage_limit').value || null,
        starts_at: document.getElementById('starts_at').value || null,
        ends_at: document.getElementById('ends_at').value || null,
        is_active: document.getElementById('is_active').checked,
        scope: scope,
        category_id: scope === 'category' ? document.getElementById('selected-category').value : null,
        product_ids: scope === 'selected' ? selectedProducts : []
    };

    try {
        await axios.put(`/api/admin/offers/${offerId}`, formData, {
            headers: getAuthHeaders()
        });
        window.toast.success('Offer updated successfully');
        setTimeout(() => window.location.href = '/admin/offers', 1500);
    } catch (error) {
        console.error('Error updating offer:', error);
        const message = error.response?.data?.message || 'Failed to update offer';
        window.toast.error(message);
    }
}

async function deleteOffer() {
    if (!confirm('Are you sure you want to delete this offer? This action cannot be undone.')) return;
    
    try {
        await axios.delete(`/api/admin/offers/${offerId}`, {
            headers: getAuthHeaders()
        });
        window.toast.success('Offer deleted successfully');
        setTimeout(() => window.location.href = '/admin/offers', 1500);
    } catch (error) {
        console.error('Error deleting offer:', error);
        window.toast.error('Failed to delete offer');
    }
}

// Expose functions needed by inline handlers
window.handleProductScopeChange = handleProductScopeChange;
window.openProductModal = openProductModal;
window.closeProductModal = closeProductModal;
window.searchProducts = searchProducts;
window.confirmProductSelection = confirmProductSelection;
window.clearProductSelection = clearProductSelection;
window.removeProduct = removeProduct;
window.handleSubmit = handleSubmit;
window.deleteOffer = deleteOffer;

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadOffer();
});
