// Admin Order Show Page
// Handles order details display and status management

let orderData = null;

// Get order ID from the page
function getOrderId() {
    const orderIdElement = document.querySelector('[data-order-id]');
    return orderIdElement ? parseInt(orderIdElement.dataset.orderId) : null;
}

// Load order data from API
async function loadOrder() {
    const orderId = getOrderId();
    if (!orderId) {
        console.error('Order ID not found');
        return;
    }

    try {
        const response = await axios.get(`/api/admin/orders/${orderId}`);
        orderData = response.data.data || response.data;
        renderOrder();
    } catch (error) {
        console.error('Error loading order:', error);
        document.getElementById('order-container').innerHTML = `
            <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                Failed to load order details
            </div>
        `;
    }
}

// Get status badge color classes
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
        'refunded': 'bg-gray-100 text-gray-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

// Render order details to the page
function renderOrder() {
    const isArchived = orderData.deleted_at !== null;
    const totalAmount = parseFloat(orderData.total_amount || 0).toFixed(2);
    
    document.getElementById('order-container').innerHTML = `
        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">Order #${orderData.id}</h2>
            <div class="flex gap-3">
                ${!isArchived ? `
                    <button id="archive-btn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-archive mr-2"></i>Archive
                    </button>
                ` : `
                    <button id="restore-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-undo mr-2"></i>Restore
                    </button>
                `}
            </div>
        </div>
        
        ${isArchived ? '<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i>This order is archived</div>' : ''}
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Order Status</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">Order Status</label>
                        <select id="order-status" class="w-full border rounded-lg px-4 py-2" ${isArchived ? 'disabled' : ''}>
                            <option value="pending" ${orderData.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="confirmed" ${orderData.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                            <option value="processing" ${orderData.status === 'processing' ? 'selected' : ''}>Processing</option>
                            <option value="ready" ${orderData.status === 'ready' ? 'selected' : ''}>Ready</option>
                            <option value="out_for_delivery" ${orderData.status === 'out_for_delivery' ? 'selected' : ''}>Out for Delivery</option>
                            <option value="delivered" ${orderData.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                            <option value="completed" ${orderData.status === 'completed' ? 'selected' : ''}>Completed</option>
                            <option value="cancelled" ${orderData.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            <option value="refunded" ${orderData.status === 'refunded' ? 'selected' : ''}>Refunded</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">Payment Status</label>
                        <select id="payment-status" class="w-full border rounded-lg px-4 py-2" ${isArchived ? 'disabled' : ''}>
                            <option value="pending" ${orderData.payment_status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="paid" ${orderData.payment_status === 'paid' ? 'selected' : ''}>Paid</option>
                            <option value="failed" ${orderData.payment_status === 'failed' ? 'selected' : ''}>Failed</option>
                            <option value="refunded" ${orderData.payment_status === 'refunded' ? 'selected' : ''}>Refunded</option>
                        </select>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="text-base font-semibold capitalize">${orderData.payment_method}</p>
                    </div>
                </div>
            </div>
            
            <!-- Customer & Delivery Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Customer Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-base">${orderData.user?.name || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="text-base">${orderData.user?.phone || 'N/A'}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Delivery Address</p>
                            <p class="text-base">${orderData.address?.address_line_1 || 'N/A'}<br>
                            ${orderData.address?.city || ''}, ${orderData.address?.postcode || ''}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Order Items</h3>
                    <div class="space-y-3">
                        ${orderData.items?.map(item => `
                            <div class="flex justify-between items-center border-b pb-3">
                                <div>
                                    <p class="font-semibold">${item.product_name}</p>
                                    <p class="text-sm text-gray-600">${item.variation_name} × ${item.quantity}</p>
                                </div>
                                <p class="font-semibold">£${parseFloat(item.total).toFixed(2)}</p>
                            </div>
                        `).join('') || '<p class="text-gray-600">No items</p>'}
                    </div>
                    
                    <div class="mt-6 pt-4 border-t space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>£${parseFloat(orderData.subtotal || 0).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delivery:</span>
                            <span>£${parseFloat(orderData.delivery_fee || 0).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span>£${totalAmount}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Attach event listeners after rendering
    attachEventListeners();
}

// Attach event listeners to dynamically created elements
function attachEventListeners() {
    const isArchived = orderData.deleted_at !== null;

    // Status change listeners
    if (!isArchived) {
        const orderStatusSelect = document.getElementById('order-status');
        const paymentStatusSelect = document.getElementById('payment-status');

        if (orderStatusSelect) {
            orderStatusSelect.addEventListener('change', (e) => {
                updateStatus('status', e.target.value);
            });
        }

        if (paymentStatusSelect) {
            paymentStatusSelect.addEventListener('change', (e) => {
                updateStatus('payment_status', e.target.value);
            });
        }

        // Archive button
        const archiveBtn = document.getElementById('archive-btn');
        if (archiveBtn) {
            archiveBtn.addEventListener('click', confirmArchiveOrder);
        }
    } else {
        // Restore button
        const restoreBtn = document.getElementById('restore-btn');
        if (restoreBtn) {
            restoreBtn.addEventListener('click', restoreOrder);
        }
    }
}

// Update order status
async function updateStatus(field, value) {
    const orderId = getOrderId();
    if (!orderId) return;

    try {
        const data = {};
        data[field] = value;
        
        // Use the correct endpoint based on the field being updated
        const endpoint = field === 'status' 
            ? `/api/admin/orders/${orderId}/status`
            : `/api/admin/orders/${orderId}/payment-status`;
        
        await axios.patch(endpoint, data);
        
        window.toast.success('Order updated successfully');
        
        // Reload to show updated data
        loadOrder();
    } catch (error) {
        console.error('Error updating order:', error);
        window.toast.error('Failed to update order');
        loadOrder();
    }
}

// Confirm archive order (requires second click)
function confirmArchiveOrder(event) {
    window.toast.warning('Click Archive again to confirm', 3000);
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Confirm Archive';
    
    // Replace event listener
    btn.removeEventListener('click', confirmArchiveOrder);
    btn.addEventListener('click', archiveOrder, { once: true });
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.removeEventListener('click', archiveOrder);
        btn.addEventListener('click', confirmArchiveOrder);
    }, 3000);
}

// Archive the order
async function archiveOrder() {
    const orderId = getOrderId();
    if (!orderId) return;

    try {
        await axios.delete(`/api/admin/orders/${orderId}`);
        window.toast.success('Order archived successfully');
        setTimeout(() => window.location.href = '/admin/orders', 1500);
    } catch (error) {
        console.error('Error archiving order:', error);
        window.toast.error('Failed to archive order');
    }
}

// Restore the order
async function restoreOrder() {
    const orderId = getOrderId();
    if (!orderId) return;

    try {
        await axios.post(`/api/admin/orders/${orderId}/restore`);
        window.toast.success('Order restored successfully');
        loadOrder();
    } catch (error) {
        console.error('Error restoring order:', error);
        window.toast.error('Failed to restore order');
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    loadOrder();
});
