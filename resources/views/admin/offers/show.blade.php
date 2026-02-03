@extends('admin.layout')

@section('title', 'Offer Details')

@section('content')
<div class="container mx-auto px-4 py-8" data-offer-id="{{ $offerId }}">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/admin/offers" class="text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Offer Details</h1>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
        <p class="mt-2 text-gray-600">Loading offer details...</p>
    </div>

    <!-- Error State -->
    <div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <p class="text-red-800"></p>
    </div>

    <!-- Offer Details -->
    <div id="offer-details" class="hidden space-y-6">
        <!-- Offer Header -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <div id="offer-header"></div>
        </div>

        <!-- Offer Info -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">Offer Information</h2>
            <div id="offer-info" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
        </div>

        <!-- Products Section -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <div id="products-section"></div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 rounded-lg p-6 flex gap-4" id="offer-actions"></div>
    </div>
</div>

<!-- Products Management Modal -->
<div id="products-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full my-8">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-xl font-semibold">Manage Offer Products</h3>
            <button id="close-modal-top" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Available Products -->
                <div>
                    <h4 class="font-semibold text-lg mb-3 flex items-center gap-2">
                        <i class="fas fa-box text-gray-600"></i>
                        Available Products
                    </h4>
                    <div class="border rounded-lg p-3 bg-gray-50 max-h-[500px] overflow-y-auto">
                        <input type="text" id="search-products" placeholder="Search products..." class="w-full mb-3 px-3 py-2 border rounded-lg">
                        <div id="available-products" class="space-y-2">
                            <div class="text-center py-4">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto"></div>
                                <p class="mt-2 text-gray-600 text-sm">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Assigned Products -->
                <div>
                    <h4 class="font-semibold text-lg mb-3 flex items-center gap-2">
                        <i class="fas fa-tags text-green-600"></i>
                        Products in This Offer
                    </h4>
                    <div class="border rounded-lg p-3 bg-green-50 max-h-[500px] overflow-y-auto">
                        <div id="offer-products" class="space-y-2">
                            <div class="text-center py-4">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto"></div>
                                <p class="mt-2 text-gray-600 text-sm">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t flex justify-end">
            <button id="close-modal-bottom" class="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700">
                Close
            </button>
        </div>
    </div>
</div>

@vite('resources/js/admin/offers/show.js')
@endsection
