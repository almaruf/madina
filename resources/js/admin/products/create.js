// Product Create Page - Essential Fields
// Handles product creation with default variation

let categories = [];

// Load categories
async function loadCategories() {
    try {
        const response = await axios.get('/api/admin/categories');
        categories = response.data.data || response.data;
        
        const select = document.getElementById('categories');
        select.innerHTML = categories
            .filter(cat => cat.is_active)
            .map(cat => `<option value="${cat.id}">${cat.name}</option>`)
            .join('');
    } catch (error) {
        console.error('Error loading categories:', error);
        window.toast.error('Failed to load categories');
    }
}

// Auto-generate slug from name
function setupSlugGeneration() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', (e) => {
        const slug = e.target.value.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    });
}

// Get form data
function getFormData() {
    const selectedCategories = Array.from(document.getElementById('categories').selectedOptions)
        .map(option => parseInt(option.value));
    
    if (selectedCategories.length === 0) {
        throw new Error('Please select at least one category');
    }

    const productData = {
        // Basic Information
        name: document.getElementById('name').value.trim(),
        slug: document.getElementById('slug').value.trim(),
        description: document.getElementById('description').value.trim() || null,
        short_description: document.getElementById('short_description').value.trim() || null,
        type: document.getElementById('type').value,
        brand: document.getElementById('brand').value.trim() || null,
        
        // Flags
        is_active: document.getElementById('is_active').checked,
        is_featured: document.getElementById('is_featured').checked,
        is_halal: document.getElementById('is_halal').checked,
        
        // Categories
        categories: selectedCategories,
        
        // Default Variation
        variations: [{
            size: document.getElementById('variation_size').value.trim(),
            unit: document.getElementById('variation_unit').value,
            price: parseFloat(document.getElementById('variation_price').value),
            compare_at_price: document.getElementById('variation_compare_at_price').value 
                ? parseFloat(document.getElementById('variation_compare_at_price').value) 
                : null,
            stock_quantity: parseInt(document.getElementById('variation_stock_quantity').value) || 0,
            sku: document.getElementById('variation_sku').value.trim() || null,
            barcode: document.getElementById('variation_barcode').value.trim() || null,
            is_default: true,
            is_active: true
        }]
    };

    return productData;
}

// Handle form submission
async function handleSubmit(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';
    
    try {
        const data = getFormData();
        const response = await axios.post('/api/admin/products', data);
        window.toast.success('Product created successfully!');
        setTimeout(() => {
            window.location.href = '/admin/products/' + response.data.slug;
        }, 1000);
    } catch (error) {
        console.error('Error creating product:', error);
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        
        if (error.message) {
            window.toast.error(error.message);
        } else if (error.response?.data?.errors) {
            const errors = Object.values(error.response.data.errors).flat();
            window.toast.error(errors.join('<br>'));
        } else {
            window.toast.error(error.response?.data?.message || 'Failed to create product');
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    setupSlugGeneration();
    loadCategories();
    
    const form = document.getElementById('product-form');
    form.addEventListener('submit', handleSubmit);
});
