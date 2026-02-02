@extends('admin.layout')

@section('title', 'Offers')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Offers & Promotions</h1>
        <a href="/admin/offers/create" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Create Offer
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button id="filter-active" class="filter-tab active border-b-2 border-green-600 py-4 px-1 text-sm font-medium text-green-600">
                Active
            </button>
            <button id="filter-inactive" class="filter-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Inactive
            </button>
            <button id="filter-expired" class="filter-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Expired
            </button>
        </nav>
    </div>

    <!-- Offers List -->
    <div id="offers-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Loading offers...</p>
        </div>
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
@endsection

@section('scripts')
    @vite('resources/js/admin/offers.js')
@endsection
