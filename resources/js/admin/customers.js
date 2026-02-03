const ADMIN_SHOP_STORAGE_KEY = 'admin_selected_shop_id';
let currentTab = 'active';
let currentShopId = null;

async function loadCustomers() {
    try {
        let url = currentTab === 'active' ? '/api/admin/customers' : '/api/admin/customers/removal-requests';
        
        if (currentShopId) {
            const params = new URLSearchParams();
            params.append('shop_id', currentShopId);
            url += `?${params.toString()}`;
        }
        
        const response = await axios.get(url);
        const customers = response.data.data || response.data;
        
        renderCustomers(customers);
    } catch (error) {
        console.error('Error loading customers:', error);
        document.getElementById('customers-table').innerHTML = 
            '<tr><td colspan="6" class="text-center text-red-600 py-4">Failed to load customers</td></tr>';
    }
}

function renderCustomers(customers) {
    const tbody = document.getElementById('customers-table');
    
    if (customers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-gray-600 py-4">No customers found</td></tr>';
        return;
    }
    
    tbody.innerHTML = customers.map(customer => {
        const actionButton = currentTab === 'removal' 
            ? `<button onclick="removeCustomer(${customer.id})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                 Permanently Remove
               </button>`
            : `<span class="text-gray-400 text-sm">—</span>`;
            
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">${customer.phone || '—'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${customer.email || '—'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${customer.city || '—'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${customer.orders_count}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${new Date(customer.created_at).toLocaleDateString()}</td>
                <td class="px-6 py-4">${actionButton}</td>
            </tr>
        `;
    }).join('');
}

async function removeCustomer(customerId) {
    if (!confirm('Are you sure you want to PERMANENTLY remove this customer? This action cannot be undone and will delete all their personal information.')) {
        return;
    }
    
    try {
        await axios.delete(`/api/admin/customers/${customerId}`);
        toast.success('Customer permanently removed');
        loadCustomers();
        loadRemovalCount();
    } catch (error) {
        console.error('Error removing customer:', error);
        toast.error('Failed to remove customer');
    }
}

function switchTab(tab) {
    currentTab = tab;
    
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-green-600', 'text-green-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeBtn = document.getElementById(`tab-${tab}`);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-green-600', 'text-green-600');
    
    loadCustomers();
}

async function loadRemovalCount() {
    try {
        let url = '/api/admin/customers/removal-requests';
        if (currentShopId) {
            url += `?shop_id=${currentShopId}`;
        }
        const response = await axios.get(url);
        const count = (response.data.data || response.data).length;
        const badge = document.getElementById('removal-count');
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error loading removal count:', error);
    }
}

// Make removeCustomer global
window.removeCustomer = removeCustomer;

document.addEventListener('DOMContentLoaded', async () => {
    document.getElementById('tab-active').addEventListener('click', () => switchTab('active'));
    document.getElementById('tab-removal').addEventListener('click', () => switchTab('removal'));
    
    try {
        const sessionResponse = await axios.get('/api/admin/shop-selected');
        currentShopId = sessionResponse.data.shop_id;
    } catch (error) {
        console.error('Error getting selected shop:', error);
        currentShopId = null;
    }
    
    loadCustomers();
    loadRemovalCount();
});
