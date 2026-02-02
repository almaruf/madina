// Admin Users JS
let allUsers = [];
let currentTab = 'active';

async function loadUsers() {
    try {
        const role = document.getElementById('role-filter').value;
        let url = '/api/admin/users';
        const params = new URLSearchParams();
        
        if (role !== 'all') {
            params.append('role', role);
        }
        if (currentTab === 'archived') {
            params.append('archived', '1');
        }
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        const response = await axios.get(url);
        allUsers = response.data.data || response.data;
        renderUsers();
    } catch (error) {
        console.error('Error loading users:', error);
        document.getElementById('users-table').innerHTML = '<tr><td colspan="5" class="text-center text-red-600 py-4">Failed to load users</td></tr>';
    }
}

function renderUsers() {
    const tbody = document.getElementById('users-table');
    
    if (allUsers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-gray-600 py-4">No users found</td></tr>';
        return;
    }
    
    tbody.innerHTML = allUsers.map(user => {
        const roleColors = {
            'super_admin': 'bg-red-100 text-red-800',
            'admin': 'bg-purple-100 text-purple-800',
            'owner': 'bg-blue-100 text-blue-800',
            'staff': 'bg-green-100 text-green-800',
            'customer': 'bg-gray-100 text-gray-800'
        };
        
        return `
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/users/${user.id}'">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${user.name || 'N/A'}</div>
                    <div class="text-sm text-gray-500">${user.phone}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">${user.email || 'N/A'}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${roleColors[user.role] || 'bg-gray-100 text-gray-800'}">
                        ${user.role}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">${new Date(user.created_at).toLocaleDateString()}</td>
                <td class="px-6 py-4 text-sm">
                    <span class="px-2 py-1 text-xs rounded ${user.deleted_at ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                        ${user.deleted_at ? 'Archived' : 'Active'}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

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

function showCreateModal() {
    toast.info('User creation form coming soon!');
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('active-tab').addEventListener('click', () => switchTab('active'));
    document.getElementById('archived-tab').addEventListener('click', () => switchTab('archived'));
    document.getElementById('role-filter').addEventListener('change', loadUsers);
    loadUsers();
});
