@extends('admin.layout')

@section('title', 'Delivery Slots')

@section('page-title', 'Delivery Slots')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Delivery Slots</h2>
    <button id="add-slot-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
        + Add Slot
    </button>
</div>

<div class="bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200" id="slots-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Slots will be loaded here -->
        </tbody>
    </table>
</div>

<!-- Create Slot Modal -->
<div id="create-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Create Delivery Slots</h3>
            <button id="close-modal-btn" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="create-slot-form" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Start Date *</label>
                    <input type="date" name="start_date" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">End Date *</label>
                    <input type="date" name="end_date" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Start Time *</label>
                    <input type="time" name="start_time" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">End Time *</label>
                    <input type="time" name="end_time" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Type *</label>
                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="delivery">Delivery</option>
                        <option value="collection">Collection</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Max Orders *</label>
                    <input type="number" name="max_orders" min="1" value="10" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" id="cancel-btn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Create Slots
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    @vite('resources/js/admin/delivery-slots.js')
@endsection
