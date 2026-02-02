// Percentage Discount Offer Create Page JavaScript
// Axios and toast are available globally via bootstrap.js and layout.js

// Helper function to get auth headers
function getAuthHeaders() {
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    return token ? { 'Authorization': `Bearer ${token}` } : {};
}

let allProducts = [];
let allCategories = [];
let selectedProducts = [];

async function loadCategories() {
    try {
        const res = await axios.get('/api/admin/categories', {
            headers: getAuthHeaders()
        });
        allCategories = res.data.data || res.data;
        loadCategorySelect();
    } catch (error) {
        console.error('Error loading categories:', error);
        window.toast.error('Failed to load categories');
    }
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
    const scope = document.querySelector('input[name="product-scope"]:checked').value;
    document.getElementById('category-selection').classList.add('hidden');
    document.getElementById('product-selection').classList.add('hidden');
    
    if (scope === 'category') {
        document.getElementById('category-selection').classList.remove('hidden');
    } else if (scope === 'selected') {
        document.getElementById('product-selection').classList.remove('hidden');
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
    const selected = allProducts.filter(p => selectedProducts.includes(p.id));
    
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
        type: 'percentage_discount',
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
        await axios.post('/api/admin/offers', formData, {
            headers: getAuthHeaders()
        });
        window.toast.success('Offer created successfully');
        setTimeout(() => window.location.href = '/admin/offers', 1500);
    } catch (error) {
        console.error('Error creating offer:', error);
        const message = error.response?.data?.message || 'Failed to create offer';
        window.toast.error(message);
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
});
