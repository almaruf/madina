@extends('admin.layout')

@section('title', 'Customers')

@section('page-title', 'Customers')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button id="tab-active" class="tab-button border-b-2 border-green-600 py-2 px-1 text-sm font-medium text-green-600">
                Active Customers
            </button>
            <button id="tab-removal" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Removal Requests
                <span id="removal-count" class="ml-2 bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs hidden">0</span>
            </button>
        </nav>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" id="action-header">Action</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="customers-table">
            <!-- Customers will be loaded here -->
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @vite('resources/js/admin/customers.js')
@endsection
