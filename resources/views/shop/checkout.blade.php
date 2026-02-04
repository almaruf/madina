@extends('shop.layout')

@section('title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Auth Check -->
    <div id="auth-check" class="hidden">
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-8 rounded-lg text-center">
            <i class="fas fa-lock text-4xl mb-4"></i>
            <h2 class="text-2xl font-bold mb-2">Please Login to Continue</h2>
            <p class="mb-4">You need to be logged in to complete your purchase.</p>
            <a href="/shop/login?redirect=/checkout" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                Login / Sign Up
            </a>
        </div>
    </div>

    <div id="checkout-content">
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
        <!-- User Addresses -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Delivery Address</h2>
            
            <div id="user-addresses" class="space-y-3">
                <p class="text-gray-600">Loading your addresses...</p>
            </div>

            <div id="new-address-form" class="hidden mt-4 pt-4 border-t">
                <h3 class="font-semibold mb-3">Add New Address</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone Number *</label>
                        <input type="tel" id="new_phone" placeholder="+44..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Address Line 1 *</label>
                        <input type="text" id="new_address_line_1" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Address Line 2</label>
                        <input type="text" id="new_address_line_2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">City *</label>
                            <input type="text" id="new_city" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <div>
                            <label class="block text-sm font-medium mb-1">Postcode *</label>
                            <input type="text" id="new_postcode" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Country *</label>
                            <input type="text" id="new_country" value="United Kingdom" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                    </div>

                    <button type="button" onclick="checkout.saveNewAddress()" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        Save Address
                    </button>
                </div>
            </div>

            <button type="button" onclick="checkout.toggleNewAddressForm()" class="mt-4 text-green-600 hover:text-green-700 font-semibold">
                + Add New Address
            </button>
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
                <div id="vat-line" class="flex justify-between text-sm text-gray-600" style="display: none;">
                    <span id="vat-label">VAT (20%):</span>
                    <span id="vat-amount">£0.00</span>
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
            this.cart = [];
            this.deliveryFee = 5.00;
            this.user = null;
            this.selectedAddressId = null;
            this.shopConfig = null;
            this.checkAuthAndInit();
        }

        async checkAuthAndInit() {
            // Set authorization token from localStorage
            const token = localStorage.getItem('token');
            if (!token) {
                // No token at all - show login prompt
                console.log('No token found, showing login prompt');
                document.getElementById('auth-check').classList.remove('hidden');
                document.getElementById('checkout-content').classList.add('hidden');
                return;
            }
            
            // Set token in axios
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

            // Check authentication
            try {
                const response = await axios.get('/api/auth/user');
                this.user = response.data;
                console.log('User authenticated:', this.user);
            } catch (error) {
                console.error('Authentication failed:', error.response?.status, error.response?.data);
                // Token is invalid or expired - clear it and show login
                localStorage.removeItem('token');
                delete axios.defaults.headers.common['Authorization'];
                this.user = null;
                document.getElementById('auth-check').classList.remove('hidden');
                document.getElementById('checkout-content').classList.add('hidden');
                return;
            }

            // Load cart
            this.cart = this.loadCart();
            
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
            await this.loadShopConfig();
            await this.loadUserAddresses();
            await this.loadDeliverySlots();
            await this.renderOrderSummary();
            this.setupFormSubmission();
        }

        async loadShopConfig() {
            try {
                const response = await axios.get('/api/shop/config');
                this.shopConfig = response.data;
                this.deliveryFee = parseFloat(this.shopConfig.delivery_fee) || 5.00;
                document.getElementById('summary-delivery-fee').textContent = 
                    (this.shopConfig.currency_symbol || '£') + this.deliveryFee.toFixed(2);
            } catch (error) {
                console.error('Failed to load shop config:', error);
                // Use defaults
                this.shopConfig = {
                    currency_symbol: '£',
                    vat: { registered: false, rate: 20.00, prices_include_vat: true }
                };
            }
        }

        async loadUserAddresses() {
            try {
                const response = await axios.get('/api/addresses');
                const addresses = response.data;

                if (addresses && addresses.length > 0) {
                    const html = addresses.map(addr => `
                        <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 transition ${addr.is_default ? 'border-green-500 bg-green-50' : ''}">
                            <input type="radio" name="address_id" value="${addr.id}" ${addr.is_default ? 'checked' : ''} required class="mt-1 w-4 h-4">
                            <div class="ml-3 flex-1">
                                <div class="font-semibold">${addr.address_line_1}</div>
                                ${addr.address_line_2 ? `<div class="text-sm text-gray-600">${addr.address_line_2}</div>` : ''}
                                <div class="text-sm text-gray-600">${addr.city}, ${addr.postcode}</div>
                                ${addr.phone ? `<div class="text-sm text-gray-500">Phone: ${addr.phone}</div>` : ''}
                                ${addr.is_default ? '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded mt-1 inline-block">Default</span>' : ''}
                            </div>
                        </label>
                    `).join('');
                    document.getElementById('user-addresses').innerHTML = html;
                    this.selectedAddressId = addresses.find(a => a.is_default)?.id || addresses[0].id;
                } else {
                    document.getElementById('user-addresses').innerHTML = '<p class="text-gray-600">No saved addresses. Please add a new one below.</p>';
                    document.getElementById('new-address-form').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error loading addresses:', error);
                document.getElementById('user-addresses').innerHTML = '<p class="text-red-600">Failed to load addresses</p>';
            }
        }

        toggleNewAddressForm() {
            const form = document.getElementById('new-address-form');
            form.classList.toggle('hidden');
        }

        async saveNewAddress() {
            const addressData = {
                address_line_1: document.getElementById('new_address_line_1').value,
                address_line_2: document.getElementById('new_address_line_2').value,
                city: document.getElementById('new_city').value,
                postcode: document.getElementById('new_postcode').value,
                phone: document.getElementById('new_phone').value,
            };

            if (!addressData.address_line_1 || !addressData.city || !addressData.postcode) {
                alert('Please fill in all required fields');
                return;
            }

            try {
                await axios.post('/api/addresses', addressData);
                this.showSuccess('Address saved successfully');
                setTimeout(() => {
                    document.getElementById('success-message').classList.add('hidden');
                }, 3000);
                await this.loadUserAddresses();
                this.toggleNewAddressForm();
                // Clear form
                document.getElementById('new_address_line_1').value = '';
                document.getElementById('new_address_line_2').value = '';
                document.getElementById('new_city').value = '';
                document.getElementById('new_postcode').value = '';
                document.getElementById('new_phone').value = '';
            } catch (error) {
                alert('Failed to save address: ' + (error.response?.data?.message || error.message));
            }
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
                const currencySymbol = this.shopConfig?.currency_symbol || '£';

                const itemsHtml = items.map(item => `
                    <div class="flex justify-between">
                        <span>${item.product_name} (${item.variation_name}) x ${item.quantity}</span>
                        <span>${currencySymbol}${item.total.toFixed(2)}</span>
                    </div>
                `).join('');

                document.getElementById('order-items').innerHTML = itemsHtml;

                // Calculate VAT if prices DON'T include VAT (need to add VAT on top)
                let vatAmount = 0;
                if (this.shopConfig?.vat?.prices_include_vat === false) {
                    const vatRate = parseFloat(this.shopConfig.vat.rate) || 20.00;
                    
                    // Prices exclude VAT - add VAT on top
                    vatAmount = subtotal * (vatRate / 100);
                    
                    // Update VAT display
                    document.getElementById('vat-label').textContent = `VAT (${vatRate}%):`;
                    document.getElementById('vat-amount').textContent = currencySymbol + vatAmount.toFixed(2);
                    document.getElementById('vat-line').style.display = 'flex';
                } else {
                    // VAT already included in prices or not applicable
                    document.getElementById('vat-line').style.display = 'none';
                }

                // Calculate total (add VAT if prices don't include it)
                const total = subtotal + vatAmount + this.deliveryFee;
                
                document.getElementById('summary-subtotal').textContent = currencySymbol + subtotal.toFixed(2);
                document.getElementById('summary-delivery-fee').textContent = currencySymbol + this.deliveryFee.toFixed(2);
                document.getElementById('summary-total').textContent = currencySymbol + total.toFixed(2);

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

                const addressId = document.querySelector('input[name="address_id"]:checked')?.value;
                const deliverySlotId = document.querySelector('input[name="delivery_slot_id"]:checked')?.value;
                
                if (!addressId) {
                    this.showError('Please select a delivery address');
                    return;
                }
                
                if (!deliverySlotId) {
                    this.showError('Please select a delivery slot');
                    return;
                }

                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Placing Order...';

                try {
                    const formData = {
                        items: this.cart.map(item => ({
                            product_variation_id: item.variation_id,
                            quantity: item.quantity
                        })),
                        address_id: parseInt(addressId),
                        delivery_slot_id: parseInt(deliverySlotId),
                        payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                        fulfillment_type: 'delivery'
                    };

                    // Use authenticated order endpoint
                    const response = await axios.post('/api/orders', formData);

                    this.showSuccess('Order placed successfully! Order ID: ' + response.data.order.id);
                    localStorage.removeItem('shopping_cart');
                    localStorage.removeItem('cart_for_checkout');
                    
                    setTimeout(() => {
                        window.location.href = '/order-confirmation/' + response.data.order.id;
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
