// Shop Create Page - Basic Information Only
// Handles shop creation with essential fields

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

// Get form data - basic information only
function getFormData() {
    return {
        // Basic Information
        name: document.getElementById('name').value,
        slug: document.getElementById('slug').value,
        description: document.getElementById('description').value || null,
        tagline: document.getElementById('tagline').value || null,
        domain: document.getElementById('domain').value || null,
        business_type: document.getElementById('business_type').value,
        specialization: document.getElementById('specialization').value,
        phone: document.getElementById('phone').value,
        email: document.getElementById('email').value,
        support_email: document.getElementById('support_email').value || null,
        whatsapp_number: document.getElementById('whatsapp_number').value || null,
        address_line_1: document.getElementById('address_line_1').value,
        address_line_2: document.getElementById('address_line_2').value || null,
        city: document.getElementById('city').value,
        postcode: document.getElementById('postcode').value,
        country: document.getElementById('country').value
    };
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
        const response = await axios.post('/api/admin/shops', data);
        window.toast.success('Shop created successfully!');
        setTimeout(() => {
            window.location.href = '/admin/shops/' + response.data.slug;
        }, 1000);
    } catch (error) {
        console.error('Error creating shop:', error);
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        
        if (error.response?.data?.errors) {
            const errors = Object.values(error.response.data.errors).flat();
            window.toast.error(errors.join('<br>'));
        } else {
            window.toast.error(error.response?.data?.message || 'Failed to create shop');
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    setupSlugGeneration();
    
    const form = document.getElementById('shop-form');
    form.addEventListener('submit', handleSubmit);
});
