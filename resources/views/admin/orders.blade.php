@extends('admin.layout')

@section('title', 'Orders')

@section('content')
<h2 class="text-3xl font-bold mb-6">Orders</h2>

<!-- Tabs and Status Filter -->
<div class="mb-4 flex justify-between items-center">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button id="tab-active" class="tab-button border-b-2 border-green-600 py-2 px-1 text-sm font-medium text-green-600">
                Active
            </button>
            <button id="tab-archived" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Archived
            </button>
        </nav>
    </div>
    
    <div>
        <select id="status-filter" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="processing">Processing</option>
            <option value="ready">Ready</option>
            <option value="out_for_delivery">Out for Delivery</option>
            <option value="delivered">Delivered</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="orders-table">
            <!-- Orders will be loaded here -->
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @vite('resources/js/admin/orders.js')
@endsection
