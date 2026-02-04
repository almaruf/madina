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
    formBasic.querySelector('[name="is_active"]').checked = !!shop.is_active;
    
    // Delivery & Pricing
    const formDelivery = document.getElementById('form-delivery');
    formDelivery.querySelector('[name="currency"]').value = shop.currency || 'GBP';
    formDelivery.querySelector('[name="currency_symbol"]').value = shop.currency_symbol || '£';
    formDelivery.querySelector('[name="delivery_fee"]').value = shop.delivery_fee || 0;
    formDelivery.querySelector('[name="min_order_amount"]').value = shop.min_order_amount || 0;
    formDelivery.querySelector('[name="free_delivery_threshold"]').value = shop.free_delivery_threshold || 0;
    formDelivery.querySelector('[name="delivery_radius_km"]').value = shop.delivery_radius_km || 10;
    formDelivery.querySelector('[name="delivery_enabled"]').checked = !!shop.delivery_enabled;
    formDelivery.querySelector('[name="collection_enabled"]').checked = !!shop.collection_enabled;
    formDelivery.querySelector('[name="online_payment"]').checked = !!shop.online_payment;
    formDelivery.querySelector('[name="has_halal_products"]').checked = !!shop.has_halal_products;
    formDelivery.querySelector('[name="has_organic_products"]').checked = !!shop.has_organic_products;
    
    // Legal & VAT
    const formLegal = document.getElementById('form-legal');
    formLegal.querySelector('[name="legal_company_name"]').value = shop.legal_company_name || '';
    formLegal.querySelector('[name="company_registration_number"]').value = shop.company_registration_number || '';
    formLegal.querySelector('[name="vat_registered"]').checked = !!shop.vat_registered;
    formLegal.querySelector('[name="vat_number"]').value = shop.vat_number || '';
    formLegal.querySelector('[name="vat_rate"]').value = shop.vat_rate || '';
    formLegal.querySelector('[name="prices_include_vat"]').checked = !!shop.prices_include_vat;
    
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
    
    // Operating Hours - Use new separate time fields
    const formHours = document.getElementById('form-hours');
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    
    days.forEach(day => {
        const openInput = formHours.querySelector(`[name="${day}_open"]`);
        const closeInput = formHours.querySelector(`[name="${day}_close"]`);
        const closedCheckbox = formHours.querySelector(`[name="${day}_closed"]`);
        
        // Set values from new separate fields
        if (openInput && shop[`${day}_open`]) {
            openInput.value = shop[`${day}_open`].substring(0, 5); // Remove seconds
        }
        if (closeInput && shop[`${day}_close`]) {
            closeInput.value = shop[`${day}_close`].substring(0, 5); // Remove seconds
        }
        if (closedCheckbox) {
            closedCheckbox.checked = shop[`${day}_closed`] || false;
            if (closedCheckbox.checked) {
                toggleDayClosed(day);
            }
        }
    });
}

// Get form data for a specific form
function getFormData(form) {
    const data = {};
    
    // Process all form inputs manually (not using FormData to avoid issues)
    form.querySelectorAll('input, select, textarea').forEach(input => {
        if (!input.name) return;
        
        if (input.type === 'checkbox') {
            // Handle checkboxes explicitly as booleans
            data[input.name] = input.checked;
        } else if (input.type === 'number') {
            data[input.name] = input.value ? parseFloat(input.value) : null;
        } else if (input.type === 'time') {
            // Handle time inputs - only add if not disabled
            if (!input.disabled) {
                data[input.name] = input.value || null;
            }
        } else {
            data[input.name] = input.value || null;
        }
    });
    
    // For operating hours: if day is closed, clear the open/close times
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    days.forEach(day => {
        const closedField = `${day}_closed`;
        if (data[closedField] === true) {
            console.log(`Day ${day} is closed, clearing times`);
            data[`${day}_open`] = null;
            data[`${day}_close`] = null;
        }
    });
    
    console.log('Final form data:', data);
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
        
        const response = await axios.patch(`/api/admin/shops/${shopSlug}`, data);
        
        // Update currentShop with response data to ensure sync with backend
        currentShop = response.data;
        
        // Refresh the form with the updated data from backend
        populateForm(currentShop);
        
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

// Operating Hours Helper Functions
window.toggleDayClosed = function(day, checkboxElement = null) {
    console.log(`toggleDayClosed called for ${day}, element passed:`, !!checkboxElement);
    
    // Use setTimeout to ensure checkbox state has updated before we query it
    setTimeout(() => {
        // If checkbox element was passed, use it directly; otherwise query for it
        const closedCheckbox = checkboxElement || document.querySelector(`input[name="${day}_closed"]`);
        const openInput = document.querySelector(`input[name="${day}_open"]`);
        const closeInput = document.querySelector(`input[name="${day}_close"]`);
        
        console.log(`toggleDayClosed(${day}): checkbox found = ${!!closedCheckbox}, checked = ${closedCheckbox?.checked}`);
        
        if (closedCheckbox && closedCheckbox.checked) {
            // Day is closed, disable and clear the time inputs
            if (openInput) {
                openInput.disabled = true;
                openInput.value = '';
                openInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            }
            if (closeInput) {
                closeInput.disabled = true;
                closeInput.value = '';
                closeInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            }
        } else {
            // Day is open, enable the time inputs
            if (openInput) {
                openInput.disabled = false;
                openInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
            if (closeInput) {
                closeInput.disabled = false;
                closeInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        }
    }, 0);
}

window.applyToAll = function() {
    const mondayOpen = document.querySelector('[name="monday_open"]').value;
    const mondayClose = document.querySelector('[name="monday_close"]').value;
    const mondayClosed = document.querySelector('[name="monday_closed"]').checked;
    
    if (!mondayOpen || !mondayClose) {
        window.toast.error('Please set Monday hours first before applying to other days');
        return;
    }
    
    const weekdays = ['tuesday', 'wednesday', 'thursday', 'friday'];
    weekdays.forEach(day => {
        document.querySelector(`[name="${day}_open"]`).value = mondayOpen;
        document.querySelector(`[name="${day}_close"]`).value = mondayClose;
        document.querySelector(`[name="${day}_closed"]`).checked = mondayClosed;
        toggleDayClosed(day);
    });
    
    window.toast.success('Applied Monday hours to all weekdays');
}

window.copyToAll = function() {
    const mondayOpen = document.querySelector('[name="monday_open"]').value;
    const mondayClose = document.querySelector('[name="monday_close"]').value;
    const mondayClosed = document.querySelector('[name="monday_closed"]').checked;
    
    if (!mondayOpen || !mondayClose) {
        window.toast.error('Please set Monday hours first before copying to other days');
        return;
    }
    
    const allDays = ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    allDays.forEach(day => {
        document.querySelector(`[name="${day}_open"]`).value = mondayOpen;
        document.querySelector(`[name="${day}_close"]`).value = mondayClose;
        document.querySelector(`[name="${day}_closed"]`).checked = mondayClosed;
        toggleDayClosed(day);
    });
    
    window.toast.success('Copied Monday hours to all days');
}

window.closeWeekends = function() {
    ['saturday', 'sunday'].forEach(day => {
        const checkbox = document.querySelector(`[name="${day}_closed"]`);
        if (checkbox) {
            checkbox.checked = true;
            toggleDayClosed(day);
        }
    });
    
    window.toast.success('Weekends marked as closed');
}

window.previewHours = function() {
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    let preview = '';
    
    days.forEach((day, index) => {
        const closedCheckbox = document.querySelector(`[name="${day}_closed"]`);
        if (closedCheckbox && closedCheckbox.checked) {
            preview += `${dayNames[index]}: Closed\n`;
        } else {
            const openTime = document.querySelector(`[name="${day}_open"]`).value;
            const closeTime = document.querySelector(`[name="${day}_close"]`).value;
            if (openTime && closeTime) {
                preview += `${dayNames[index]}: ${formatTime(openTime)} - ${formatTime(closeTime)}\n`;
            } else {
                preview += `${dayNames[index]}: Not set\n`;
            }
        }
    });
    
    showModal('Operating Hours Preview', preview);
}

function formatTime(time24) {
    if (!time24) return '';
    const [hours, minutes] = time24.split(':');
    let hour = parseInt(hours);
    const period = hour >= 12 ? 'PM' : 'AM';
    if (hour > 12) hour -= 12;
    if (hour === 0) hour = 12;
    return `${hour}:${minutes} ${period}`;
}

// Modal helper functions
function showModal(title, content) {
    const modal = document.getElementById('general-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');
    
    if (modal && modalTitle && modalContent) {
        modalTitle.textContent = title;
        modalContent.textContent = content;
        modal.classList.remove('hidden');
    }
}

window.closeModal = function() {
    const modal = document.getElementById('general-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
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
