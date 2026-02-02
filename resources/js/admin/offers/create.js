// Offers Create (Generic) Page JavaScript
// Axios and toast are available globally via bootstrap.js and layout.js

function updateFormFields() {
    const type = document.getElementById('offer-type').value;
    
    // Hide all dynamic fields
    document.getElementById('field-discount-value').classList.add('hidden');
    document.getElementById('field-bxgy').classList.add('hidden');
    document.getElementById('field-get-discount').classList.add('hidden');
    document.getElementById('field-bundle-price').classList.add('hidden');
    
    // Show relevant fields based on type
    switch(type) {
        case 'percentage_discount':
        case 'fixed_discount':
        case 'flash_sale':
            document.getElementById('field-discount-value').classList.remove('hidden');
            break;
        case 'bxgy_free':
            document.getElementById('field-bxgy').classList.remove('hidden');
            break;
        case 'multibuy':
            document.getElementById('field-bxgy').classList.remove('hidden');
            document.getElementById('field-bundle-price').classList.remove('hidden');
            break;
        case 'bxgy_discount':
            document.getElementById('field-bxgy').classList.remove('hidden');
            document.getElementById('field-get-discount').classList.remove('hidden');
            break;
        case 'bundle':
            document.getElementById('field-bundle-price').classList.remove('hidden');
            break;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('offer-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const data = {
            name: document.getElementById('offer-name').value,
            description: document.getElementById('offer-description').value,
            type: document.getElementById('offer-type').value,
            discount_value: document.getElementById('discount-value').value || null,
            buy_quantity: document.getElementById('buy-quantity').value || null,
            get_quantity: document.getElementById('get-quantity').value || null,
            get_discount_percentage: document.getElementById('get-discount-percentage').value || null,
            bundle_price: document.getElementById('bundle-price').value || null,
            starts_at: document.getElementById('starts-at').value || null,
            ends_at: document.getElementById('ends-at').value || null,
            badge_text: document.getElementById('badge-text').value || null,
            badge_color: document.getElementById('badge-color').value,
            min_purchase_amount: document.getElementById('min-purchase-amount').value || null,
            max_uses_per_customer: document.getElementById('max-uses-per-customer').value || null,
            total_usage_limit: document.getElementById('total-usage-limit').value || null,
            priority: document.getElementById('priority').value || 0,
            is_active: document.getElementById('is-active').checked,
        };

        try {
            await axios.post('/api/admin/offers', data);
            window.toast.success('Offer created successfully!');
            setTimeout(() => {
                window.location.href = '/admin/offers';
            }, 1000);
        } catch (error) {
            console.error('Error creating offer:', error);
            const message = error.response?.data?.message || error.message;
            window.toast.error('Failed to create offer: ' + message);
        }
    });
});

// Expose function needed by inline handler
window.updateFormFields = updateFormFields;
