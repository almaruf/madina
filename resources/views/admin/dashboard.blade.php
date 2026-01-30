@extends('admin.layout')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Total Orders</p>
                <p class="text-3xl font-bold text-green-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Pending Orders</p>
                <p class="text-3xl font-bold text-orange-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Today's Orders</p>
                <p class="text-3xl font-bold text-blue-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Total Revenue</p>
                <p class="text-3xl font-bold text-purple-600">£0.00</p>
            </div>
        </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4">Recent Orders</h3>
        <p class="text-gray-600">No orders yet</p>
    </div>
@endsection
