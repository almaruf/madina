<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ app(\App\Services\ShopConfigService::class)->name() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-store text-green-600 text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Admin Panel</h1>
                    <p class="text-gray-600 mt-2">{{ app(\App\Services\ShopConfigService::class)->name() }}</p>
                </div>

                <!-- Form -->
                <form id="login-form" class="space-y-4">
                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <div class="flex gap-2">
                            <select id="phone-prefix" class="border border-gray-300 rounded-lg px-4 py-2 bg-white" style="width: 80px; focus-ring-2 focus-ring-green-500 focus-border-transparent">
                                <option value="+44">+44</option>
                            </select>
                            <input type="tel" id="phone" placeholder="077........." required inputmode="numeric" pattern="[0-9]*" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" maxlength="11">
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

                <!-- Help Text -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-gray-600 text-sm">
                        Need help?<br>
                        📞 {{ app(\App\Services\ShopConfigService::class)->phone() }}
                    </p>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Demo Credentials</h3>
                <p class="text-sm text-blue-700">
                    <strong>Super Admin:</strong> +4407849261469<br>
                    <strong>OTP:</strong> 123456
                </p>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js', 'resources/js/admin/login.js'])
</body>
</html>
