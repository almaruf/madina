@extends('admin.layout')

@section('title', 'Admin Users')

@section('page-title', 'Admin Users')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Admin Users</h2>
    <button onclick="showCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
        + Add Admin User
    </button>
</div>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200" id="admin-users-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shop</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Admin users will be loaded here -->
        </tbody>
    </table>
</div>

<!-- Create Admin User Modal -->
<div id="create-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Add Admin User</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="create-admin-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Phone Number *</label>
                <input type="tel" name="phone" placeholder="+44..." required class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Role *</label>
                <select name="role" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="admin">Admin</option>
                    <option value="shop_manager">Shop Manager</option>
                    <option value="shop_admin">Shop Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Create Admin User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    async function loadAdminUsers() {
        try {
            const response = await axios.get('/api/admin/admin-users');
            const users = response.data.data || response.data;
            const tbody = document.querySelector('#admin-users-table tbody');
            
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No admin users found</td></tr>';
                return;
            }
            
            tbody.innerHTML = users.map(user => {
                const roleColors = {
                    'super_admin': 'bg-red-100 text-red-800',
                    'shop_admin': 'bg-purple-100 text-purple-800',
                    'shop_manager': 'bg-indigo-100 text-indigo-800',
                    'admin': 'bg-blue-100 text-blue-800'
                };
                
                return `
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${user.phone}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${user.name || 'N/A'}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">${user.email || 'N/A'}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded ${roleColors[user.role] || 'bg-gray-100 text-gray-800'}">
                                ${user.role.replace('_', ' ')}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">${user.shop?.name || 'All Shops'}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">${new Date(user.created_at).toLocaleDateString()}</td>
                        <td class="px-6 py-4 text-sm">
                            <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                            ${user.role !== 'super_admin' ? `<button onclick="demoteUser(${user.id})" class="text-orange-600 hover:text-orange-900">Demote</button>` : ''}
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (error) {
            console.error('Error loading admin users:', error);
            toast.error('Failed to load admin users');
        }
    }
    
    function showCreateModal() {
        document.getElementById('create-modal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('create-modal').classList.add('hidden');
    }
    
    document.getElementById('create-admin-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = {
            phone: formData.get('phone'),
            name: formData.get('name'),
            email: formData.get('email'),
            role: formData.get('role')
        };
        
        try {
            await axios.post('/api/admin/users', data);
            toast.success('Admin user created successfully');
            closeModal();
            loadAdminUsers();
            e.target.reset();
        } catch (error) {
            console.error('Error creating admin user:', error);
            toast.error(error.response?.data?.message || 'Failed to create admin user');
        }
    });
    
    function editUser(id) {
        toast.info(`Edit user ${id} - coming soon!`);
    }
    
    async function demoteUser(id) {
        if (!confirm('Are you sure you want to demote this user to customer?')) return;
        
        try {
            await axios.patch(`/api/admin/users/${id}`, { role: 'customer' });
            toast.success('User demoted successfully');
            loadAdminUsers();
        } catch (error) {
            console.error('Error demoting user:', error);
            toast.error('Failed to demote user');
        }
    }
    
    loadAdminUsers();
</script>
@endsection
