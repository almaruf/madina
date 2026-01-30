@extends('admin.layout')

@section('title', 'Delivery Slots')

@section('page-title', 'Delivery Slots')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Delivery Slots</h2>
    <button onclick="showCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
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
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
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
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Create Slots
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    async function loadSlots() {
        try {
            const response = await axios.get('/api/admin/delivery-slots');
            const slots = response.data.data || response.data;
            const tbody = document.querySelector('#slots-table tbody');
            
            if (slots.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No delivery slots found</td></tr>';
                return;
            }
            
            tbody.innerHTML = slots.map(slot => `
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${new Date(slot.date).toLocaleDateString()}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${slot.start_time} - ${slot.end_time}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${slot.type === 'delivery' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'}">
                            ${slot.type}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${slot.max_orders}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${slot.current_orders || 0}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${slot.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${slot.is_available ? 'Available' : 'Full'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button onclick="deleteSlot(${slot.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading slots:', error);
            toast.error('Failed to load delivery slots');
        }
    }
    
    function showCreateModal() {
        document.getElementById('create-modal').classList.remove('hidden');
        // Set default dates (today and next 7 days)
        const today = new Date();
        const nextWeek = new Date(today);
        nextWeek.setDate(today.getDate() + 7);
        
        document.querySelector('[name="start_date"]').value = today.toISOString().split('T')[0];
        document.querySelector('[name="end_date"]').value = nextWeek.toISOString().split('T')[0];
    }
    
    function closeModal() {
        document.getElementById('create-modal').classList.add('hidden');
    }
    
    document.getElementById('create-slot-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = {
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date'),
            type: formData.get('type'),
            slots: [{
                start_time: formData.get('start_time'),
                end_time: formData.get('end_time'),
                max_orders: parseInt(formData.get('max_orders'))
            }]
        };
        
        try {
            await axios.post('/api/admin/delivery-slots/generate', data);
            toast.success('Delivery slots created successfully');
            closeModal();
            loadSlots();
            e.target.reset();
        } catch (error) {
            console.error('Error creating slots:', error);
            toast.error(error.response?.data?.message || 'Failed to create delivery slots');
        }
    });
    
    async function deleteSlot(id) {
        if (!confirm('Are you sure you want to delete this slot?')) return;
        
        try {
            await axios.delete(`/api/admin/delivery-slots/${id}`);
            toast.success('Delivery slot deleted successfully');
            loadSlots();
        } catch (error) {
            console.error('Error deleting slot:', error);
            toast.error('Failed to delete delivery slot');
        }
    }
    
    loadSlots();
</script>
@endsection
