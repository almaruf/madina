@extends('shop.layout')

@section('title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <a href="/cart" class="text-green-600 hover:text-green-700 mb-4 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to Cart
        </a>
        <h1 class="text-3xl font-bold mt-4">Checkout</h1>
    </div>

    <div id="error-message" class="hidden bg-red-50 border border-red-200 text-red-700 p-4 rounded mb-6 flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i>
        <span></span>
    </div>
    <div id="success-message" class="hidden bg-green-50 border border-green-200 text-green-700 p-4 rounded mb-6 flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span></span>
    </div>

    <form id="checkout-form" class="space-y-8">
        <!-- Delivery Address -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Delivery Address</h2>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">First Name *</label>
                        <input type="text" name="first_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Last Name *</label>
                        <input type="text" name="last_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Phone Number *</label>
                    <input type="tel" name="phone" placeholder="+44..." required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Address Line 1 *</label>
                    <input type="text" name="address_line_1" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Address Line 2</label>
                    <input type="text" name="address_line_2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">City *</label>
                        <input type="text" name="city" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Postcode *</label>
                        <input type="text" name="postcode" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Country *</label>
                        <input type="text" name="country" value="United Kingdom" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Slot -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Delivery Slot *</h2>
            
            <div id="delivery-slots" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <p class="text-gray-600">Loading delivery slots...</p>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Payment Method *</h2>
            
            <div class="space-y-3">
                <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50">
                    <input type="radio" name="payment_method" value="cash" checked class="w-4 h-4">
                    <span class="ml-3">Cash on Delivery</span>
                </label>
                <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50">
                    <input type="radio" name="payment_method" value="card" class="w-4 h-4">
                    <span class="ml-3">Card Payment</span>
                </label>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">Order Summary</h2>
            
            <div id="order-items" class="space-y-2 mb-4 pb-4 border-b border-green-200">
                <!-- Items will be rendered here -->
            </div>

            <div class="space-y-2 text-lg">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span id="summary-subtotal">£0.00</span>
                </div>
                <div class="flex justify-between">
                    <span>Delivery Fee:</span>
                    <span id="summary-delivery-fee">£5.00</span>
                </div>
                <div class="flex justify-between font-bold text-xl">
                    <span>Total:</span>
                    <span id="summary-total">£0.00</span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold text-lg flex items-center justify-center gap-2" id="submit-btn">
            <i class="fas fa-check"></i>
            Place Order
        </button>

        <a href="/cart" class="block text-center text-green-600 hover:text-green-700 font-medium">
            <i class="fas fa-arrow-left"></i>
            Back to Cart
        </a>
    </form>
</div>

<script>
    class CheckoutPage {
        constructor() {
            this.cart = this.loadCart();
            this.deliveryFee = 5.00;
            
            if (!this.cart.length) {
                window.location.href = '/cart';
                return;
            }

            this.init();
        }

        loadCart() {
            // Try cart_for_checkout first, then shopping_cart
            let cart = localStorage.getItem('cart_for_checkout');
            if (cart) {
                return JSON.parse(cart);
            }
            
            cart = localStorage.getItem('shopping_cart');
            if (cart) {
                return JSON.parse(cart);
            }
            
            return [];
        }

        async init() {
            await this.loadDeliverySlots();
            await this.renderOrderSummary();
            this.setupFormSubmission();
        }

        async loadDeliverySlots() {
            try {
                const response = await axios.get('/api/delivery-slots');
                this.renderDeliverySlots(response.data);
            } catch (error) {
                console.error('Error loading delivery slots:', error);
                document.getElementById('delivery-slots').innerHTML = '<p class="text-red-600">Failed to load delivery slots</p>';
            }
        }

        renderDeliverySlots(slots) {
            if (!slots || slots.length === 0) {
                document.getElementById('delivery-slots').innerHTML = '<p class="text-gray-600">No delivery slots available. Please contact the shop.</p>';
                return;
            }

            const html = slots.map(slot => `
                <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 transition">
                    <input type="radio" name="delivery_slot_id" value="${slot.id}" required class="mt-1 w-4 h-4">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold">${this.formatDate(slot.date)}</div>
                        <div class="text-sm text-gray-600">${this.formatTime(slot.start_time)} - ${this.formatTime(slot.end_time)}</div>
                        <div class="text-xs text-gray-500 mt-1">${slot.current_orders || 0}/${slot.max_orders} slots booked</div>
                    </div>
                </label>
            `).join('');

            document.getElementById('delivery-slots').innerHTML = html;
            
            // Select first available slot automatically
            const firstSlot = document.querySelector('input[name="delivery_slot_id"]');
            if (firstSlot) {
                firstSlot.checked = true;
            }
        }

        async renderOrderSummary() {
            try {
                const response = await axios.post('/api/cart/validate', { items: this.cart });
                const items = response.data.items;
                const subtotal = response.data.subtotal;

                const itemsHtml = items.map(item => `
                    <div class="flex justify-between">
                        <span>${item.product_name} (${item.variation_name}) x ${item.quantity}</span>
                        <span>£${item.total.toFixed(2)}</span>
                    </div>
                `).join('');

                document.getElementById('order-items').innerHTML = itemsHtml;

                const total = subtotal + this.deliveryFee;
                document.getElementById('summary-subtotal').textContent = '£' + subtotal.toFixed(2);
                document.getElementById('summary-delivery-fee').textContent = '£' + this.deliveryFee.toFixed(2);
                document.getElementById('summary-total').textContent = '£' + total.toFixed(2);

                this.subtotal = subtotal;
            } catch (error) {
                console.error('Error validating cart:', error);
                document.getElementById('error-message').textContent = 'Error loading order summary';
                document.getElementById('error-message').classList.remove('hidden');
            }
        }

        setupFormSubmission() {
            document.getElementById('checkout-form').addEventListener('submit', async (e) => {
                e.preventDefault();

                const deliverySlotId = document.querySelector('input[name="delivery_slot_id"]:checked')?.value;
                if (!deliverySlotId) {
                    this.showError('Please select a delivery slot');
                    return;
                }

                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Placing Order...';

                try {
                    const formData = {
                        items: this.cart,
                        delivery_slot_id: parseInt(deliverySlotId),
                        payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                        address: {
                            first_name: document.querySelector('input[name="first_name"]').value,
                            last_name: document.querySelector('input[name="last_name"]').value,
                            phone: document.querySelector('input[name="phone"]').value,
                            address_line_1: document.querySelector('input[name="address_line_1"]').value,
                            address_line_2: document.querySelector('input[name="address_line_2"]').value,
                            city: document.querySelector('input[name="city"]').value,
                            postcode: document.querySelector('input[name="postcode"]').value,
                            country: document.querySelector('input[name="country"]').value,
                        }
                    };

                    // For now, create a guest checkout order
                    // You would need to implement this endpoint
                    const response = await axios.post('/api/orders/guest', formData);

                    this.showSuccess('Order placed successfully! Order ID: ' + response.data.id);
                    localStorage.removeItem('shopping_cart');
                    localStorage.removeItem('cart_for_checkout');
                    
                    setTimeout(() => {
                        window.location.href = '/order-confirmation/' + response.data.id;
                    }, 2000);
                } catch (error) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Place Order';
                    const message = error.response?.data?.message || error.response?.data?.errors?.[0] || 'Failed to place order';
                    this.showError(message);
                }
            });
        }

        showError(message) {
            const el = document.getElementById('error-message');
            el.querySelector('span').textContent = message;
            el.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        showSuccess(message) {
            const el = document.getElementById('success-message');
            el.querySelector('span').textContent = message;
            el.classList.remove('hidden');
        }

        formatDate(date) {
            return new Date(date).toLocaleDateString('en-GB', { weekday: 'short', month: 'short', day: 'numeric' });
        }

        formatTime(time) {
            return time.substring(0, 5);
        }
    }

    const checkout = new CheckoutPage();
</script>
@endsection
