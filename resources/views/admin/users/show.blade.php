@extends('admin.layout')

@section('title', 'User Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/admin/users" class="text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
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

    <!-- User Details -->
    <div id="user-details" class="hidden">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- User Header -->
            <div id="user-header" class="p-6 border-b"></div>

            <!-- User Info -->
            <div id="user-info" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6"></div>

            <!-- Addresses Section -->
            <div id="addresses-section" class="p-6 border-t"></div>

            <!-- Orders Section -->
            <div id="orders-section" class="p-6 border-t"></div>

            <!-- Actions -->
            <div id="user-actions" class="p-6 bg-gray-50 border-t flex gap-4"></div>
        </div>
    </div>
</div>

<script>
    const userId = {{ request()->route('id') }};
    let currentUser = null;

    async function loadUser() {
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
                <button onclick="editUser()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit User
                </button>
                <button onclick="changeUserRole()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Change Role
                </button>
                <button onclick="archiveUser()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Archive User
                </button>
            `);
        } else {
            actionsHtml.push(`
                <button onclick="restoreUser()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Restore User
                </button>
                <button onclick="permanentlyDeleteUser()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Permanently Delete
                </button>
            `);
        }

        document.getElementById('user-actions').innerHTML = actionsHtml.join('');
    }

    async function loadAddressesAndOrders() {
        document.getElementById('addresses-section').innerHTML = '<p class="text-gray-600">Loading addresses...</p>';
        document.getElementById('orders-section').innerHTML = '<p class="text-gray-600">Loading orders...</p>';
        
        // In a real app, you'd fetch these from API
        document.getElementById('addresses-section').innerHTML = `
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Saved Addresses</h3>
            <p class="text-gray-700">Address information not available in detail view</p>
        `;
        
        document.getElementById('orders-section').innerHTML = `
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Order History</h3>
            <p class="text-gray-700">Order history not available in detail view</p>
        `;
    }

    function editUser() {
        toast.info('Edit functionality coming soon!');
    }

    function changeUserRole() {
        const newRole = currentUser.role === 'admin' ? 'customer' : 'admin';
        const confirmMsg = `Are you sure you want to change this user's role to ${newRole}?`;
        
        if (!confirm(confirmMsg)) {
            return;
        }

        axios.patch(`/api/admin/users/${userId}`, { role: newRole })
            .then(response => {
                toast.success('User role updated successfully!');
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(error => {
                console.error('Error updating role:', error);
                toast.error(error.response?.data?.message || 'Failed to update role');
            });
    }

    async function archiveUser() {
        if (!confirm('Are you sure you want to archive this user?')) {
            return;
        }

        try {
            await axios.delete(`/api/admin/users/${userId}`);
            toast.success('User archived successfully!');
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            console.error('Error archiving user:', error);
            toast.error(error.response?.data?.message || 'Failed to archive user');
        }
    }

    async function restoreUser() {
        try {
            await axios.post(`/api/admin/users/${userId}/restore`);
            toast.success('User restored successfully!');
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            console.error('Error restoring user:', error);
            toast.error(error.response?.data?.message || 'Failed to restore user');
        }
    }

    async function permanentlyDeleteUser() {
        if (!confirm('Are you sure you want to PERMANENTLY delete this user? This action cannot be undone!')) {
            return;
        }

        try {
            await axios.delete(`/api/admin/users/${userId}/force`);
            toast.success('User permanently deleted!');
            setTimeout(() => window.location.href = '/admin/users', 1000);
        } catch (error) {
            console.error('Error deleting user:', error);
            toast.error(error.response?.data?.message || 'Failed to delete user');
        }
    }

    // Load user on page load
    loadUser();
</script>
@endsection
