@extends('admin.layout')

@section('title', 'Shops')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Shops</h2>
    <a href="{{ route('admin.shops.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
        + Add Shop
    </a>
</div>

<!-- Tabs -->
<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8">
        <button id="active-tab" class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
            Active
        </button>
        <button id="archived-tab" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
            Archived
        </button>
    </nav>
</div>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200" id="shops-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Shops will be loaded here -->
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @vite('resources/js/admin/shops.js')
@endsection
