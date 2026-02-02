// Product Edit Page JavaScript
// Axios and toast are available globally via bootstrap.js and layout.js

const pathParts = window.location.pathname.split('/').filter(Boolean);
const productSlug = pathParts[pathParts.length - 2]; // Get slug from /admin/products/{slug}/edit
let productData = null;
let allCategories = [];
let variations = [];
let nextVariationId = 1;

async function loadProduct() {
    try {
        const [productRes, categoriesRes] = await Promise.all([
            axios.get(`/api/admin/products/${productSlug}`),
            axios.get('/api/admin/categories')
        ]);
        
        productData = productRes.data;
        allCategories = categoriesRes.data.data || categoriesRes.data;
        
        populateForm();
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('product-form').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading product:', error);
        window.toast.error('Failed to load product details');
        setTimeout(() => window.location.href = '/admin/products', 2000);
    }
}

function populateForm() {
    // Basic info
    document.getElementById('name').value = productData.name || '';
    document.getElementById('type').value = productData.type || 'standard';
    document.getElementById('sku').value = productData.sku || '';
    document.getElementById('description').value = productData.description || '';
    document.getElementById('short_description').value = productData.short_description || '';
    
    // Meat fields
    if (productData.type === 'meat') {
        document.getElementById('meat-fields').classList.remove('hidden');
        document.getElementById('meat_type').value = productData.meat_type || '';
        document.getElementById('cut_type').value = productData.cut_type || '';
        document.getElementById('is_halal').checked = productData.is_halal || false;
    }
    
    // Additional details
    document.getElementById('brand').value = productData.brand || '';
    document.getElementById('country_of_origin').value = productData.country_of_origin || '';
    document.getElementById('ingredients').value = productData.ingredients || '';
    document.getElementById('allergen_info').value = productData.allergen_info || '';
    document.getElementById('storage_instructions').value = productData.storage_instructions || '';
    
    // Status
    document.getElementById('is_active').checked = productData.is_active || false;
    document.getElementById('is_featured').checked = productData.is_featured || false;
    document.getElementById('is_on_sale').checked = productData.is_on_sale || false;
    
    // Load categories and variations
    loadCategories();
    loadVariations();
}

function loadCategories() {
    const container = document.getElementById('categories-container');
    const selectedIds = (productData.categories || []).map(c => c.id);
    
    container.innerHTML = allCategories.map(cat => `
        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
            <input type="checkbox" name="categories[]" value="${cat.id}" 
                ${selectedIds.includes(cat.id) ? 'checked' : ''}
                class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
            <span class="ml-3 text-sm font-medium">${cat.name}</span>
        </label>
    `).join('');
}

function handleTypeChange() {
    const type = document.getElementById('type').value;
    const meatFields = document.getElementById('meat-fields');
    
    if (type === 'meat') {
        meatFields.classList.remove('hidden');
    } else {
        meatFields.classList.add('hidden');
    }
}

function loadVariations() {
    variations = (productData.variations || []).map(v => ({
        id: v.id,
        name: v.name,
        size: v.size,
        size_unit: v.size_unit,
        price: v.price,
        compare_at_price: v.compare_at_price,
        stock_quantity: v.stock_quantity,
        sku: v.sku,
        is_default: v.is_default,
        is_active: v.is_active,
        isExisting: true
    }));
    
    nextVariationId = Math.max(...variations.map(v => v.id), 0) + 1;
    renderVariations();
}

function renderVariations() {
    const container = document.getElementById('variations-container');
    
    if (variations.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No variations yet. Click "Add Variation" to create one.</p>';
        return;
    }
    
    container.innerHTML = variations.map((v, index) => `
        <div class="border rounded-lg p-4 ${v.toDelete ? 'opacity-50 bg-red-50' : ''}" data-variation-index="${index}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-gray-900">${v.name || 'New Variation'}</h3>
                    ${v.is_default ? '<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Default</span>' : ''}
                    ${v.toDelete ? '<span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Will be deleted</span>' : ''}
                </div>
                <div class="flex items-center gap-2">
                    ${v.toDelete ? `
                        <button type="button" onclick="undoDeleteVariation(${index})" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-undo"></i> Undo
                        </button>
                    ` : `
                        <button type="button" onclick="deleteVariation(${index})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    `}
                </div>
            </div>
            
            ${!v.toDelete ? `
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Size *</label>
                        <input type="text" value="${v.size || ''}" onchange="updateVariation(${index}, 'size', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Unit *</label>
                        <select onchange="updateVariation(${index}, 'size_unit', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm" required>
                            <option value="">Select unit</option>
                            <option value="g" ${v.size_unit === 'g' ? 'selected' : ''}>Grams (g)</option>
                            <option value="kg" ${v.size_unit === 'kg' ? 'selected' : ''}>Kilograms (kg)</option>
                            <option value="ml" ${v.size_unit === 'ml' ? 'selected' : ''}>Milliliters (ml)</option>
                            <option value="l" ${v.size_unit === 'l' ? 'selected' : ''}>Liters (l)</option>
                            <option value="oz" ${v.size_unit === 'oz' ? 'selected' : ''}>Ounces (oz)</option>
                            <option value="lb" ${v.size_unit === 'lb' ? 'selected' : ''}>Pounds (lb)</option>
                            <option value="piece" ${v.size_unit === 'piece' ? 'selected' : ''}>Piece</option>
                            <option value="pack" ${v.size_unit === 'pack' ? 'selected' : ''}>Pack</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Price (£) *</label>
                        <input type="number" step="0.01" value="${v.price || ''}" onchange="updateVariation(${index}, 'price', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Compare Price (£)</label>
                        <input type="number" step="0.01" value="${v.compare_at_price || ''}" onchange="updateVariation(${index}, 'compare_at_price', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Stock *</label>
                        <input type="number" value="${v.stock_quantity || 0}" onchange="updateVariation(${index}, 'stock_quantity', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" value="${v.sku || ''}" onchange="updateVariation(${index}, 'sku', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div class="flex items-center gap-4 md:col-span-3">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" ${v.is_default ? 'checked' : ''} onchange="setDefaultVariation(${index}, this.checked)" 
                                class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm">Default variation</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" ${v.is_active ? 'checked' : ''} onchange="updateVariation(${index}, 'is_active', this.checked)" 
                                class="w-4 h-4 text-green-600 rounded">
                            <span class="text-sm">Active</span>
                        </label>
                    </div>
                </div>
            ` : ''}
        </div>
    `).join('');
}

function addVariation() {
    variations.push({
        id: nextVariationId++,
        name: '',
        size: '',
        size_unit: '',
        price: '',
        compare_at_price: '',
        stock_quantity: 0,
        sku: '',
        is_default: variations.length === 0,
        is_active: true,
        isExisting: false
    });
    renderVariations();
}

function updateVariation(index, field, value) {
    variations[index][field] = value;
    
    // Auto-generate name from size + unit
    if (field === 'size' || field === 'size_unit') {
        const v = variations[index];
        if (v.size && v.size_unit) {
            variations[index].name = `${v.size} ${v.size_unit}`;
        }
    }
    
    renderVariations();
}

function setDefaultVariation(index, isDefault) {
    if (isDefault) {
        // Unset all other defaults
        variations.forEach((v, i) => {
            v.is_default = i === index;
        });
    } else {
        variations[index].is_default = false;
    }
    renderVariations();
}

function deleteVariation(index) {
    const variation = variations[index];
    if (variation.isExisting) {
        // Mark for deletion
        variation.toDelete = true;
    } else {
        // Remove new variation immediately
        variations.splice(index, 1);
    }
    renderVariations();
}

function undoDeleteVariation(index) {
    variations[index].toDelete = false;
    renderVariations();
}

async function handleSubmit(e) {
    e.preventDefault();
    
    const selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked'))
        .map(input => parseInt(input.value));
    
    if (selectedCategories.length === 0) {
        window.toast.error('Please select at least one category');
        return;
    }
    
    // Validate variations
    const activeVariations = variations.filter(v => !v.toDelete);
    if (activeVariations.length === 0) {
        window.toast.error('Product must have at least one variation');
        return;
    }
    
    const hasDefault = activeVariations.some(v => v.is_default);
    if (!hasDefault) {
        window.toast.error('Please select a default variation');
        return;
    }
    
    // Validate required fields for each active variation
    for (const v of activeVariations) {
        if (!v.size || !v.size_unit || !v.price) {
            window.toast.error('Please fill in all required fields for variations (size, unit, price)');
            return;
        }
    }
    
    const formData = {
        name: document.getElementById('name').value,
        type: document.getElementById('type').value,
        sku: document.getElementById('sku').value || null,
        description: document.getElementById('description').value || null,
        short_description: document.getElementById('short_description').value || null,
        brand: document.getElementById('brand').value || null,
        country_of_origin: document.getElementById('country_of_origin').value || null,
        ingredients: document.getElementById('ingredients').value || null,
        allergen_info: document.getElementById('allergen_info').value || null,
        storage_instructions: document.getElementById('storage_instructions').value || null,
        is_active: document.getElementById('is_active').checked,
        is_featured: document.getElementById('is_featured').checked,
        is_on_sale: document.getElementById('is_on_sale').checked,
        categories: selectedCategories,
        variations: variations.map(v => ({
            id: v.isExisting ? v.id : undefined,
            name: v.name,
            size: v.size,
            size_unit: v.size_unit,
            price: parseFloat(v.price),
            compare_at_price: v.compare_at_price ? parseFloat(v.compare_at_price) : null,
            stock_quantity: parseInt(v.stock_quantity) || 0,
            sku: v.sku || null,
            is_default: v.is_default,
            is_active: v.is_active,
            _delete: v.toDelete || false
        }))
    };
    
    // Add meat fields if type is meat
    if (formData.type === 'meat') {
        formData.meat_type = document.getElementById('meat_type').value || null;
        formData.cut_type = document.getElementById('cut_type').value || null;
        formData.is_halal = document.getElementById('is_halal').checked;
    }
    
    try {
        await axios.patch(`/api/admin/products/${productSlug}`, formData);
        window.toast.success('Product updated successfully');
        setTimeout(() => window.location.href = `/admin/products/${productSlug}`, 1500);
    } catch (error) {
        console.error('Error updating product:', error);
        const message = error.response?.data?.message || 'Failed to update product';
        window.toast.error(message);
    }
}

// Expose functions needed by inline handlers
window.handleTypeChange = handleTypeChange;
window.handleSubmit = handleSubmit;
window.addVariation = addVariation;
window.updateVariation = updateVariation;
window.setDefaultVariation = setDefaultVariation;
window.deleteVariation = deleteVariation;
window.undoDeleteVariation = undoDeleteVariation;

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadProduct();
});
