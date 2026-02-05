// Category Edit Page JavaScript
// Axios and toast are available globally via bootstrap.js and layout.js

const pathParts = window.location.pathname.split('/').filter(Boolean);
const categorySlug = pathParts[pathParts.length - 2]; // Get slug from /admin/categories/{slug}/edit
let categoryData = null;
let allCategories = [];

async function loadCategory() {
    try {
        const [categoryRes, categoriesRes] = await Promise.all([
            axios.get(`/api/admin/categories/${categorySlug}`),
            axios.get('/api/admin/categories')
        ]);
        
        categoryData = categoryRes.data;
        allCategories = (categoriesRes.data.data || categoriesRes.data).filter(c => c.slug !== categorySlug);
        
        populateForm();
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('category-form').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading category:', error);
        window.toast.error('Failed to load category details');
        setTimeout(() => window.location.href = '/admin/categories', 2000);
    }
}

function populateForm() {
    document.getElementById('name').value = categoryData.name || '';
    document.getElementById('slug').value = categoryData.slug || '';
    document.getElementById('description').value = categoryData.description || '';
    document.getElementById('order').value = categoryData.order || 0;
    document.getElementById('is_active').checked = categoryData.is_active || false;
    document.getElementById('is_featured').checked = categoryData.is_featured || false;
    
    // Load parent categories
    const parentSelect = document.getElementById('parent_id');
    parentSelect.innerHTML = '<option value="">None (Top Level)</option>' + 
        allCategories.map(cat => `
            <option value="${cat.id}" ${cat.id === categoryData.parent_id ? 'selected' : ''}>
                ${cat.name}
            </option>
        `).join('');
}

async function handleSubmit(e) {
    e.preventDefault();
    
    const formData = {
        name: document.getElementById('name').value,
        slug: document.getElementById('slug').value || null,
        parent_id: document.getElementById('parent_id').value || null,
        description: document.getElementById('description').value || null,
        order: parseInt(document.getElementById('order').value) || 0,
        is_active: document.getElementById('is_active').checked,
        is_featured: document.getElementById('is_featured').checked
    };
    
    try {
        await axios.patch(`/api/admin/categories/${categorySlug}`, formData);
        window.toast.success('Category updated successfully');
        setTimeout(() => window.location.href = `/admin/categories/${categorySlug}`, 1500);
    } catch (error) {
        console.error('Error updating category:', error);
        const message = error.response?.data?.message || 'Failed to update category';
        window.toast.error(message);
    }
}

// Expose function needed by inline handler
window.handleSubmit = handleSubmit;

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadCategory();
});
