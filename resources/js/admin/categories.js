// Admin Categories JS
let allCategories = [];
let currentTab = 'active';

async function loadCategories() {
    try {
        const url = currentTab === 'archived' ? '/api/admin/categories?archived=1' : '/api/admin/categories';
        const response = await axios.get(url);
        allCategories = response.data.data || response.data;
        renderCategories();
    } catch (error) {
        console.error('Error loading categories:', error);
        document.getElementById('categories-table').innerHTML = '<tr><td colspan="5" class="text-center text-red-600 py-4">Failed to load categories</td></tr>';
    }
}

function renderCategories() {
    const tbody = document.getElementById('categories-table');
    
    if (allCategories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-gray-600 py-4">No categories found</td></tr>';
        return;
    }
    
    tbody.innerHTML = allCategories.map(category => `
        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/categories/${category.slug}'">
            <td class="px-6 py-4">
                ${category.image_url 
                    ? `<img src="${category.image_url}" class="w-12 h-12 object-cover rounded">`
                    : '<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'
                }
            </td>
            <td class="px-6 py-4">
                <div class="font-medium text-gray-900">${category.name}</div>
                <div class="text-sm text-gray-500">${category.description || 'No description'}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">${category.products_count || 0}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded ${category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${category.is_active ? 'Active' : 'Inactive'}
                </span>
            </td>
        </tr>
    `).join('');
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
    
    loadCategories();
}

function showCreateModal() {
    window.location.href = '/admin/categories/create';
}

// Expose functions for inline onclick handlers
window.showCreateModal = showCreateModal;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('active-tab').addEventListener('click', () => switchTab('active'));
    document.getElementById('archived-tab').addEventListener('click', () => switchTab('archived'));
    loadCategories();
});
