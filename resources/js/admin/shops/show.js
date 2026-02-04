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

// ===== BANNER MANAGEMENT =====

function renderBannersSection(shop) {
    const banners = shop.banners || [];
    const bannerCount = banners.length;
    const remainingSlots = 5 - bannerCount;

    const bannersContainer = document.getElementById('shop-banners-section');
    bannersContainer.innerHTML = `
        <h3 class="text-lg font-bold mb-4">Shop Banners (${bannerCount}/5)</h3>
        
        <!-- Upload Section -->
        <div class="mb-6">
            <div class="flex flex-col gap-3">
                <input type="file" id="banner-upload" accept="image/jpeg,image/png,image/webp" multiple class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" ${bannerCount >= 5 ? 'disabled' : ''}>
                <button id="upload-banner-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold ${bannerCount >= 5 ? 'opacity-50 cursor-not-allowed' : ''}" ${bannerCount >= 5 ? 'disabled' : ''}>
                    <i class="fas fa-upload mr-2"></i>Upload Banners ${remainingSlots > 0 ? `(${remainingSlots} remaining)` : '(Maximum reached)'}
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div id="banner-upload-progress" class="hidden mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div id="banner-progress-bar" class="bg-blue-600 h-2 transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="banner-progress-text" class="text-sm text-gray-600 mt-1">Uploading...</p>
            </div>
            
            <!-- Validation Errors -->
            <div id="banner-validation-errors" class="hidden mt-3 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm"></div>
        </div>
        
        <!-- Banner Gallery -->
        <div id="banner-gallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            ${banners.length > 0 ? banners.map(banner => `
                <div class="relative group cursor-move" draggable="true" data-banner-id="${banner.id}" data-banner-order="${banner.order}">
                    <img src="${banner.signed_thumbnail_url || banner.signed_url || banner.thumbnail_url || banner.url}" alt="${banner.title || shop.name}" class="w-full h-[150px] object-cover rounded-lg border-2 ${banner.is_primary ? 'border-blue-500' : 'border-gray-200'}">
                    
                    <!-- Primary Badge -->
                    ${banner.is_primary ? '<div class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">Primary</div>' : ''}
                    
                    <!-- Hover Controls -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
                        ${!banner.is_primary ? `<button class="set-primary-banner-btn bg-blue-600 hover:bg-blue-700 text-white p-2 rounded" data-banner-id="${banner.id}" title="Set as Primary">
                            <i class="fas fa-star"></i>
                        </button>` : ''}
                        <button class="delete-banner-btn bg-red-600 hover:bg-red-700 text-white p-2 rounded" data-banner-id="${banner.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('') : '<p class="text-gray-500 text-center col-span-full py-8">No banners uploaded yet</p>'}
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-banner-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md mx-4">
                <h3 class="text-lg font-bold mb-4">Delete Banner</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this banner? This action cannot be undone.</p>
                <div class="flex gap-4 justify-end">
                    <button id="cancel-delete-banner" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button id="confirm-delete-banner" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Delete</button>
                </div>
            </div>
        </div>
    `;

    // Attach event listeners
    attachBannerEventListeners();
}

function attachBannerEventListeners() {
    // Upload button
    const uploadBtn = document.getElementById('upload-banner-btn');
    if (uploadBtn) {
        uploadBtn.addEventListener('click', handleBannerUpload);
    }

    // Delete buttons
    document.querySelectorAll('.delete-banner-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const bannerId = e.currentTarget.dataset.bannerId;
            showDeleteBannerModal(bannerId);
        });
    });

    // Set primary buttons
    document.querySelectorAll('.set-primary-banner-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const bannerId = e.currentTarget.dataset.bannerId;
            await setPrimaryBanner(bannerId);
        });
    });

    // Drag and drop for reordering
    attachBannerDragAndDropListeners();
}

function validateBannerFiles(files) {
    const errors = [];
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    
    const currentBannerCount = document.querySelectorAll('[data-banner-id]').length;
    
    if (currentBannerCount + files.length > 5) {
        errors.push(`Can only upload ${5 - currentBannerCount} more banner(s). Maximum is 5 banners per shop.`);
        return errors;
    }
    
    for (let file of files) {
        if (!allowedTypes.includes(file.type)) {
            errors.push(`${file.name}: Invalid file type. Only JPEG, PNG, and WebP are allowed.`);
        }
        if (file.size > maxSize) {
            errors.push(`${file.name}: File too large. Maximum size is 5MB.`);
        }
    }
    
    return errors;
}

async function handleBannerUpload() {
    const fileInput = document.getElementById('banner-upload');
    const files = Array.from(fileInput.files);
    
    if (files.length === 0) {
        window.toast.error('Please select at least one image');
        return;
    }
    
    // Validate files
    const errors = validateBannerFiles(files);
    const errorsDiv = document.getElementById('banner-validation-errors');
    
    if (errors.length > 0) {
        errorsDiv.innerHTML = errors.map(err => `<p>• ${err}</p>`).join('');
        errorsDiv.classList.remove('hidden');
        return;
    }
    
    errorsDiv.classList.add('hidden');
    
    // Prepare form data
    const formData = new FormData();
    files.forEach(file => {
        formData.append('image[]', file);
    });
    
    // Show progress
    const progressDiv = document.getElementById('banner-upload-progress');
    const progressBar = document.getElementById('banner-progress-bar');
    const progressText = document.getElementById('banner-progress-text');
    progressDiv.classList.remove('hidden');
    
    try {
        const response = await new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                    progressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                }
            });
            
            xhr.addEventListener('load', () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(JSON.parse(xhr.responseText));
                } else {
                    reject(JSON.parse(xhr.responseText));
                }
            });
            
            xhr.addEventListener('error', () => reject({ message: 'Upload failed' }));
            
            xhr.open('POST', `/api/admin/shops/${currentShop.slug}/banners`);
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            if (token) {
                xhr.setRequestHeader('Authorization', `Bearer ${token}`);
            }
            xhr.send(formData);
        });
        
        progressDiv.classList.add('hidden');
        progressBar.style.width = '0%';
        fileInput.value = '';
        
        window.toast.success('Banners uploaded successfully!');
        
        // Reload shop data to refresh banner list
        await loadShop();
        
        // Switch to banners tab to show the new banners
        const bannersTab = document.querySelector('[data-tab="banners"]');
        if (bannersTab) bannersTab.click();
        
    } catch (error) {
        console.error('Upload error:', error);
        progressDiv.classList.add('hidden');
        window.toast.error(error.message || 'Failed to upload banners');
        
        if (error.errors) {
            const errorMessages = Object.values(error.errors).flat();
            errorsDiv.innerHTML = errorMessages.map(err => `<p>• ${err}</p>`).join('');
            errorsDiv.classList.remove('hidden');
        }
    }
}

let bannerToDelete = null;

function showDeleteBannerModal(bannerId) {
    bannerToDelete = bannerId;
    const modal = document.getElementById('delete-banner-modal');
    modal.classList.remove('hidden');
    
    document.getElementById('cancel-delete-banner').onclick = () => {
        modal.classList.add('hidden');
        bannerToDelete = null;
    };
    
    document.getElementById('confirm-delete-banner').onclick = async () => {
        await deleteBanner(bannerToDelete);
        modal.classList.add('hidden');
        bannerToDelete = null;
    };
}

async function deleteBanner(bannerId) {
    try {
        await axios.delete(`/api/admin/shops/${currentShop.slug}/banners/${bannerId}`);
        window.toast.success('Banner deleted successfully!');
        await loadShop();
        
        // Re-render banners tab
        const bannersTab = document.querySelector('[data-tab="banners"]');
        if (bannersTab) bannersTab.click();
        
    } catch (error) {
        console.error('Delete error:', error);
        window.toast.error(error.response?.data?.message || 'Failed to delete banner');
    }
}

async function setPrimaryBanner(bannerId) {
    try {
        await axios.patch(`/api/admin/shops/${currentShop.slug}/banners/${bannerId}/set-primary`);
        window.toast.success('Primary banner updated!');
        await loadShop();
        
        // Re-render banners tab
        const bannersTab = document.querySelector('[data-tab="banners"]');
        if (bannersTab) bannersTab.click();
        
    } catch (error) {
        console.error('Set primary error:', error);
        window.toast.error(error.response?.data?.message || 'Failed to set primary banner');
    }
}

function attachBannerDragAndDropListeners() {
    const bannerItems = document.querySelectorAll('[data-banner-id]');
    let draggedElement = null;
    
    bannerItems.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.style.opacity = '0.4';
        });
        
        item.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
            bannerItems.forEach(i => i.classList.remove('border-4', 'border-blue-400'));
        });
        
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-4', 'border-blue-400');
        });
        
        item.addEventListener('dragleave', function(e) {
            this.classList.remove('border-4', 'border-blue-400');
        });
        
        item.addEventListener('drop', async function(e) {
            e.preventDefault();
            this.classList.remove('border-4', 'border-blue-400');
            
            if (draggedElement !== this) {
                // Get all banner items
                const allBanners = Array.from(document.querySelectorAll('[data-banner-id]'));
                const draggedIndex = allBanners.indexOf(draggedElement);
                const targetIndex = allBanners.indexOf(this);
                
                // Reorder in DOM
                if (draggedIndex < targetIndex) {
                    this.parentNode.insertBefore(draggedElement, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedElement, this);
                }
                
                // Update order on server
                await updateBannerOrder();
            }
        });
    });
}

async function updateBannerOrder() {
    const bannerItems = document.querySelectorAll('[data-banner-id]');
    const bannersData = Array.from(bannerItems).map((item, index) => ({
        id: parseInt(item.dataset.bannerId),
        order: index
    }));
    
    try {
        await axios.post(`/api/admin/shops/${currentShop.slug}/banners/reorder`, {
            banners: bannersData
        });
        
        window.toast.success('Banners reordered successfully!');
        
    } catch (error) {
        console.error('Reorder error:', error);
        window.toast.error(error.response?.data?.message || 'Failed to reorder banners');
        
        // Reload to get correct order
        await loadShop();
        const bannersTab = document.querySelector('[data-tab="banners"]');
        if (bannersTab) bannersTab.click();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeTabs();
    loadShop();
    
    // Re-render banners section when banners tab is clicked
    const bannersTabBtn = document.querySelector('[data-tab="banners"]');
    if (bannersTabBtn) {
        bannersTabBtn.addEventListener('click', () => {
            if (currentShop) {
                renderBannersSection(currentShop);
            }
        });
    }
});
