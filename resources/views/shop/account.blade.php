@extends('shop.layout')

@section('title', 'My Account')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Auth Check -->
    <div id="auth-check" class="hidden">
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-8 rounded-lg text-center">
            <i class="fas fa-lock text-4xl mb-4"></i>
            <h2 class="text-2xl font-bold mb-2">Please Login to Continue</h2>
            <p class="mb-4">You need to be logged in to manage your account.</p>
            <a href="/shop/login?redirect=/account" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                Login / Sign Up
            </a>
        </div>
    </div>

    <div id="account-content" class="hidden">
        <!-- Page Header -->
        <div id="account-message" class="hidden p-4 rounded mb-6 flex items-center gap-2">
            <i class="fas"></i>
            <span></span>
        </div>

        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex flex-wrap gap-6">
                <button class="tab-button border-b-2 border-green-600 py-2 px-1 text-sm font-medium text-green-600" data-tab="contact">
                    Contact Details
                </button>
                <button class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="addresses">
                    Address Book
                </button>
                <button class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="orders">
                    Order History
                </button>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Contact Details -->
                <div id="tab-contact" class="tab-panel bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Contact Details</h2>
                    <form id="profile-form" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Full Name</label>
                                <input type="text" id="profile-name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Your name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Email</label>
                                <input type="email" id="profile-email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="you@example.com">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Phone</label>
                            <input type="text" id="profile-phone" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50" readonly>
                        </div>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold">
                            Save Changes
                        </button>
                    </form>
                </div>

                <!-- Address Book -->
                <div id="tab-addresses" class="tab-panel bg-white rounded-lg shadow-md p-6 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold">Address Book</h2>
                        <button type="button" id="toggle-address-form" class="text-green-600 hover:text-green-700 font-semibold">
                            + Add New Address
                        </button>
                    </div>

                    <div id="addresses-list" class="space-y-4">
                        <p class="text-gray-600">Loading your addresses...</p>
                    </div>

                    <div id="address-form-wrapper" class="hidden mt-6 border-t pt-6">
                        <h3 class="font-semibold mb-4" id="address-form-title">Add Address</h3>
                        <form id="address-form" class="space-y-4">
                            <input type="hidden" id="address-id">
                            <div>
                                <label class="block text-sm font-medium mb-1">Label</label>
                                <input type="text" id="address-label" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Home, Work">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Postcode *</label>
                                <div class="flex gap-2">
                                    <input type="text" id="address-postcode" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="e.g. SW1A 1AA">
                                    <button type="button" id="postcode-lookup-btn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded">Find Address</button>
                                </div>
                                <select id="address-select" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-2 hidden"></select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Address Line 1 *</label>
                                <input type="text" id="address-line-1" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Address Line 2</label>
                                <input type="text" id="address-line-2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">City *</label>
                                    <input type="text" id="address-city" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">County</label>
                                    <input type="text" id="address-county" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Delivery Instructions</label>
                                <textarea id="address-instructions" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                            </div>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" id="address-default" class="w-4 h-4">
                                Set as default address
                            </label>
                            <div class="flex items-center gap-3">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold" id="address-submit">Save Address</button>
                                <button type="button" class="text-gray-600 hover:text-gray-800" id="address-cancel">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order History -->
                <div id="tab-orders" class="tab-panel bg-white rounded-lg shadow-md p-6 hidden">
                    <h2 class="text-xl font-bold mb-4">Order History</h2>
                    <div id="orders-list" class="space-y-4">
                        <p class="text-gray-600">Loading your orders...</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-8">
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">My Account</h2>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p class="text-gray-600 mt-1">Manage your contact details, addresses, and orders.</p>
                        <div class="flex justify-between">
                            <span>Member since</span>
                            <span id="member-since">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Orders</span>
                            <span id="order-count">0</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        <a href="/products" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold">
                            Continue Shopping
                        </a>
                        <a href="/cart" class="block w-full text-center border border-green-600 text-green-600 hover:bg-green-50 py-2 rounded-lg font-semibold">
                            View Cart
                        </a>
                        <button id="request-deletion-btn" class="block w-full text-center border border-red-600 text-red-600 hover:bg-red-50 py-2 rounded-lg font-semibold">
                            Request Account Deletion
                        </button>
                        <button id="logout-btn-account" class="hidden w-full text-center border border-red-600 text-red-600 hover:bg-red-50 py-2 rounded-lg font-semibold">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Account Deletion Modal -->
<div id="delete-account-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Request Account Deletion</h3>
            <button type="button" id="delete-account-modal-close" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to request account deletion? An admin will review your request.</p>
        <div class="flex justify-end gap-3">
            <button type="button" id="delete-account-cancel" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                Cancel
            </button>
            <button type="button" id="delete-account-confirm" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                Request Deletion
            </button>
        </div>
    </div>
</div>

<script>
    // Postcode.io integration helpers
    async function fetchAddressesByPostcode(postcode) {
        // Replace with your getaddress.io API key
        const apiKey = 'YOUR_GETADDRESS_API_KEY';
        const cleaned = postcode.replace(/\s+/g, '');
        const url = `https://api.getaddress.io/find/${encodeURIComponent(cleaned)}?api-key=${apiKey}`;
        try {
            const response = await fetch(url);
            if (!response.ok) return null;
            const data = await response.json();
            if (!data.addresses) return null;
            return data.addresses;
        } catch (e) {
            return null;
        }
    }

    class AccountPage {
        constructor() {
            this.user = null;
            this.addresses = [];
            this.orders = [];
            this.initAuth();
        }

        initAuth() {
            const token = localStorage.getItem('token');
            if (!token) {
                document.getElementById('auth-check').classList.remove('hidden');
                return;
            }

            axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

            axios.get('/api/auth/user')
                .then(response => {
                    this.user = response.data;
                    document.getElementById('auth-check').classList.add('hidden');
                    document.getElementById('account-content').classList.remove('hidden');
                    this.init();
                })
                .catch(() => {
                    localStorage.removeItem('token');
                    delete axios.defaults.headers.common['Authorization'];
                    document.getElementById('auth-check').classList.remove('hidden');
                });
        }

        init() {
            this.fillProfile();
            this.bindProfileForm();
            this.bindAddressForm();
            this.loadAddresses();
            this.loadOrders();
            this.setupTabs();
            this.configureDeletionRequest();
        }

        configureDeletionRequest() {
            const requestBtn = document.getElementById('request-deletion-btn');
            if (!requestBtn) return;

            if (!this.user || this.user.role !== 'customer') {
                requestBtn.disabled = true;
                requestBtn.classList.add('opacity-50', 'cursor-not-allowed');
                requestBtn.textContent = 'Deletion Unavailable';

                const messageBox = document.getElementById('account-message');
                if (messageBox) {
                    messageBox.classList.remove('hidden', 'bg-green-50', 'border-green-200', 'text-green-700');
                    messageBox.classList.add('bg-yellow-50', 'border', 'border-yellow-200', 'text-yellow-700');
                    messageBox.querySelector('i').className = 'fas fa-exclamation-triangle';
                    messageBox.querySelector('span').textContent = 'Account deletion requests are only available for customer accounts.';
                }
            }
        }

        setupTabs() {
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    this.activateTab(button.dataset.tab);
                });
            });

            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab') || 'contact';
            this.activateTab(tab);

            if (tab === 'addresses' && params.get('open') === '1') {
                this.openAddressForm();
            }
        }

        activateTab(tab) {
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-green-600', 'text-green-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.add('hidden');
            });

            const activeButton = document.querySelector(`.tab-button[data-tab="${tab}"]`);
            const activePanel = document.getElementById(`tab-${tab}`);
            if (activeButton && activePanel) {
                activeButton.classList.remove('border-transparent', 'text-gray-500');
                activeButton.classList.add('border-green-600', 'text-green-600');
                activePanel.classList.remove('hidden');
            }
        }

        openAddressForm() {
            document.getElementById('address-form-wrapper').classList.remove('hidden');
            document.getElementById('address-form-wrapper').scrollIntoView({ behavior: 'smooth' });
        }

        showMessage(type, message) {
            const container = document.getElementById('account-message');
            const icon = container.querySelector('i');
            const text = container.querySelector('span');

            container.className = 'p-4 rounded mb-6 flex items-center gap-2';
            if (type === 'success') {
                container.classList.add('bg-green-50', 'border', 'border-green-200', 'text-green-700');
                icon.className = 'fas fa-check-circle';
            } else {
                container.classList.add('bg-red-50', 'border', 'border-red-200', 'text-red-700');
                icon.className = 'fas fa-exclamation-circle';
            }
            text.textContent = message;
            container.classList.remove('hidden');
        }

        fillProfile() {
            document.getElementById('profile-name').value = this.user?.name || '';
            document.getElementById('profile-email').value = this.user?.email || '';
            document.getElementById('profile-phone').value = this.user?.phone || '';
            document.getElementById('member-since').textContent = this.user?.created_at ? new Date(this.user.created_at).toLocaleDateString() : '-';
        }

        bindProfileForm() {
            document.getElementById('profile-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                try {
                    const payload = {
                        name: document.getElementById('profile-name').value.trim(),
                        email: document.getElementById('profile-email').value.trim()
                    };

                    const response = await axios.patch('/api/auth/profile', payload);
                    this.user = response.data.user;
                    this.fillProfile();
                    this.showMessage('success', 'Contact details updated successfully');
                } catch (error) {
                    const message = error.response?.data?.message || 'Failed to update contact details';
                    this.showMessage('error', message);
                }
            });
        }

        bindAddressForm() {
                        // Postcode lookup logic
                        const postcodeInput = document.getElementById('address-postcode');
                        const lookupBtn = document.getElementById('postcode-lookup-btn');
                        const addressSelect = document.getElementById('address-select');
                        lookupBtn.addEventListener('click', async () => {
                            const postcode = postcodeInput.value.trim();
                            if (!postcode) return;
                            lookupBtn.disabled = true;
                            lookupBtn.textContent = 'Searching...';
                            addressSelect.innerHTML = '';
                            addressSelect.classList.add('hidden');
                            const addresses = await fetchAddressesByPostcode(postcode);
                            lookupBtn.disabled = false;
                            lookupBtn.textContent = 'Find Address';
                            if (!addresses || !addresses.length) {
                                addressSelect.innerHTML = '<option>No addresses found</option>';
                                addressSelect.classList.remove('hidden');
                                return;
                            }
                            addressSelect.innerHTML = '<option value="">Select your address</option>' + addresses.map(addr => `<option value="${addr}">${addr}</option>`).join('');
                            addressSelect.classList.remove('hidden');
                        });
                        addressSelect.addEventListener('change', () => {
                            const val = addressSelect.value;
                            if (!val) return;
                            // UK addresses: [line1, line2, line3, city, county, postcode]
                            const parts = val.split(',').map(x => x.trim());
                            document.getElementById('address-line-1').value = parts[0] || '';
                            document.getElementById('address-line-2').value = parts[1] || '';
                            document.getElementById('address-city').value = parts[3] || '';
                            document.getElementById('address-county').value = parts[4] || '';
                        });
            document.getElementById('toggle-address-form').addEventListener('click', () => {
                this.activateTab('addresses');
                document.getElementById('address-form-wrapper').classList.toggle('hidden');
            });

            document.getElementById('address-cancel').addEventListener('click', () => {
                this.resetAddressForm();
            });

            document.getElementById('address-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const addressId = document.getElementById('address-id').value;

                const payload = {
                    label: document.getElementById('address-label').value.trim() || null,
                    address_line_1: document.getElementById('address-line-1').value.trim(),
                    address_line_2: document.getElementById('address-line-2').value.trim() || null,
                    city: document.getElementById('address-city').value.trim(),
                    county: document.getElementById('address-county').value.trim() || null,
                    postcode: document.getElementById('address-postcode').value.trim(),
                    delivery_instructions: document.getElementById('address-instructions').value.trim() || null,
                    is_default: document.getElementById('address-default').checked
                };

                try {
                    if (addressId) {
                        await axios.put(`/api/addresses/${addressId}`, payload);
                        this.showMessage('success', 'Address updated successfully');
                    } else {
                        await axios.post('/api/addresses', payload);
                        this.showMessage('success', 'Address added successfully');
                    }

                    await this.loadAddresses();
                    this.resetAddressForm();
                } catch (error) {
                    const message = error.response?.data?.message || 'Failed to save address';
                    this.showMessage('error', message);
                }
            });
        }

        resetAddressForm() {
            document.getElementById('address-form').reset();
            document.getElementById('address-id').value = '';
            document.getElementById('address-form-title').textContent = 'Add Address';
            document.getElementById('address-submit').textContent = 'Save Address';
            document.getElementById('address-form-wrapper').classList.add('hidden');
        }

        editAddressById(addressId) {
            const address = this.addresses.find(item => item.id === addressId);
            if (!address) return;

            document.getElementById('address-id').value = address.id;
            document.getElementById('address-label').value = address.label || '';
            document.getElementById('address-line-1').value = address.address_line_1 || '';
            document.getElementById('address-line-2').value = address.address_line_2 || '';
            document.getElementById('address-city').value = address.city || '';
            document.getElementById('address-county').value = address.county || '';
            document.getElementById('address-postcode').value = address.postcode || '';
            document.getElementById('address-instructions').value = address.delivery_instructions || '';
            document.getElementById('address-default').checked = !!address.is_default;
            document.getElementById('address-form-title').textContent = 'Edit Address';
            document.getElementById('address-submit').textContent = 'Update Address';
            document.getElementById('address-form-wrapper').classList.remove('hidden');
            document.getElementById('address-form-wrapper').scrollIntoView({ behavior: 'smooth' });
        }

        async deleteAddress(addressId) {
            const address = this.addresses.find(item => item.id === addressId);
            if (address?.is_default) {
                this.showMessage('error', 'Default address cannot be deleted. Set another address as default first.');
                return;
            }
            if (!confirm('Delete this address?')) return;
            try {
                await axios.delete(`/api/addresses/${addressId}`);
                this.showMessage('success', 'Address deleted successfully');
                this.loadAddresses();
            } catch (error) {
                const message = error.response?.data?.message || 'Failed to delete address';
                this.showMessage('error', message);
            }
        }

        async makeDefaultAddress(addressId) {
            try {
                const address = this.addresses.find(item => item.id === addressId);
                if (!address) {
                    this.showMessage('error', 'Address not found');
                    return;
                }

                await axios.put(`/api/addresses/${addressId}`, {
                    label: address.label || null,
                    address_line_1: address.address_line_1,
                    address_line_2: address.address_line_2 || null,
                    city: address.city,
                    county: address.county || null,
                    postcode: address.postcode,
                    delivery_instructions: address.delivery_instructions || null,
                    is_default: true
                });
                this.showMessage('success', 'Default address updated');
                this.loadAddresses();
            } catch (error) {
                const message = error.response?.data?.message || 'Failed to update default address';
                this.showMessage('error', message);
            }
        }

        async loadAddresses() {
            try {
                const response = await axios.get('/api/addresses');
                this.addresses = response.data || [];

                if (!this.addresses.length) {
                    document.getElementById('addresses-list').innerHTML = '<p class="text-gray-600">No saved addresses. Add your first address below.</p>';
                    return;
                }

                document.getElementById('addresses-list').innerHTML = this.addresses.map(address => `
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="font-semibold flex items-center gap-2">
                                    ${address.label ? address.label : 'Address'}
                                    ${address.is_default ? '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Default</span>' : ''}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">${address.address_line_1}</div>
                                ${address.address_line_2 ? `<div class="text-sm text-gray-600">${address.address_line_2}</div>` : ''}
                                <div class="text-sm text-gray-600">${address.city}${address.county ? ', ' + address.county : ''} ${address.postcode}</div>
                                ${address.delivery_instructions ? `<div class="text-xs text-gray-500 mt-1">${address.delivery_instructions}</div>` : ''}
                            </div>
                            <div class="flex items-center gap-3">
                                <button class="text-green-600 hover:text-green-800 text-sm" onclick="accountPage.editAddressById(${address.id})">Edit</button>
                                ${address.is_default
                                    ? '<span class="text-xs text-gray-400">Default</span>'
                                    : `<button class="text-blue-600 hover:text-blue-800 text-sm" onclick="accountPage.makeDefaultAddress(${address.id})">Make Default</button>
                                       <button class="text-red-600 hover:text-red-800 text-sm" onclick="accountPage.deleteAddress(${address.id})">Delete</button>`
                                }
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                document.getElementById('addresses-list').innerHTML = '<p class="text-red-600">Failed to load addresses</p>';
            }
        }

        async loadOrders() {
            try {
                const response = await axios.get('/api/orders');
                const orders = response.data.data || response.data || [];
                this.orders = orders;

                document.getElementById('order-count').textContent = orders.length || 0;

                if (!orders.length) {
                    document.getElementById('orders-list').innerHTML = '<p class="text-gray-600">You have not placed any orders yet.</p>';
                    return;
                }

                document.getElementById('orders-list').innerHTML = orders.map(order => `
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="font-semibold">Order #${order.id}</div>
                                <div class="text-sm text-gray-600">${new Date(order.created_at).toLocaleDateString()} • ${order.items?.length || 0} items</div>
                                <div class="text-sm text-gray-600">Status: <span class="font-medium">${order.status.replace('_', ' ')}</span></div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold">£${parseFloat(order.total || 0).toFixed(2)}</div>
                                <a href="/order-confirmation/${order.id}" class="text-green-600 hover:text-green-700 text-sm">View Details</a>
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                document.getElementById('orders-list').innerHTML = '<p class="text-red-600">Failed to load orders</p>';
            }
        }
    }

    const accountPage = new AccountPage();

    // Logout button handlers
    const logoutBtnAccount = document.getElementById('logout-btn-account');
    
    function checkAuthStatusAccount() {
        const token = localStorage.getItem('token');
        if (token) {
            if (logoutBtnAccount) logoutBtnAccount.classList.remove('hidden');
        } else {
            if (logoutBtnAccount) logoutBtnAccount.classList.add('hidden');
        }
    }

    async function handleLogout(e) {
        e.preventDefault();
        try {
            const token = localStorage.getItem('token');
            await axios.post('/api/auth/logout', {}, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
        } catch (error) {
            console.error('Logout error:', error);
        }
        localStorage.removeItem('token');
        localStorage.removeItem('shopping_cart');
        window.location.href = '/';
    }

    if (logoutBtnAccount) {
        logoutBtnAccount.addEventListener('click', handleLogout);
    }

    checkAuthStatusAccount();
    window.addEventListener('storage', checkAuthStatusAccount);

    // Request deletion handler
    const deleteModal = document.getElementById('delete-account-modal');
    const openDeleteBtn = document.getElementById('request-deletion-btn');
    const closeDeleteBtn = document.getElementById('delete-account-modal-close');
    const cancelDeleteBtn = document.getElementById('delete-account-cancel');
    const confirmDeleteBtn = document.getElementById('delete-account-confirm');

    function openDeleteModal() {
        if (deleteModal) {
            deleteModal.classList.remove('hidden');
        }
    }

    function closeDeleteModal() {
        if (deleteModal) {
            deleteModal.classList.add('hidden');
        }
    }

    openDeleteBtn?.addEventListener('click', openDeleteModal);
    closeDeleteBtn?.addEventListener('click', closeDeleteModal);
    cancelDeleteBtn?.addEventListener('click', closeDeleteModal);

    confirmDeleteBtn?.addEventListener('click', async () => {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                const messageBox = document.getElementById('account-message');
                if (messageBox) {
                    messageBox.classList.remove('hidden', 'bg-green-50', 'border-green-200', 'text-green-700');
                    messageBox.classList.add('bg-red-50', 'border', 'border-red-200', 'text-red-700');
                    messageBox.querySelector('i').className = 'fas fa-exclamation-circle';
                    messageBox.querySelector('span').textContent = 'Please log in to request account deletion.';
                }
                closeDeleteModal();
                return;
            }

            await axios.post('/api/auth/request-deletion', {}, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const messageBox = document.getElementById('account-message');
            if (messageBox) {
                messageBox.classList.remove('hidden', 'bg-red-50', 'border-red-200', 'text-red-700');
                messageBox.classList.add('bg-green-50', 'border', 'border-green-200', 'text-green-700');
                messageBox.querySelector('i').className = 'fas fa-check-circle';
                messageBox.querySelector('span').textContent = 'Your account deletion request has been submitted.';
            }
            if (openDeleteBtn) {
                openDeleteBtn.disabled = true;
                openDeleteBtn.textContent = 'Deletion Requested';
                openDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            closeDeleteModal();
        } catch (error) {
            console.error('Error requesting deletion:', error);
            const messageBox = document.getElementById('account-message');
            if (messageBox) {
                messageBox.classList.remove('hidden', 'bg-green-50', 'border-green-200', 'text-green-700');
                messageBox.classList.add('bg-red-50', 'border', 'border-red-200', 'text-red-700');
                messageBox.querySelector('i').className = 'fas fa-exclamation-circle';
                messageBox.querySelector('span').textContent = 'Failed to submit deletion request. Please try again.';
            }
            closeDeleteModal();
        }
    });
</script>
@endsection
