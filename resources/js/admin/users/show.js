// Admin User Show Page
// Handles user details display and management

let currentUser = null;

// Get user ID from the page
function getUserId() {
    const userIdElement = document.querySelector('[data-user-id]');
    return userIdElement ? parseInt(userIdElement.dataset.userId) : null;
}

// Load user data from API
async function loadUser() {
    const userId = getUserId();
    if (!userId) {
        console.error('User ID not found');
        return;
    }

    try {
        const response = await axios.get(`/api/admin/users/${userId}`);
        currentUser = response.data;
        renderUser(currentUser);
        
        // Load addresses and orders count
        loadAddressesAndOrders();
    } catch (error) {
        console.error('Error loading user:', error);
        document.getElementById('loading').classList.add('hidden');
        const errorDiv = document.getElementById('error');
        errorDiv.classList.remove('hidden');
        errorDiv.querySelector('p').textContent = error.response?.data?.message || 'Failed to load user details';
    }
}

// Render user details to the page
function renderUser(user) {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('user-details').classList.remove('hidden');

    const isArchived = user.deleted_at !== null;

    // Header
    document.getElementById('user-header').innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">${user.name}</h2>
                ${isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>' : ''}
                <span class="inline-block mt-2 px-3 py-1 ${user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'} text-sm font-medium rounded-full">${user.role}</span>
            </div>
        </div>
    `;

    // Info
    document.getElementById('user-info').innerHTML = `
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Name</h3>
            <p class="text-lg text-gray-900">${user.name}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Phone Number</h3>
            <p class="text-lg text-gray-900">${user.phone}</p>
        </div>
        ${user.email ? `
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Email</h3>
                <p class="text-lg text-gray-900">${user.email}</p>
            </div>
        ` : ''}
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Role</h3>
            <p class="text-gray-900 capitalize">${user.role}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Member Since</h3>
            <p class="text-gray-900">${new Date(user.created_at).toLocaleDateString()}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Last Updated</h3>
            <p class="text-gray-900">${new Date(user.updated_at).toLocaleString()}</p>
        </div>
    `;

    // Actions
    const actionsHtml = [];
    
    if (!isArchived) {
        actionsHtml.push(`
            <a href="/admin/users/${userId}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-block">
                Edit User
            </a>
            <button id="archive-btn" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                Archive User
            </button>
        `);
    } else {
        actionsHtml.push(`
            <button id="restore-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Restore User
            </button>
            <button id="permanent-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Permanently Delete
            </button>
        `);
    }

    document.getElementById('user-actions').innerHTML = actionsHtml.join('');

    // Attach event listeners
    attachActionListeners(isArchived);
}

// Attach event listeners to action buttons
function attachActionListeners(isArchived) {
    if (!isArchived) {
        const archiveBtn = document.getElementById('archive-btn');
        if (archiveBtn) {
            archiveBtn.addEventListener('click', confirmArchiveUser);
        }
    } else {
        const restoreBtn = document.getElementById('restore-btn');
        const permanentDeleteBtn = document.getElementById('permanent-delete-btn');
        
        if (restoreBtn) {
            restoreBtn.addEventListener('click', restoreUser);
        }
        
        if (permanentDeleteBtn) {
            permanentDeleteBtn.addEventListener('click', confirmPermanentDeleteUser);
        }
    }
}

// Load addresses and orders for the user
async function loadAddressesAndOrders() {
    const userId = getUserId();
    if (!userId) return;

    document.getElementById('addresses-section').innerHTML = '<p class="text-gray-600">Loading addresses...</p>';
    document.getElementById('orders-section').innerHTML = '<p class="text-gray-600">Loading orders...</p>';
    
    // Load addresses
    try {
        const addressResponse = await axios.get(`/api/admin/users/${userId}/addresses`);
        const addresses = addressResponse.data;
        
        let addressesHtml = '<h3 class="text-lg font-semibold text-gray-900 mb-4">Saved Addresses</h3>';
        
        if (addresses.length === 0) {
            addressesHtml += '<p class="text-gray-600">No addresses saved</p>';
        } else {
            addressesHtml += '<div class="space-y-3">';
            addresses.forEach(address => {
                addressesHtml += `
                    <div class="border border-gray-200 rounded-lg p-4 ${address.is_default ? 'border-green-500 bg-green-50' : ''}">
                        <div class="flex items-center gap-2 mb-2">
                            <h4 class="font-semibold">${address.first_name} ${address.last_name}</h4>
                            ${address.is_default ? '<span class="px-2 py-1 bg-green-600 text-white text-xs rounded">Default</span>' : ''}
                        </div>
                        <p class="text-gray-700 text-sm">${address.address_line_1}</p>
                        ${address.address_line_2 ? `<p class="text-gray-700 text-sm">${address.address_line_2}</p>` : ''}
                        <p class="text-gray-700 text-sm">${address.city}, ${address.postcode}</p>
                        <p class="text-gray-700 text-sm">${address.country}</p>
                    </div>
                `;
            });
            addressesHtml += '</div>';
        }
        
        document.getElementById('addresses-section').innerHTML = addressesHtml;
    } catch (error) {
        console.error('Error loading addresses:', error);
        document.getElementById('addresses-section').innerHTML = `
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Saved Addresses</h3>
            <p class="text-red-600">Failed to load addresses</p>
        `;
    }
    
    // Load orders
    try {
        const ordersResponse = await axios.get(`/api/admin/orders?user_id=${userId}`);
        const orders = ordersResponse.data.data || ordersResponse.data;
        
        let ordersHtml = '<h3 class="text-lg font-semibold text-gray-900 mb-4">Order History</h3>';
        
        if (orders.length === 0) {
            ordersHtml += '<p class="text-gray-600">No orders placed yet</p>';
        } else {
            ordersHtml += '<div class="space-y-3">';
            orders.forEach(order => {
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'confirmed': 'bg-blue-100 text-blue-800',
                    'processing': 'bg-indigo-100 text-indigo-800',
                    'ready': 'bg-purple-100 text-purple-800',
                    'out_for_delivery': 'bg-orange-100 text-orange-800',
                    'delivered': 'bg-green-100 text-green-800',
                    'completed': 'bg-green-100 text-green-800',
                    'cancelled': 'bg-red-100 text-red-800',
                    'refunded': 'bg-gray-100 text-gray-800'
                };
                
                ordersHtml += `
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer order-card" data-order-id="${order.id}">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">Order #${order.id}</h4>
                                <p class="text-sm text-gray-600">${new Date(order.created_at).toLocaleDateString()}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded ${statusColors[order.status] || 'bg-gray-100 text-gray-800'}">
                                ${order.status.replace(/_/g, ' ')}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-sm text-gray-600">${order.items?.length || 0} items</p>
                            <p class="font-semibold text-gray-900">£${parseFloat(order.total).toFixed(2)}</p>
                        </div>
                    </div>
                `;
            });
            ordersHtml += '</div>';
        }
        
        document.getElementById('orders-section').innerHTML = ordersHtml;

        // Attach click handlers to order cards
        document.querySelectorAll('.order-card').forEach(card => {
            card.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                window.location.href = `/admin/orders/${orderId}`;
            });
        });
    } catch (error) {
        console.error('Error loading orders:', error);
        document.getElementById('orders-section').innerHTML = `
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Order History</h3>
            <p class="text-red-600">Failed to load order history</p>
        `;
    }
}

// Confirm archive user (requires second click)
function confirmArchiveUser(event) {
    window.toast.warning('Click Archive again to confirm', 3000);
    const btn = event.target;
    btn.textContent = 'Confirm Archive';
    
    btn.removeEventListener('click', confirmArchiveUser);
    btn.addEventListener('click', archiveUser, { once: true });
    
    setTimeout(() => {
        btn.textContent = 'Archive';
        btn.removeEventListener('click', archiveUser);
        btn.addEventListener('click', confirmArchiveUser);
    }, 3000);
}

// Archive the user
async function archiveUser() {
    const userId = getUserId();
    if (!userId) return;

    try {
        await axios.delete(`/api/admin/users/${userId}`);
        window.toast.success('User archived successfully!');
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error archiving user:', error);
        window.toast.error(error.response?.data?.message || 'Failed to archive user');
    }
}

// Restore the user
async function restoreUser() {
    const userId = getUserId();
    if (!userId) return;

    try {
        await axios.post(`/api/admin/users/${userId}/restore`);
        window.toast.success('User restored successfully!');
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error restoring user:', error);
        window.toast.error(error.response?.data?.message || 'Failed to restore user');
    }
}

// Confirm permanent delete (requires second click)
function confirmPermanentDeleteUser(event) {
    window.toast.warning('Click Delete again to PERMANENTLY delete', 3000);
    const btn = event.target;
    btn.textContent = 'Confirm Delete';
    
    btn.removeEventListener('click', confirmPermanentDeleteUser);
    btn.addEventListener('click', permanentlyDeleteUser, { once: true });
    
    setTimeout(() => {
        btn.textContent = 'Permanent Delete';
        btn.removeEventListener('click', permanentlyDeleteUser);
        btn.addEventListener('click', confirmPermanentDeleteUser);
    }, 3000);
}

// Permanently delete the user
async function permanentlyDeleteUser() {
    const userId = getUserId();
    if (!userId) return;

    try {
        await axios.delete(`/api/admin/users/${userId}/force`);
        window.toast.success('User permanently deleted!');
        setTimeout(() => window.location.href = '/admin/users', 1000);
    } catch (error) {
        console.error('Error deleting user:', error);
        window.toast.error(error.response?.data?.message || 'Failed to delete user');
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Wait a tiny bit to ensure axios interceptors are fully registered
    setTimeout(() => {
        loadUser();
    }, 100);
});
