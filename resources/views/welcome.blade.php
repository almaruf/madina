<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app(\App\Services\ShopConfigService::class)->name() }} - Online Grocery</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-green-600">{{ app(\App\Services\ShopConfigService::class)->name() }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-gray-700 hover:text-green-600">Admin</a>
                    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Login
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                Welcome to {{ app(\App\Services\ShopConfigService::class)->name() }}
            </h2>
            <p class="text-xl text-gray-600 mb-8">
                {{ app(\App\Services\ShopConfigService::class)->description() }}
            </p>
            <div class="flex justify-center space-x-4">
                <button class="bg-green-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-green-700">
                    Start Shopping
                </button>
                <button class="bg-white text-green-600 px-8 py-3 rounded-lg text-lg font-semibold border-2 border-green-600 hover:bg-green-50">
                    Learn More
                </button>
            </div>
        </div>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-green-600 text-4xl mb-4">🛒</div>
                <h3 class="text-xl font-bold mb-2">Wide Selection</h3>
                <p class="text-gray-600">Browse our extensive range of fresh groceries and everyday essentials.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-green-600 text-4xl mb-4">🚚</div>
                <h3 class="text-xl font-bold mb-2">Fast Delivery</h3>
                <p class="text-gray-600">Choose your preferred delivery or collection time slot that suits your schedule.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-green-600 text-4xl mb-4">✅</div>
                <h3 class="text-xl font-bold mb-2">Quality Products</h3>
                <p class="text-gray-600">All our products are sourced from trusted suppliers and meet high quality standards.</p>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white mt-16 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; 2026 {{ app(\App\Services\ShopConfigService::class)->name() }}. All rights reserved.</p>
                <p class="mt-2">{{ app(\App\Services\ShopConfigService::class)->fullAddress() }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
