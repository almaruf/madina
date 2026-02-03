// Admin Orders JS
const ADMIN_SHOP_STORAGE_KEY = 'admin_selected_shop_id';
let allOrders = [];
let currentTab = 'active';
let currentShopId = null;

async function loadAuthUser() {
    const response = await axios.get('/api/auth/user');
    return response.data;
}

async function loadShops() {
    const response = await axios.get('/api/admin/shops');
    return response.data.data || response.data || [];
}

function resolveShopContext(shops) {
    const params = new URLSearchParams(window.location.search);
    const shopSlug = params.get('shop');
    if (shopSlug) {
        const slugMatch = shops.find(shop => shop.slug === shopSlug);
        if (slugMatch) {
            return { shopId: String(slugMatch.id), showSelector: false };
        }
    }

    const host = window.location.hostname;
    const domainMatch = shops.find(shop => shop.domain && shop.domain === host);
    if (domainMatch) {
        return { shopId: String(domainMatch.id), showSelector: false };
    }

    return { shopId: null, showSelector: true };
}

async function initShopSelection() {
    try {
        const user = await loadAuthUser();
        if (!user || !['admin', 'super_admin'].includes(user.role)) {
            currentShopId = null;
            return;
        }

        const shops = await loadShops();
        const context = resolveShopContext(shops);

        if (!context.showSelector && context.shopId) {
            currentShopId = context.shopId;
            return;
        }

        const savedShopId = localStorage.getItem(ADMIN_SHOP_STORAGE_KEY);
        const validSaved = savedShopId && shops.some(shop => String(shop.id) === String(savedShopId));
        currentShopId = validSaved ? savedShopId : 'all';
    } catch (error) {
        console.error('Error initializing shop selection:', error);
        currentShopId = null;
    }
}

async function loadOrders() {
    try {
        const status = document.getElementById('status-filter').value;
        let url = '/api/admin/orders';
        const params = new URLSearchParams();

        if (currentShopId) {
            params.append('shop_id', currentShopId);
        }
        
        if (status !== 'all') {
            params.append('status', status);
        }
        if (currentTab === 'archived') {
            params.append('archived', '1');
        }
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        const response = await axios.get(url);
        allOrders = response.data.data || response.data;
        renderOrders();
    } catch (error) {
        console.error('Error loading orders:', error);
        document.getElementById('orders-table').innerHTML = '<tr><td colspan="8" class="text-center text-red-600 py-4">Failed to load orders</td></tr>';
    }
}

function renderOrders() {
    const tbody = document.getElementById('orders-table');
    
    if (allOrders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-gray-600 py-4">No orders found</td></tr>';
        return;
    }
    
    tbody.innerHTML = allOrders.map(order => {
        const statusColors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'confirmed': 'bg-blue-100 text-blue-800',
            'processing': 'bg-indigo-100 text-indigo-800',
            'ready': 'bg-purple-100 text-purple-800',
            'out_for_delivery': 'bg-orange-100 text-orange-800',
            'delivered': 'bg-green-100 text-green-800',
            'completed': 'bg-green-100 text-green-800',
            'cancelled': 'bg-red-100 text-red-800',
        };
        
        return `
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/orders/${order.id}'">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${order.order_number}</td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${order.shop ? `${order.shop.name}${order.shop.city ? ' • ' + order.shop.city : ''}` : '—'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">${order.user?.name || 'Guest'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${order.items?.length || 0} items</td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-900">£${parseFloat(order.total).toFixed(2)}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${statusColors[order.status] || 'bg-gray-100 text-gray-800'}">
                        ${order.status}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">${order.fulfillment_type}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${new Date(order.created_at).toLocaleDateString()}</td>
            </tr>
        `;
    }).join('');
}

function switchTab(tab) {
    currentTab = tab;
    
    // Update tab styling
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-green-600', 'text-green-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeBtn = document.getElementById(`tab-${tab}`);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-green-600', 'text-green-600');
    
    loadOrders();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    document.getElementById('tab-active').addEventListener('click', () => switchTab('active'));
    document.getElementById('tab-archived').addEventListener('click', () => switchTab('archived'));
    document.getElementById('status-filter').addEventListener('change', loadOrders);
    await initShopSelection();
    loadOrders();
});
