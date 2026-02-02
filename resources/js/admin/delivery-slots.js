// Admin Delivery Slots JS

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
                    <button onclick="window.confirmDeleteSlot(${slot.id})" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading slots:', error);
        if (typeof toast !== 'undefined') {
            toast.error('Failed to load delivery slots');
        }
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

function confirmDeleteSlot(id) {
    toast.warning('Click delete again to confirm', 3000);
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
    btn.onclick = () => window.deleteSlot(id);
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.onclick = () => window.confirmDeleteSlot(id);
    }, 3000);
}

async function deleteSlot(id) {
    try {
        await axios.delete(`/api/admin/delivery-slots/${id}`);
        toast.success('Delivery slot deleted successfully');
        loadSlots();
    } catch (error) {
        console.error('Error deleting slot:', error);
        toast.error('Failed to delete delivery slot');
    }
}

// Expose functions to window
window.showCreateModal = showCreateModal;
window.closeModal = closeModal;
window.confirmDeleteSlot = confirmDeleteSlot;
window.deleteSlot = deleteSlot;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Add slot button
    document.getElementById('add-slot-btn')?.addEventListener('click', showCreateModal);
    
    // Close modal buttons
    document.getElementById('close-modal-btn')?.addEventListener('click', closeModal);
    document.getElementById('cancel-btn')?.addEventListener('click', closeModal);
    
    // Form submission
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
    
    loadSlots();
});
