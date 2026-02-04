// Admin Shop Show Page
// Handles shop details display and management with tabs

let currentShop = null;

// Get shop slug from the page
function getShopSlug() {
    const slugElement = document.querySelector('[data-shop-slug]');
    return slugElement ? slugElement.dataset.shopSlug : null;
}

// Initialize tabs
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
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
        renderShop(currentShop);
    } catch (error) {
        console.error('Error loading shop:', error);
        document.getElementById('loading').classList.add('hidden');
        const errorDiv = document.getElementById('error');
        errorDiv.classList.remove('hidden');
        errorDiv.querySelector('p').textContent = error.response?.data?.message || 'Failed to load shop details';
    }
}

// Render shop details to the page
function renderShop(shop) {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('shop-details').classList.remove('hidden');

    const isArchived = shop.deleted_at !== null;

    // Header
    document.getElementById('shop-header').innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">${shop.name}</h2>
                ${shop.legal_company_name ? `<p class="text-sm text-gray-600">Legal Name: ${shop.legal_company_name}</p>` : ''}
                <p class="text-gray-600 mt-1">${shop.slug}</p>
                ${isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>' : ''}
                ${shop.is_active && !isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>' : ''}
                ${!shop.is_active && !isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Inactive</span>' : ''}
            </div>
        </div>
    `;

    // Basic Information Tab
    document.getElementById('shop-basic-info').innerHTML = `
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Shop Name</h3>
            <p class="text-lg text-gray-900">${shop.name}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Slug</h3>
            <p class="text-lg text-gray-900">${shop.slug}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Domain</h3>
            <p class="text-lg text-gray-900">${shop.domain || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Business Type</h3>
            <p class="text-lg text-gray-900">${shop.business_type || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Phone</h3>
            <p class="text-lg text-gray-900">${shop.phone}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Email</h3>
            <p class="text-lg text-gray-900">${shop.email}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Support Email</h3>
            <p class="text-lg text-gray-900">${shop.support_email || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Specialization</h3>
            <p class="text-lg text-gray-900">${shop.specialization || 'General'}</p>
        </div>
        <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Address</h3>
            <p class="text-gray-900">
                ${shop.address_line_1 || 'N/A'}<br>
                ${shop.address_line_2 ? shop.address_line_2 + '<br>' : ''}
                ${shop.city || ''}, ${shop.postcode || ''}<br>
                ${shop.country || 'United Kingdom'}
            </p>
        </div>
        ${shop.description ? `
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                <p class="text-gray-900">${shop.description}</p>
            </div>
        ` : ''}
        ${shop.tagline ? `
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Tagline</h3>
                <p class="text-gray-900">${shop.tagline}</p>
            </div>
        ` : ''}
    `;

    // Delivery & Pricing Tab
    document.getElementById('shop-delivery-info').innerHTML = `
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Currency</h3>
            <p class="text-lg text-gray-900">${shop.currency} (${shop.currency_symbol})</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Delivery Fee</h3>
            <p class="text-lg text-gray-900">${shop.currency_symbol}${parseFloat(shop.delivery_fee || 0).toFixed(2)}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Min Order Amount</h3>
            <p class="text-lg text-gray-900">${shop.currency_symbol}${parseFloat(shop.min_order_amount || 0).toFixed(2)}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Free Delivery Threshold</h3>
            <p class="text-lg text-gray-900">${shop.currency_symbol}${parseFloat(shop.free_delivery_threshold || 0).toFixed(2)}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Delivery Radius</h3>
            <p class="text-lg text-gray-900">${shop.delivery_radius_km || 0} km</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Delivery Enabled</h3>
            <p class="text-lg text-gray-900">${shop.delivery_enabled ? '✓ Yes' : '✗ No'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Collection Enabled</h3>
            <p class="text-lg text-gray-900">${shop.collection_enabled ? '✓ Yes' : '✗ No'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Online Payment</h3>
            <p class="text-lg text-gray-900">${shop.online_payment ? '✓ Enabled' : '✗ Disabled'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Halal Products</h3>
            <p class="text-lg text-gray-900">${shop.has_halal_products ? '✓ Yes' : '✗ No'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Organic Products</h3>
            <p class="text-lg text-gray-900">${shop.has_organic_products ? '✓ Yes' : '✗ No'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">International Products</h3>
            <p class="text-lg text-gray-900">${shop.has_international_products ? '✓ Yes' : '✗ No'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Loyalty Program</h3>
            <p class="text-lg text-gray-900">${shop.loyalty_program ? '✓ Enabled' : '✗ Disabled'}</p>
        </div>
    `;

    // Legal & VAT Tab
    document.getElementById('shop-legal-info').innerHTML = `
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Legal Company Name</h3>
            <p class="text-lg text-gray-900">${shop.legal_company_name || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Company Registration Number</h3>
            <p class="text-lg text-gray-900">${shop.company_registration_number || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">VAT Registered</h3>
            <p class="text-lg text-gray-900">${shop.vat_registered ? '✓ Yes' : '✗ No'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">VAT Number</h3>
            <p class="text-lg text-gray-900">${shop.vat_number || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">VAT Rate</h3>
            <p class="text-lg text-gray-900">${shop.vat_rate || 0}%</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Prices Include VAT</h3>
            <p class="text-lg text-gray-900">${shop.prices_include_vat ? '✓ Yes (VAT Inclusive)' : '✗ No (VAT Exclusive)'}</p>
        </div>
    `;

    // Bank Details Tab
    document.getElementById('shop-bank-info').innerHTML = `
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Bank Name</h3>
            <p class="text-lg text-gray-900">${shop.bank_name || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Account Name</h3>
            <p class="text-lg text-gray-900">${shop.bank_account_name || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Account Number</h3>
            <p class="text-lg text-gray-900">${shop.bank_account_number || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Sort Code</h3>
            <p class="text-lg text-gray-900">${shop.bank_sort_code || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">IBAN</h3>
            <p class="text-lg text-gray-900">${shop.bank_iban || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">SWIFT/BIC Code</h3>
            <p class="text-lg text-gray-900">${shop.bank_swift_code || 'N/A'}</p>
        </div>
    `;

    // Branding & Social Tab
    document.getElementById('shop-branding-info').innerHTML = `
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Primary Color</h3>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded border" style="background-color: ${shop.primary_color || '#10b981'}"></div>
                <p class="text-lg text-gray-900">${shop.primary_color || '#10b981'}</p>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Secondary Color</h3>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded border" style="background-color: ${shop.secondary_color || '#059669'}"></div>
                <p class="text-lg text-gray-900">${shop.secondary_color || '#059669'}</p>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Logo URL</h3>
            <p class="text-lg text-gray-900 break-all">${shop.logo_url || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Favicon URL</h3>
            <p class="text-lg text-gray-900 break-all">${shop.favicon_url || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Facebook</h3>
            <p class="text-lg text-gray-900 break-all">${shop.facebook_url || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Instagram</h3>
            <p class="text-lg text-gray-900 break-all">${shop.instagram_url || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Twitter</h3>
            <p class="text-lg text-gray-900 break-all">${shop.twitter_url || 'N/A'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">WhatsApp</h3>
            <p class="text-lg text-gray-900">${shop.whatsapp_number || 'N/A'}</p>
        </div>
    `;

    // Operating Hours Tab
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    const hoursHtml = days.map(day => {
        const closed = shop[`${day}_closed`];
        let hours = 'Closed';
        
        if (!closed && shop[`${day}_open`] && shop[`${day}_close`]) {
            hours = formatTime(shop[`${day}_open`]) + ' - ' + formatTime(shop[`${day}_close`]);
        } else if (closed) {
            hours = 'Closed';
        } else {
            hours = 'Not set';
        }
        
        return `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">${day.charAt(0).toUpperCase() + day.slice(1)}</h3>
                <p class="text-lg text-gray-900">${hours}</p>
            </div>
        `;
    }).join('');
    document.getElementById('shop-hours-info').innerHTML = hoursHtml;

    // Actions
    const actionsHtml = [];
    
    if (!isArchived) {
        actionsHtml.push(`
            <a href="/admin/shops/${shop.slug}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Shop
            </a>
            <button id="toggle-status-btn" class="px-4 py-2 ${shop.is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700'} text-white rounded-lg">
                ${shop.is_active ? 'Deactivate' : 'Activate'} Shop
            </button>
            <button id="archive-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Archive Shop
            </button>
        `);
    } else {
        actionsHtml.push(`
            <button id="restore-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Restore Shop
            </button>
            <button id="permanent-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Permanently Delete
            </button>
        `);
    }

    document.getElementById('shop-actions').innerHTML = actionsHtml.join('');

    // Attach event listeners
    attachActionListeners(isArchived);
}

// Attach event listeners to action buttons
function attachActionListeners(isArchived) {
    if (!isArchived) {
        const toggleBtn = document.getElementById('toggle-status-btn');
        const archiveBtn = document.getElementById('archive-btn');
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', confirmToggleStatus);
        }
        
        if (archiveBtn) {
            archiveBtn.addEventListener('click', confirmArchiveShop);
        }
    } else {
        const restoreBtn = document.getElementById('restore-btn');
        const permanentDeleteBtn = document.getElementById('permanent-delete-btn');
        
        if (restoreBtn) {
            restoreBtn.addEventListener('click', restoreShop);
        }
        
        if (permanentDeleteBtn) {
            permanentDeleteBtn.addEventListener('click', confirmPermanentDeleteShop);
        }
    }
}

// Confirm toggle status (requires second click)
function confirmToggleStatus(event) {
    const newStatus = !currentShop.is_active;
    const action = newStatus ? 'activate' : 'deactivate';
    window.toast.warning(`Click again to ${action} shop`, 3000);
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = `Confirm ${action.charAt(0).toUpperCase() + action.slice(1)}`;
    
    btn.removeEventListener('click', confirmToggleStatus);
    btn.addEventListener('click', toggleShopStatus, { once: true });
    
    setTimeout(() => {
        btn.textContent = originalText;
        btn.removeEventListener('click', toggleShopStatus);
        btn.addEventListener('click', confirmToggleStatus);
    }, 3000);
}

// Toggle shop status
async function toggleShopStatus() {
    const shopSlug = getShopSlug();
    if (!shopSlug || !currentShop) return;

    const newStatus = !currentShop.is_active;
    const action = newStatus ? 'activate' : 'deactivate';

    try {
        // Send required fields along with is_active to pass validation
        await axios.patch(`/api/admin/shops/${shopSlug}`, {
            name: currentShop.name,
            slug: currentShop.slug,
            phone: currentShop.phone,
            email: currentShop.email,
            is_active: newStatus
        });
        window.toast.success(`Shop ${action}d successfully!`);
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error updating shop status:', error);
        window.toast.error(error.response?.data?.message || 'Failed to update shop status');
    }
}

// Confirm archive shop (requires second click)
function confirmArchiveShop(event) {
    window.toast.warning('Click Archive again to confirm', 3000);
    const btn = event.target;
    btn.textContent = 'Confirm Archive';
    
    btn.removeEventListener('click', confirmArchiveShop);
    btn.addEventListener('click', archiveShop, { once: true });
    
    setTimeout(() => {
        btn.textContent = 'Archive Shop';
        btn.removeEventListener('click', archiveShop);
        btn.addEventListener('click', confirmArchiveShop);
    }, 3000);
}

// Archive the shop
async function archiveShop() {
    const shopSlug = getShopSlug();
    if (!shopSlug) return;

    try {
        await axios.delete(`/api/admin/shops/${shopSlug}`);
        window.toast.success('Shop archived successfully!');
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error archiving shop:', error);
        window.toast.error(error.response?.data?.message || 'Failed to archive shop');
    }
}

// Restore archived shop
async function restoreShop() {
    const shopSlug = getShopSlug();
    if (!shopSlug) return;

    try {
        await axios.post(`/api/admin/shops/${shopSlug}/restore`);
        window.toast.success('Shop restored successfully!');
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error restoring shop:', error);
        window.toast.error(error.response?.data?.message || 'Failed to restore shop');
    }
}

// Confirm permanent delete (requires second click)
function confirmPermanentDeleteShop(event) {
    window.toast.warning('Click Delete again to permanently delete', 3000);
    const btn = event.target;
    btn.textContent = 'Confirm Permanent Delete';
    
    btn.removeEventListener('click', confirmPermanentDeleteShop);
    btn.addEventListener('click', permanentDeleteShop, { once: true });
    
    setTimeout(() => {
        btn.textContent = 'Permanently Delete';
        btn.removeEventListener('click', permanentDeleteShop);
        btn.addEventListener('click', confirmPermanentDeleteShop);
    }, 3000);
}

// Permanently delete the shop
async function permanentDeleteShop() {
    const shopSlug = getShopSlug();
    if (!shopSlug) return;

    try {
        await axios.delete(`/api/admin/shops/${shopSlug}/force`);
        window.toast.success('Shop permanently deleted!');
        setTimeout(() => window.location.href = '/admin/shops', 1500);
    } catch (error) {
        console.error('Error deleting shop:', error);
        window.toast.error(error.response?.data?.message || 'Failed to delete shop');
    }
}

// Format time from 24-hour to 12-hour with AM/PM
function formatTime(time24) {
    if (!time24) return '';
    const parts = time24.split(':');
    let hour = parseInt(parts[0]);
    const minute = parts[1];
    
    const period = hour >= 12 ? 'PM' : 'AM';
    hour = hour > 12 ? hour - 12 : (hour == 0 ? 12 : hour);
    
    return `${hour}:${minute} ${period}`;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeTabs();
    loadShop();
});
