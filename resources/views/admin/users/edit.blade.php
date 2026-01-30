@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/admin/users/{{ $id }}" class="text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
        <p class="mt-2 text-gray-600">Loading user details...</p>
    </div>

    <!-- Error State -->
    <div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <p class="text-red-800"></p>
    </div>

    <!-- Permission Denied -->
    <div id="permission-denied" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
        <i class="fas fa-lock text-4xl text-yellow-600 mb-4"></i>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Permission Denied</h2>
        <p class="text-gray-700 mb-4">Super admin users can only be edited by themselves.</p>
        <a href="/admin/users/{{ $id }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Back to User Details
        </a>
    </div>

    <!-- Edit Form -->
    <div id="edit-form-container" class="hidden">
        <!-- User Info Section -->
        <div class="bg-white shadow-md rounded-lg p-6 max-w-4xl mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">User Information</h2>
            <form id="edit-user-form" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" id="phone" readonly class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Phone number cannot be changed</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" id="name" name="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select id="role" name="role" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                        <option value="shop_manager">Shop Manager</option>
                        <option value="shop_admin">Shop Admin</option>
                    </select>
                </div>
                
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="is_active" name="is_active" class="rounded">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="/admin/users/{{ $id }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Addresses Section -->
        <div class="bg-white shadow-md rounded-lg p-6 max-w-4xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Addresses</h2>
                <button onclick="showAddAddressModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    + Add Address
                </button>
            </div>
            
            <div id="addresses-list" class="space-y-4">
                <p class="text-gray-600">Loading addresses...</p>
            </div>
        </div>
    </div>

    <!-- Add/Edit Address Modal -->
    <div id="address-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="address-modal-title">Add Address</h3>
                <button onclick="closeAddressModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="address-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <input type="tel" id="address-phone" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
                    <input type="text" id="address-line-1" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                    <input type="text" id="address-line-2" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <input type="text" id="address-city" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Postcode *</label>
                        <input type="text" id="address-postcode" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <input type="text" id="address-country" value="United Kingdom" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="address-is-default" class="rounded">
                        <span class="text-sm font-medium text-gray-700">Set as default address</span>
                    </label>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeAddressModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save Address
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const userId = {{ $id }};
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
                        <p class="text-gray-600 text-sm mt-2"><i class="fas fa-phone"></i> ${address.phone}</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="editAddress(${address.id})" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteAddress(${address.id})" class="text-red-600 hover:text-red-800">
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
        document.getElementById('address-phone').value = address.phone;
        document.getElementById('address-line-1').value = address.address_line_1;
        document.getElementById('address-line-2').value = address.address_line_2 || '';
        document.getElementById('address-city').value = address.city;
        document.getElementById('address-postcode').value = address.postcode;
        document.getElementById('address-country').value = address.country || 'United Kingdom';
        document.getElementById('address-is-default').checked = address.is_default;
        
        document.getElementById('address-modal').classList.remove('hidden');
    }

    async function deleteAddress(addressId) {
        if (!confirm('Are you sure you want to delete this address?')) return;

        try {
            await axios.delete(`/api/admin/users/${userId}/addresses/${addressId}`);
            toast.success('Address deleted successfully!');
            loadAddresses();
        } catch (error) {
            console.error('Error deleting address:', error);
            toast.error(error.response?.data?.message || 'Failed to delete address');
        }
    }

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
            toast.success('User updated successfully!');
            setTimeout(() => window.location.href = `/admin/users/${userId}`, 1000);
        } catch (error) {
            console.error('Error updating user:', error);
            toast.error(error.response?.data?.message || 'Failed to update user');
        }
    });

    document.getElementById('address-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const addressData = {
            user_id: userId,
            phone: document.getElementById('address-phone').value,
            address_line_1: document.getElementById('address-line-1').value,
            address_line_2: document.getElementById('address-line-2').value,
            city: document.getElementById('address-city').value,
            postcode: document.getElementById('address-postcode').value,
            country: document.getElementById('address-country').value,
            is_default: document.getElementById('address-is-default').checked
        };
        
        try {
            if (editingAddressId) {
                await axios.put(`/api/admin/users/${userId}/addresses/${editingAddressId}`, addressData);
                toast.success('Address updated successfully!');
            } else {
                await axios.post(`/api/admin/users/${userId}/addresses`, addressData);
                toast.success('Address added successfully!');
            }
            closeAddressModal();
            loadAddresses();
        } catch (error) {
            console.error('Error saving address:', error);
            toast.error(error.response?.data?.message || 'Failed to save address');
        }
    });

    // Load user on page load
    loadUser();
</script>
@endsection
