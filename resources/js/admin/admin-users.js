// Admin Users Management JS

async function loadAdminUsers() {
    try {
        const response = await axios.get('/api/admin/admin-users');
        const users = response.data.data || response.data;
        const tbody = document.querySelector('#admin-users-table tbody');
        
        if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No admin users found</td></tr>';
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
                <tr onclick="window.location.href='/admin/users/${user.id}'" class="hover:bg-gray-50 cursor-pointer transition">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${user.phone}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${user.name || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${user.email || 'N/A'}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${roleColors[user.role] || 'bg-gray-100 text-gray-800'}">
                            ${user.role.replace(/_/g, ' ')}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${user.shop?.name || 'All Shops'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${new Date(user.created_at).toLocaleDateString()}</td>
                </tr>
            `;
        }).join('');
    } catch (error) {
        console.error('Error loading admin users:', error);
        if (typeof toast !== 'undefined') {
            toast.error('Failed to load admin users');
        }
    }
}

function showCreateModal() {
    document.getElementById('create-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('create-modal').classList.add('hidden');
}

// Expose functions to window
window.showCreateModal = showCreateModal;
window.closeModal = closeModal;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Add admin user button (remove onclick if present and use proper event listener)
    const addBtn = document.querySelector('button[onclick*="showCreateModal"]');
    if (addBtn) {
        addBtn.removeAttribute('onclick');
        addBtn.addEventListener('click', showCreateModal);
    }
    
    // Close modal buttons
    document.querySelectorAll('button[onclick*="closeModal"]').forEach(btn => {
        btn.removeAttribute('onclick');
        btn.addEventListener('click', closeModal);
    });
    
    // Form submission
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
    
    loadAdminUsers();
});
