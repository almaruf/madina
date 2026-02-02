// User Edit Page JavaScript
// Axios and toast are available globally via bootstrap.js and layout.js

const userId = parseInt(document.body.dataset.userId);
let currentUser = null;
let currentAuthUser = null;
let addresses = [];
let editingAddressId = null;

async function loadUser() {
    try {
        // Get current authenticated user
        const authResponse = await axios.get('/api/auth/user');
        currentAuthUser = authResponse.data;

        // Get user to edit
        const response = await axios.get(`/api/admin/users/${userId}`);
        currentUser = response.data;

        // Check permissions
        if (currentUser.role === 'super_admin' && currentAuthUser.id !== currentUser.id) {
            // Cannot edit super_admin unless you are that super_admin
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('permission-denied').classList.remove('hidden');
            return;
        }

        renderEditForm(currentUser);
        loadAddresses();
    } catch (error) {
        console.error('Error loading user:', error);
        document.getElementById('loading').classList.add('hidden');
        const errorDiv = document.getElementById('error');
        errorDiv.classList.remove('hidden');
        errorDiv.querySelector('p').textContent = error.response?.data?.message || 'Failed to load user details';
    }
}

function renderEditForm(user) {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('edit-form-container').classList.remove('hidden');

    // Populate form fields
    document.getElementById('phone').value = user.phone;
    document.getElementById('name').value = user.name || '';
    document.getElementById('email').value = user.email || '';
    document.getElementById('role').value = user.role;
    document.getElementById('is_active').checked = user.is_active;
}

async function loadAddresses() {
    try {
        const response = await axios.get(`/api/admin/users/${userId}/addresses`);
        addresses = response.data;
        renderAddresses();
    } catch (error) {
        console.error('Error loading addresses:', error);
        document.getElementById('addresses-list').innerHTML = '<p class="text-red-600">Failed to load addresses</p>';
    }
}

function renderAddresses() {
    const container = document.getElementById('addresses-list');
    
    if (addresses.length === 0) {
        container.innerHTML = '<p class="text-gray-600">No addresses added yet.</p>';
        return;
    }

    container.innerHTML = addresses.map(address => `
        <div class="border border-gray-300 rounded-lg p-4 ${address.is_default ? 'border-green-500 bg-green-50' : ''}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        ${address.is_default ? '<span class="px-2 py-1 bg-green-600 text-white text-xs rounded">Default</span>' : ''}
                    </div>
                    <p class="text-gray-700">${address.address_line_1}</p>
                    ${address.address_line_2 ? `<p class="text-gray-700">${address.address_line_2}</p>` : ''}
                    <p class="text-gray-700">${address.city}, ${address.postcode}</p>
                    <p class="text-gray-700">${address.country || 'United Kingdom'}</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.editAddress(${address.id})" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="window.confirmDeleteAddress(${address.id})" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function showAddAddressModal() {
    editingAddressId = null;
    document.getElementById('address-modal-title').textContent = 'Add Address';
    document.getElementById('address-form').reset();
    document.getElementById('address-country').value = 'United Kingdom';
    document.getElementById('address-modal').classList.remove('hidden');
}

function closeAddressModal() {
    document.getElementById('address-modal').classList.add('hidden');
    editingAddressId = null;
}

function editAddress(addressId) {
    const address = addresses.find(a => a.id === addressId);
    if (!address) return;
    editingAddressId = addressId;
    document.getElementById('address-modal-title').textContent = 'Edit Address';
    document.getElementById('address-line-1').value = address.address_line_1;
    document.getElementById('address-line-2').value = address.address_line_2 || '';
    document.getElementById('address-city').value = address.city;
    document.getElementById('address-postcode').value = address.postcode;
    document.getElementById('address-country').value = address.country || 'United Kingdom';
    document.getElementById('address-is-default').checked = address.is_default;
    document.getElementById('address-modal').classList.remove('hidden');
}

function confirmDeleteAddress(addressId) {
    window.toast.warning('Click delete again to confirm', 3000);
    const btn = event.target;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
    btn.onclick = () => deleteAddress(addressId);
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.onclick = () => confirmDeleteAddress(addressId);
    }, 3000);
}

async function deleteAddress(addressId) {
    try {
        await axios.delete(`/api/admin/users/${userId}/addresses/${addressId}`);
        window.toast.success('Address deleted successfully!');
        loadAddresses();
    } catch (error) {
        console.error('Error deleting address:', error);
        window.toast.error(error.response?.data?.message || 'Failed to delete address');
    }
}

// Postcode.io integration
async function searchPostcode() {
    const postcode = document.getElementById('postcode-search').value.trim();
    const resultsDiv = document.getElementById('postcode-results');
    resultsDiv.innerHTML = '';
    if (!postcode) return;
    resultsDiv.innerHTML = '<span class="text-gray-500">Searching...</span>';
    try {
        const res = await fetch(`https://api.postcodes.io/postcodes/${encodeURIComponent(postcode)}`);
        const data = await res.json();
        if (data.status !== 200 || !data.result) {
            resultsDiv.innerHTML = '<span class="text-red-600">No address found for this postcode.</span>';
            return;
        }
        const address = data.result;
        const line1 = address.parliamentary_constituency || address.admin_ward || address.admin_district || address.region || '';
        const city = address.admin_district || address.region || '';
        const country = address.country || 'United Kingdom';
        resultsDiv.innerHTML = `<button type="button" class="block w-full text-left px-3 py-2 border border-gray-300 rounded-lg hover:bg-blue-50 mb-2" onclick="window.autofillAddressFromPostcode('${address.postcode.replace(/'/g, '')}','${line1.replace(/'/g, '')}','${city.replace(/'/g, '')}','${country.replace(/'/g, '')}')">
            ${line1 ? line1 + ', ' : ''}${city}, ${address.postcode}, ${country}
        </button>`;
    } catch (e) {
        resultsDiv.innerHTML = '<span class="text-red-600">Failed to fetch address.</span>';
    }
}

function autofillAddressFromPostcode(postcode, line1, city, country) {
    document.getElementById('address-line-1').value = line1;
    document.getElementById('address-city').value = city;
    document.getElementById('address-postcode').value = postcode;
    document.getElementById('address-country').value = country;
    document.getElementById('postcode-results').innerHTML = '';
}

// Form submission handlers
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('edit-user-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            role: document.getElementById('role').value,
            is_active: document.getElementById('is_active').checked
        };
        
        try {
            await axios.patch(`/api/admin/users/${userId}`, formData);
            window.toast.success('User updated successfully!');
            setTimeout(() => window.location.href = `/admin/users/${userId}`, 1000);
        } catch (error) {
            console.error('Error updating user:', error);
            window.toast.error(error.response?.data?.message || 'Failed to update user');
        }
    });

    document.getElementById('address-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const addressData = {
            user_id: userId,
            address_line_1: document.getElementById('address-line-1')?.value || '',
            address_line_2: document.getElementById('address-line-2')?.value || '',
            city: document.getElementById('address-city')?.value || '',
            postcode: document.getElementById('address-postcode')?.value || '',
            country: document.getElementById('address-country')?.value || '',
            is_default: document.getElementById('address-is-default')?.checked || false
        };
        
        try {
            if (editingAddressId) {
                await axios.put(`/api/admin/users/${userId}/addresses/${editingAddressId}`, addressData);
                window.toast.success('Address updated successfully!');
            } else {
                await axios.post(`/api/admin/users/${userId}/addresses`, addressData);
                window.toast.success('Address added successfully!');
            }
            closeAddressModal();
            loadAddresses();
        } catch (error) {
            console.error('Error saving address:', error);
            window.toast.error(error.response?.data?.message || 'Failed to save address');
        }
    });

    // Load user on page load
    loadUser();
});

// Expose functions needed by inline handlers
window.showAddAddressModal = showAddAddressModal;
window.closeAddressModal = closeAddressModal;
window.editAddress = editAddress;
window.confirmDeleteAddress = confirmDeleteAddress;
window.searchPostcode = searchPostcode;
window.autofillAddressFromPostcode = autofillAddressFromPostcode;
