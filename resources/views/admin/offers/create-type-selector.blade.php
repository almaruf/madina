@extends('admin.layout')

@section('title', 'Create Offer')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Offer</h1>
        <p class="text-gray-600 mt-2">Select the type of promotion you want to create</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Percentage Discount -->
        <a href="/admin/offers/create/percentage-discount" class="bg-white rounded-lg shadow-lg hover:shadow-xl transition p-8 border-2 border-transparent hover:border-green-500 cursor-pointer">
            <div class="text-4xl mb-4">
                <i class="fas fa-percent text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-900">Percentage Discount</h3>
            <p class="text-gray-600 text-sm">Give customers a percentage off (e.g., 20% OFF)</p>
            <div class="mt-4 text-green-600 font-semibold flex items-center gap-2">
                Get Started <i class="fas fa-arrow-right"></i>
            </div>
        </a>

        <!-- Fixed Discount (Coming Soon) -->
        <div class="bg-white rounded-lg shadow-lg p-8 border-2 border-gray-200 opacity-50">
            <div class="text-4xl mb-4">
                <i class="fas fa-pound-sign text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-900">Fixed Discount</h3>
            <p class="text-gray-600 text-sm">Give customers a fixed amount off (e.g., £5 OFF)</p>
            <div class="mt-4 text-gray-400 font-semibold inline-block px-3 py-1 bg-gray-100 rounded">
                Coming Soon
            </div>
        </div>

        <!-- Buy X Get Y -->
        <a href="/admin/offers/create/bxgy" class="bg-white rounded-lg shadow-lg hover:shadow-xl transition p-8 border-2 border-transparent hover:border-green-500 cursor-pointer">
            <div class="text-4xl mb-4">
                <i class="fas fa-gift text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-900">Buy X Get Y</h3>
            <p class="text-gray-600 text-sm">Free items or discounts with bulk purchases</p>
            <div class="mt-4 text-green-600 font-semibold flex items-center gap-2">
                Get Started <i class="fas fa-arrow-right"></i>
            </div>
        </a>

        <!-- Multi-Buy (Coming Soon) -->
        <div class="bg-white rounded-lg shadow-lg p-8 border-2 border-gray-200 opacity-50">
            <div class="text-4xl mb-4">
                <i class="fas fa-boxes text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-900">Multi-Buy Deal</h3>
            <p class="text-gray-600 text-sm">Bundle products at a special price</p>
            <div class="mt-4 text-gray-400 font-semibold inline-block px-3 py-1 bg-gray-100 rounded">
                Coming Soon
            </div>
        </div>
    </div>

    <div class="mt-8">
        <a href="/admin/offers" class="text-gray-600 hover:text-gray-900 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Back to Offers
        </a>
    </div>
</div>
@endsection
