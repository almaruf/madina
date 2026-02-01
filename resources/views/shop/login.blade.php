<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ app(\App\Services\ShopConfigService::class)->name() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-green-600">{{ app(\App\Services\ShopConfigService::class)->name() }}</h1>
                </div>
                <div class="flex items-center">
                    <a href="/" class="text-gray-700 hover:text-green-600">← Back to Shop</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 text-center mb-8">Login</h1>

                <form id="login-form" class="space-y-4">
                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <div class="flex gap-2">
                            <select id="phone-prefix" class="border border-gray-300 rounded-lg px-4 py-2 bg-white" style="width: 80px;">
                                <option value="+44">+44</option>
                            </select>
                            <input type="tel" id="phone" placeholder="7911123456" required inputmode="numeric" pattern="[0-9]*" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" maxlength="11">
                        </div>
                    </div>

                    <!-- OTP Section (initially hidden) -->
                    <div id="otp-section" class="hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Enter OTP</label>
                            <input type="text" id="otp" maxlength="6" placeholder="000000" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-center text-2xl tracking-widest focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-2">Check your SMS for the verification code</p>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="error-message" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm"></div>

                    <!-- Loading State -->
                    <div id="loading-message" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-3 text-blue-700 text-sm flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="loading-text">Sending OTP...</span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submit-btn" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-semibold transition">
                        Send OTP
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Don't have an account? <span class="text-green-600 font-semibold">Sign up on checkout</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let otpSent = false;

        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const phone = document.getElementById('phone').value;
            const otp = document.getElementById('otp').value;
            const errorDiv = document.getElementById('error-message');
            const loadingDiv = document.getElementById('loading-message');
            const loadingText = document.getElementById('loading-text');
            const submitBtn = document.getElementById('submit-btn');

            // Reset errors
            errorDiv.classList.add('hidden');

            if (!otpSent) {
                // Send OTP phase
                if (!phone) {
                    showError('Please enter your phone number');
                    return;
                }

                loadingDiv.classList.remove('hidden');
                loadingText.textContent = 'Sending OTP...';
                submitBtn.disabled = true;

                try {
                    const prefix = document.getElementById('phone-prefix').value;
                    const fullPhone = prefix + phone;
                    await axios.post('/api/auth/send-otp', { phone: fullPhone });
                    loadingDiv.classList.add('hidden');
                    submitBtn.disabled = false;
                    
                    otpSent = true;
                    document.getElementById('otp-section').classList.remove('hidden');
                    submitBtn.textContent = 'Verify & Login';
                    showSuccess('OTP sent! Check your SMS.');
                } catch (error) {
                    loadingDiv.classList.add('hidden');
                    submitBtn.disabled = false;
                    showError(error.response?.data?.message || 'Failed to send OTP');
                }
            } else {
                // Verify OTP phase
                if (!otp || otp.length !== 6) {
                    showError('Please enter a valid 6-digit OTP');
                    return;
                }

                loadingDiv.classList.remove('hidden');
                loadingText.textContent = 'Verifying OTP...';
                submitBtn.disabled = true;

                try {
                    const prefix = document.getElementById('phone-prefix').value;
                    const fullPhone = prefix + phone;
                    const response = await axios.post('/api/auth/verify-otp', { phone: fullPhone, otp });
                    
                    localStorage.setItem('token', response.data.token);
                    axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.token;

                    loadingDiv.classList.add('hidden');
                    showSuccess('Login successful! Redirecting...');
                    
                    // Get redirect URL from query parameter
                    const urlParams = new URLSearchParams(window.location.search);
                    const redirectUrl = urlParams.get('redirect') || '/products';
                    
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 1500);
                } catch (error) {
                    loadingDiv.classList.add('hidden');
                    submitBtn.disabled = false;
                    showError(error.response?.data?.message || 'Failed to verify OTP');
                }
            }
        });

        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }

        function showSuccess(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            errorDiv.classList.add('bg-green-50', 'border-green-200', 'text-green-700');
            errorDiv.classList.remove('bg-red-50', 'border-red-200', 'text-red-700');
        }

        if (localStorage.getItem('token')) {
            const urlParams = new URLSearchParams(window.location.search);
            const redirectUrl = urlParams.get('redirect') || '/products';
            window.location.href = redirectUrl;
        }
    </script>
</body>
</html>
