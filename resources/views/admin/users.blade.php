@extends('admin.layout')

@section('title', 'Users')

@section('content')
<h2 class="text-3xl font-bold mb-6">Users</h2>

<!-- Tabs -->
<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8">
        <button onclick="switchTab('active')" id="active-tab" class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
            Active
        </button>
        <button onclick="switchTab('archived')" id="archived-tab" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
            Archived
        </button>
    </nav>
</div>

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
    let currentTab = 'active';

    function switchTab(tab) {
        currentTab = tab;
        
        // Update tab styling
        const activeTab = document.getElementById('active-tab');
        const archivedTab = document.getElementById('archived-tab');
        
        if (tab === 'active') {
            activeTab.classList.add('border-blue-500', 'text-blue-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            archivedTab.classList.remove('border-blue-500', 'text-blue-600');
            archivedTab.classList.add('border-transparent', 'text-gray-500');
        } else {
            archivedTab.classList.add('border-blue-500', 'text-blue-600');
            archivedTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.remove('border-blue-500', 'text-blue-600');
            activeTab.classList.add('border-transparent', 'text-gray-500');
        }
        
        loadUsers();
    }

    async function loadUsers() {
        try {
            const search = document.getElementById('search').value;
            const role = document.getElementById('role-filter').value;
            
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (role) params.append('role', role);
            if (currentTab === 'archived') params.append('archived', '1');
            
            const response = await axios.get(`/api/admin/users?${params}`);
            const users = response.data.data || response.data;
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
                    <td class="px-6 py-4 text-sm text-right">
                        <a href="/admin/users/${user.id}" class="text-blue-600 hover:text-blue-900 font-medium">
                            View Details
                        </a>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading users:', error);
            toast.error('Failed to load users');
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
