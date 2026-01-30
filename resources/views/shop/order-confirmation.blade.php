@extends('shop.layout')

@section('title', 'Order Confirmation')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <!-- Loading State -->
    <div id="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
        <p class="mt-4 text-gray-600">Loading order details...</p>
    </div>

    <!-- Error State -->
    <div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-6 text-center">
        <i class="fas fa-exclamation-circle text-4xl text-red-600 mb-4"></i>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Order Not Found</h2>
        <p class="text-gray-700 mb-4">We couldn't find this order. Please check your order history.</p>
        <a href="/" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
            Continue Shopping
        </a>
    </div>

    <!-- Order Confirmation -->
    <div id="order-confirmation" class="hidden">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="inline-block bg-green-100 rounded-full p-4 mb-4">
                <i class="fas fa-check-circle text-5xl text-green-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
            <p class="text-gray-600">Thank you for your order. We'll send you a confirmation shortly.</p>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-green-600 text-white px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold">Order #<span id="order-number"></span></h2>
                        <p class="text-green-100 text-sm" id="order-date"></p>
                    </div>
                    <span id="order-status" class="px-4 py-2 bg-white text-green-600 rounded-lg font-semibold text-sm"></span>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="p-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4">Delivery Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Delivery Address</h4>
                        <div id="delivery-address" class="text-gray-900"></div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Delivery Time</h4>
                        <div id="delivery-time" class="text-gray-900"></div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="p-6 border-b">
                <h3 class="font-bold text-gray-900 mb-4">Order Items</h3>
                <div id="order-items" class="space-y-4"></div>
            </div>

            <!-- Order Summary -->
            <div class="p-6 bg-gray-50">
                <h3 class="font-bold text-gray-900 mb-4">Order Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-gray-700">
                        <span>Subtotal</span>
                        <span id="subtotal"></span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>Delivery Fee</span>
                        <span id="delivery-fee"></span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
                        <span>Total</span>
                        <span id="total"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 text-center font-semibold">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<script>
    const orderId = {{ $orderId }};

    async function loadOrder() {
        try {
            // Set authorization token
            const token = localStorage.getItem('token');
            if (token) {
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
            }

            const response = await axios.get(`/api/orders/${orderId}`);
            const order = response.data;
            
            renderOrder(order);
        } catch (error) {
            console.error('Error loading order:', error);
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error').classList.remove('hidden');
        }
    }

    function renderOrder(order) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('order-confirmation').classList.remove('hidden');

        // Order header
        document.getElementById('order-number').textContent = order.id;
        document.getElementById('order-date').textContent = new Date(order.created_at).toLocaleString();
        document.getElementById('order-status').textContent = order.status.replace(/_/g, ' ').toUpperCase();

        // Delivery address
        if (order.address) {
            document.getElementById('delivery-address').innerHTML = `
                <p class="font-semibold">${order.user?.name || 'Customer'}</p>
                <p>${order.address.address_line_1}</p>
                ${order.address.address_line_2 ? `<p>${order.address.address_line_2}</p>` : ''}
                <p>${order.address.city}, ${order.address.postcode}</p>
                <p>${order.address.country || 'United Kingdom'}</p>
                <p class="mt-2"><i class="fas fa-phone"></i> ${order.address.phone}</p>
            `;
        }

        // Delivery time
        if (order.delivery_slot) {
            document.getElementById('delivery-time').innerHTML = `
                <p class="font-semibold">${new Date(order.delivery_slot.date).toLocaleDateString()}</p>
                <p>${order.delivery_slot.start_time} - ${order.delivery_slot.end_time}</p>
            `;
        }

        // Order items
        const itemsHtml = order.items.map(item => `
            <div class="flex gap-4">
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">${item.product_name}</h4>
                    <p class="text-sm text-gray-600">${item.variation_name}</p>
                    <p class="text-sm text-gray-600">Quantity: ${item.quantity}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-900">£${parseFloat(item.unit_price * item.quantity).toFixed(2)}</p>
                    <p class="text-sm text-gray-600">£${parseFloat(item.unit_price).toFixed(2)} each</p>
                </div>
            </div>
        `).join('');
        document.getElementById('order-items').innerHTML = itemsHtml;

        // Order summary
        document.getElementById('subtotal').textContent = '£' + parseFloat(order.subtotal).toFixed(2);
        document.getElementById('delivery-fee').textContent = order.delivery_fee > 0 ? '£' + parseFloat(order.delivery_fee).toFixed(2) : 'FREE';
        document.getElementById('total').textContent = '£' + parseFloat(order.total).toFixed(2);
    }

    loadOrder();
</script>
@endsection
