// Shop Edit Page with Separate Form Submissions per Tab

let currentShop = null;

// Get shop slug from the page
function getShopSlug() {
    const slugElement = document.querySelector('[data-shop-slug]');
    return slugElement ? slugElement.dataset.shopSlug : null;
}

// Initialize tabs
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.tab;

            // Update button states
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            button.classList.remove('border-transparent', 'text-gray-500');
            button.classList.add('active', 'border-blue-500', 'text-blue-600');

            // Update content visibility
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`tab-${targetTab}`).classList.remove('hidden');
        });
    });
}

// Load shop data from API
async function loadShop() {
    const shopSlug = getShopSlug();
    if (!shopSlug) {
        console.error('Shop slug not found');
        return;
    }

    try {
        const response = await axios.get(`/api/admin/shops/${shopSlug}`);
        currentShop = response.data;
        populateForm(currentShop);
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('shop-form-container').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading shop:', error);
        document.getElementById('loading').classList.add('hidden');
        window.toast.error('Failed to load shop details');
    }
}

// Populate form with shop data
function populateForm(shop) {
    // Basic Information
    const formBasic = document.getElementById('form-basic');
    formBasic.querySelector('[name="name"]').value = shop.name || '';
    formBasic.querySelector('[name="slug"]').value = shop.slug || '';
    formBasic.querySelector('[name="description"]').value = shop.description || '';
    formBasic.querySelector('[name="tagline"]').value = shop.tagline || '';
    formBasic.querySelector('[name="domain"]').value = shop.domain || '';
    formBasic.querySelector('[name="business_type"]').value = shop.business_type || 'grocery';
    formBasic.querySelector('[name="specialization"]').value = shop.specialization || 'general';
    formBasic.querySelector('[name="phone"]').value = shop.phone || '';
    formBasic.querySelector('[name="email"]').value = shop.email || '';
    formBasic.querySelector('[name="support_email"]').value = shop.support_email || '';
    formBasic.querySelector('[name="whatsapp_number"]').value = shop.whatsapp_number || '';
    formBasic.querySelector('[name="address_line_1"]').value = shop.address_line_1 || '';
    formBasic.querySelector('[name="address_line_2"]').value = shop.address_line_2 || '';
    formBasic.querySelector('[name="city"]').value = shop.city || '';
    formBasic.querySelector('[name="postcode"]').value = shop.postcode || '';
    formBasic.querySelector('[name="country"]').value = shop.country || 'United Kingdom';
    formBasic.querySelector('[name="is_active"]').checked = shop.is_active;
    
    // Delivery & Pricing
    const formDelivery = document.getElementById('form-delivery');
    formDelivery.querySelector('[name="currency"]').value = shop.currency || 'GBP';
    formDelivery.querySelector('[name="currency_symbol"]').value = shop.currency_symbol || '£';
    formDelivery.querySelector('[name="delivery_fee"]').value = shop.delivery_fee || 0;
    formDelivery.querySelector('[name="min_order_amount"]').value = shop.min_order_amount || 0;
    formDelivery.querySelector('[name="free_delivery_threshold"]').value = shop.free_delivery_threshold || 0;
    formDelivery.querySelector('[name="delivery_radius_km"]').value = shop.delivery_radius_km || 10;
    formDelivery.querySelector('[name="delivery_enabled"]').checked = shop.delivery_enabled;
    formDelivery.querySelector('[name="collection_enabled"]').checked = shop.collection_enabled;
    formDelivery.querySelector('[name="online_payment"]').checked = shop.online_payment;
    formDelivery.querySelector('[name="has_halal_products"]').checked = shop.has_halal_products;
    formDelivery.querySelector('[name="has_organic_products"]').checked = shop.has_organic_products;
    
    // Legal & VAT
    const formLegal = document.getElementById('form-legal');
    formLegal.querySelector('[name="legal_company_name"]').value = shop.legal_company_name || '';
    formLegal.querySelector('[name="company_registration_number"]').value = shop.company_registration_number || '';
    formLegal.querySelector('[name="vat_registered"]').checked = shop.vat_registered || false;
    formLegal.querySelector('[name="vat_number"]').value = shop.vat_number || '';
    formLegal.querySelector('[name="vat_rate"]').value = shop.vat_rate || '';
    formLegal.querySelector('[name="prices_include_vat"]').checked = shop.prices_include_vat !== false;
    
    // Bank Details
    const formBank = document.getElementById('form-bank');
    formBank.querySelector('[name="bank_name"]').value = shop.bank_name || '';
    formBank.querySelector('[name="bank_account_name"]').value = shop.bank_account_name || '';
    formBank.querySelector('[name="bank_account_number"]').value = shop.bank_account_number || '';
    formBank.querySelector('[name="bank_sort_code"]').value = shop.bank_sort_code || '';
    formBank.querySelector('[name="bank_iban"]').value = shop.bank_iban || '';
    formBank.querySelector('[name="bank_swift_code"]').value = shop.bank_swift_code || '';
    
    // Branding & Social
    const formBranding = document.getElementById('form-branding');
    formBranding.querySelector('[name="primary_color"]').value = shop.primary_color || '#10b981';
    formBranding.querySelector('[name="secondary_color"]').value = shop.secondary_color || '#059669';
    formBranding.querySelector('[name="logo_url"]').value = shop.logo_url || '';
    formBranding.querySelector('[name="favicon_url"]').value = shop.favicon_url || '';
    formBranding.querySelector('[name="facebook_url"]').value = shop.facebook_url || '';
    formBranding.querySelector('[name="instagram_url"]').value = shop.instagram_url || '';
    formBranding.querySelector('[name="twitter_url"]').value = shop.twitter_url || '';
    
    // Operating Hours
    const formHours = document.getElementById('form-hours');
    formHours.querySelector('[name="monday_hours"]').value = shop.monday_hours || '';
    formHours.querySelector('[name="tuesday_hours"]').value = shop.tuesday_hours || '';
    formHours.querySelector('[name="wednesday_hours"]').value = shop.wednesday_hours || '';
    formHours.querySelector('[name="thursday_hours"]').value = shop.thursday_hours || '';
    formHours.querySelector('[name="friday_hours"]').value = shop.friday_hours || '';
    formHours.querySelector('[name="saturday_hours"]').value = shop.saturday_hours || '';
    formHours.querySelector('[name="sunday_hours"]').value = shop.sunday_hours || '';
}

// Get form data for a specific form
function getFormData(form) {
    const formData = new FormData(form);
    const data = {};
    
    for (const [key, value] of formData.entries()) {
        const input = form.querySelector(`[name="${key}"]`);
        
        if (input && input.type === 'checkbox') {
            data[key] = input.checked;
        } else if (input && input.type === 'number') {
            data[key] = value ? parseFloat(value) : null;
        } else {
            data[key] = value || null;
        }
    }
    
    return data;
}

// Handle form submission for any tab
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formType = form.dataset.formType;
    const shopSlug = getShopSlug();
    
    if (!shopSlug) return;
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    try {
        const data = getFormData(form);
        
        // Remove vat_rate if present (it's readonly and managed by config)
        if ('vat_rate' in data) {
            delete data.vat_rate;
        }
        
        await axios.patch(`/api/admin/shops/${shopSlug}`, data);
        
        // Update currentShop with new data
        Object.assign(currentShop, data);
        
        window.toast.success(`${getFormTypeLabel(formType)} updated successfully!`);
        
        // If slug was changed and it's the basic form, redirect to new slug
        if (formType === 'basic' && data.slug && data.slug !== shopSlug) {
            setTimeout(() => {
                window.location.href = '/admin/shops/' + data.slug + '/edit';
            }, 1000);
        }
    } catch (error) {
        console.error(`Error updating ${formType}:`, error);
        
        if (error.response?.data?.errors) {
            const errors = Object.values(error.response.data.errors).flat();
            window.toast.error(errors.join('<br>'));
        } else {
            window.toast.error(error.response?.data?.message || `Failed to update ${formType}`);
        }
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

// Get label for form type
function getFormTypeLabel(formType) {
    const labels = {
        'basic': 'Basic Information',
        'delivery': 'Delivery & Pricing',
        'legal': 'Legal & VAT',
        'bank': 'Bank Details',
        'branding': 'Branding & Social',
        'hours': 'Operating Hours'
    };
    return labels[formType] || formType;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeTabs();
    loadShop();
    
    // Attach submit handlers to all forms
    document.querySelectorAll('form[data-form-type]').forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });
});
