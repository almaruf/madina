// Admin Shops JS
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
    
    loadShops();
}

async function loadShops() {
    try {
        const url = currentTab === 'archived' ? '/api/admin/shops?archived=1' : '/api/admin/shops';
        const response = await axios.get(url);
        const shops = response.data.data || response.data;
        const tbody = document.querySelector('#shops-table tbody');
        
        if (shops.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No shops found</td></tr>';
            return;
        }
        
        tbody.innerHTML = shops.map(shop => `
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${shop.name}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${shop.domain || 'N/A'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${shop.city || 'N/A'}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${shop.phone || 'N/A'}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${shop.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${shop.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-right">
                    <a href="/admin/shops/${shop.slug}" class="text-blue-600 hover:text-blue-900 font-medium">
                        View Details
                    </a>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading shops:', error);
        if (typeof toast !== 'undefined') {
            toast.error('Failed to load shops');
        }
    }
}

// Expose function to window for onclick handlers
window.switchTab = switchTab;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Tab buttons
    document.getElementById('active-tab')?.addEventListener('click', () => switchTab('active'));
    document.getElementById('archived-tab')?.addEventListener('click', () => switchTab('archived'));
    
    loadShops();
});
