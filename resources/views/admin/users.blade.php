@extends('admin.layout')

@section('title', 'Users')

@section('content')
<h2 class="text-3xl font-bold mb-6">Users</h2>

<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 border-b">
        <div class="flex gap-4">
            <input type="text" id="search" placeholder="Search by phone or name..." class="flex-1 px-4 py-2 border rounded-lg">
            <select id="role-filter" class="px-4 py-2 border rounded-lg">
                <option value="">All Roles</option>
                <option value="customer">Customers</option>
                <option value="admin">Admins</option>
            </select>
        </div>
    </div>
    
    <table class="min-w-full divide-y divide-gray-200" id="users-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Users will be loaded here -->
        </tbody>
    </table>
</div>

<script>
    async function loadUsers() {
        try {
            const search = document.getElementById('search').value;
            const role = document.getElementById('role-filter').value;
            
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (role) params.append('role', role);
            
            const response = await axios.get(`/api/admin/users?${params}`);
            const users = response.data.data;
            const tbody = document.querySelector('#users-table tbody');
            
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No users found</td></tr>';
                return;
            }
            
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${user.phone}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${user.name || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${user.email || 'N/A'}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'}">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${user.orders_count || 0}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${new Date(user.created_at).toLocaleDateString()}</td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="viewUser(${user.id})" class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                        ${user.role !== 'admin' ? `<button onclick="makeAdmin(${user.id})" class="text-green-600 hover:text-green-900">Make Admin</button>` : ''}
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading users:', error);
            alert('Failed to load users');
        }
    }
    
    function viewUser(id) {
        alert(`View user ${id} details - coming soon!`);
    }
    
    async function makeAdmin(id) {
        if (!confirm('Are you sure you want to make this user an admin?')) return;
        
        try {
            await axios.patch(`/api/admin/users/${id}`, { role: 'admin' });
            alert('User promoted to admin successfully');
            loadUsers();
        } catch (error) {
            console.error('Error updating user role:', error);
            alert('Failed to update user role');
        }
    }
    
    document.getElementById('search').addEventListener('input', () => {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(loadUsers, 500);
    });
    
    document.getElementById('role-filter').addEventListener('change', loadUsers);
    
    loadUsers();
</script>
@endsection
