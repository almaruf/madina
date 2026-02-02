@extends('admin.layout')

@section('title', 'Users')

@section('content')
<h2 class="text-3xl font-bold mb-6">Users</h2>

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

<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 border-b">
        <div class="flex gap-4">
            <input type="text" id="search" placeholder="Search by phone or name..." class="flex-1 px-4 py-2 border rounded-lg">
            <select id="role-filter" class="px-4 py-2 border rounded-lg">
                <option value="">All Roles</option>
                <option value="customer">Customers</option>
                <option value="admin">Admins</option>
            </select>
        </div>
    </div>
    
    <table class="min-w-full divide-y divide-gray-200" id="users-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Users will be loaded here -->
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
    @vite('resources/js/admin/users.js')
@endsection
