@extends('admin.layout')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Total Orders</p>
        <p id="total-orders" class="text-3xl font-bold text-green-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Pending Orders</p>
        <p id="pending-orders" class="text-3xl font-bold text-orange-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Today's Orders</p>
        <p id="today-orders" class="text-3xl font-bold text-blue-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Total Revenue</p>
        <p id="total-revenue" class="text-3xl font-bold text-purple-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Total Products</p>
        <p id="total-products" class="text-2xl font-bold text-indigo-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-600 text-sm">Total Customers</p>
        <p id="total-customers" class="text-2xl font-bold text-pink-600">
            <span class="animate-pulse">...</span>
        </p>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-xl font-bold mb-4">Recent Orders</h3>
    <div id="recent-orders">
        <p class="text-gray-600 animate-pulse">Loading orders...</p>
    </div>
</div>
@endsection

@section('scripts')
    @vite('resources/js/admin/dashboard.js')
@endsection
