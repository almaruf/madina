// Offers Edit (Generic) Page JavaScript
// Axios and toast are available globally via bootstrap.js and layout.js

// Get offer ID from URL
const urlParams = new URLSearchParams(window.location.search);
const offerId = urlParams.get('id') || window.location.pathname.split('/').pop();

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

async function loadOffer() {
    try {
        const response = await axios.get(`/api/admin/offers/${offerId}`);
        const offer = response.data;
        
        document.getElementById('offer-id').value = offer.id;
        document.getElementById('offer-name').value = offer.name;
        document.getElementById('offer-description').value = offer.description || '';
        document.getElementById('offer-type').value = offer.type;
        document.getElementById('discount-value').value = offer.discount_value || '';
        document.getElementById('buy-quantity').value = offer.buy_quantity || '';
        document.getElementById('get-quantity').value = offer.get_quantity || '';
        document.getElementById('get-discount-percentage').value = offer.get_discount_percentage || '';
        document.getElementById('bundle-price').value = offer.bundle_price || '';
        document.getElementById('badge-text').value = offer.badge_text || '';
        document.getElementById('badge-color').value = offer.badge_color || '#DC2626';
        document.getElementById('min-purchase-amount').value = offer.min_purchase_amount || '';
        document.getElementById('max-uses-per-customer').value = offer.max_uses_per_customer || '';
        document.getElementById('total-usage-limit').value = offer.total_usage_limit || '';
        document.getElementById('priority').value = offer.priority || 0;
        document.getElementById('is-active').checked = offer.is_active;
        
        if (offer.starts_at) {
            document.getElementById('starts-at').value = new Date(offer.starts_at).toISOString().slice(0, 16);
        }
        if (offer.ends_at) {
            document.getElementById('ends-at').value = new Date(offer.ends_at).toISOString().slice(0, 16);
        }
        
        updateFormFields();
        
        document.getElementById('loading-container').classList.add('hidden');
        document.getElementById('offer-form').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading offer:', error);
        const message = error.response?.data?.message || error.message;
        window.toast.error('Failed to load offer: ' + message);
        document.getElementById('loading-container').classList.add('hidden');
        document.getElementById('offer-form').classList.remove('hidden');
    }
}

async function deleteOffer() {
    if (!confirm('Are you sure you want to delete this offer? This action cannot be undone.')) {
        return;
    }
    
    try {
        await axios.delete(`/api/admin/offers/${offerId}`);
        window.toast.success('Offer deleted successfully!');
        setTimeout(() => {
            window.location.href = '/admin/offers';
        }, 1000);
    } catch (error) {
        console.error('Error deleting offer:', error);
        window.toast.error('Failed to delete offer');
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
            await axios.put(`/api/admin/offers/${offerId}`, data);
            window.toast.success('Offer updated successfully!');
            setTimeout(() => {
                window.location.href = '/admin/offers';
            }, 1000);
        } catch (error) {
            console.error('Error updating offer:', error);
            const message = error.response?.data?.message || error.message;
            window.toast.error('Failed to update offer: ' + message);
        }
    });

    loadOffer();
});

// Expose functions needed by inline handlers
window.updateFormFields = updateFormFields;
window.deleteOffer = deleteOffer;
