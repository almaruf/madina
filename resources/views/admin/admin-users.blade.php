@extends('admin.layout')

@section('title', 'Admin Users')

@section('page-title', 'Admin Users')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Admin Users</h2>
    <button onclick="showCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
        + Add Admin User
    </button>
</div>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200" id="admin-users-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shop</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Admin users will be loaded here -->
        </tbody>
    </table>
</div>

<!-- Create Admin User Modal -->
<div id="create-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Add Admin User</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="create-admin-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Phone Number *</label>
                <input type="tel" name="phone" placeholder="+44..." required class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Role *</label>
                <select name="role" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="admin">Admin</option>
                    <option value="shop_manager">Shop Manager</option>
                    <option value="shop_admin">Shop Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Create Admin User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    @vite('resources/js/admin/admin-users.js')
@endsection
