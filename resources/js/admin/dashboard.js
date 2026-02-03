// Admin Dashboard JS
const ADMIN_SHOP_STORAGE_KEY = 'admin_selected_shop_id';

async function loadDashboardStats(shopId = null) {
    try {
        let url = '/api/admin/dashboard/stats';
        if (shopId) {
            const params = new URLSearchParams();
            params.append('shop_id', shopId);
            url += `?${params.toString()}`;
        }
        const response = await axios.get(url);
        const data = response.data;
        
        // Update stats
        document.getElementById('total-orders').textContent = data.total_orders;
        document.getElementById('pending-orders').textContent = data.pending_orders;
        document.getElementById('today-orders').textContent = data.today_orders;
        document.getElementById('total-revenue').textContent = '£' + data.total_revenue;
        document.getElementById('total-products').textContent = data.total_products;
        document.getElementById('total-customers').textContent = data.total_customers;
        
        // Update recent orders
        const ordersContainer = document.getElementById('recent-orders');
        
        if (data.recent_orders && data.recent_orders.length > 0) {
            ordersContainer.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shop</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${data.recent_orders.map(order => `
                                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/orders/${order.id}'">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${order.order_number}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        ${order.shop ? `${order.shop.name}${order.shop.city ? ' • ' + order.shop.city : ''}` : '—'}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${order.user?.name || 'Guest'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${order.items?.length || 0} items</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">£${parseFloat(order.total).toFixed(2)}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded ${getStatusColor(order.status)}">
                                            ${order.status}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">${new Date(order.created_at).toLocaleDateString()}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            ordersContainer.innerHTML = '<p class="text-gray-600">No orders yet</p>';
        }
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
        if (typeof toast !== 'undefined') {
            toast.error('Failed to load dashboard data');
        }
    }
}

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

function renderShopSelector(shops, selectedShopId) {
    const section = document.getElementById('shop-selector-section');
    const container = document.getElementById('shop-selector-cards');

    if (!section || !container) {
        return;
    }

    section.classList.remove('hidden');

    const allCard = {
        id: 'all',
        name: 'All Shops',
        city: 'Aggregated view'
    };

    const cards = [allCard, ...shops];

    container.innerHTML = cards.map(shop => {
        const isSelected = String(selectedShopId) === String(shop.id);
        return `
            <button
                class="text-left border rounded-lg p-4 transition ${isSelected ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:border-green-400'}"
                data-shop-id="${shop.id}"
            >
                <p class="text-lg font-semibold text-gray-900">${shop.name}</p>
                <p class="text-sm text-gray-500">${shop.city || ''}</p>
            </button>
        `;
    }).join('');

    container.querySelectorAll('button[data-shop-id]').forEach(button => {
        button.addEventListener('click', () => {
            const shopId = button.getAttribute('data-shop-id');
            localStorage.setItem(ADMIN_SHOP_STORAGE_KEY, shopId);
            renderShopSelector(shops, shopId);
            loadDashboardStats(shopId);
        });
    });
}

function getStatusColor(status) {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'confirmed': 'bg-blue-100 text-blue-800',
        'processing': 'bg-indigo-100 text-indigo-800',
        'ready': 'bg-purple-100 text-purple-800',
        'out_for_delivery': 'bg-orange-100 text-orange-800',
        'delivered': 'bg-green-100 text-green-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const user = await loadAuthUser();

        if (!user || !['admin', 'super_admin'].includes(user.role)) {
            loadDashboardStats();
            return;
        }

        const shops = await loadShops();
        const context = resolveShopContext(shops);

        if (!context.showSelector && context.shopId) {
            loadDashboardStats(context.shopId);
            return;
        }

        const savedShopId = localStorage.getItem(ADMIN_SHOP_STORAGE_KEY);
        const validSaved = savedShopId && shops.some(shop => String(shop.id) === String(savedShopId));
        const selectedShopId = validSaved ? savedShopId : 'all';

        renderShopSelector(shops, selectedShopId);
        loadDashboardStats(selectedShopId);
    } catch (error) {
        console.error('Error initializing dashboard:', error);
        loadDashboardStats();
    }
});
