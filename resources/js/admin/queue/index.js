// Queue Management Page JavaScript
// Axios and toast are available globally via bootstrap.js and layout.js

let currentTab = 'pending';
let jobExceptions = {}; // Store exceptions by job ID

function switchTab(tab) {
    currentTab = tab;
    
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById(`tab-${tab}`).classList.add('active', 'border-blue-500', 'text-blue-600');
    document.getElementById(`tab-${tab}`).classList.remove('border-transparent', 'text-gray-500');
    
    // Update content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    document.getElementById(`${tab}-jobs`).classList.remove('hidden');
}

function loadJobs() {
    axios.get('/api/admin/queue/jobs')
        .then(response => {
            const data = response.data;
            
            // Update counts
            document.getElementById('pendingCount').textContent = data.pending_count;
            document.getElementById('failedCount').textContent = data.failed_count;
            
            // Update pending jobs table
            const pendingTable = document.getElementById('pending-jobs-table');
            const noPending = document.getElementById('no-pending');
            
            if (data.pending_jobs.length === 0) {
                pendingTable.innerHTML = '';
                noPending.classList.remove('hidden');
            } else {
                noPending.classList.add('hidden');
                pendingTable.innerHTML = data.pending_jobs.map(job => `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${job.id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${job.job_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${job.queue}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${job.attempts}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${job.created_at}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${job.reserved_at ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'}">
                                ${job.reserved_at ? 'Processing' : 'Pending'}
                            </span>
                        </td>
                    </tr>
                `).join('');
            }
            
            // Update failed jobs table
            const failedTable = document.getElementById('failed-jobs-table');
            const noFailed = document.getElementById('no-failed');
            
            if (data.failed_jobs.length === 0) {
                failedTable.innerHTML = '';
                noFailed.classList.remove('hidden');
            } else {
                noFailed.classList.add('hidden');
                
                // Store exceptions in the map
                data.failed_jobs.forEach(job => {
                    jobExceptions[job.id] = job.exception;
                });
                
                failedTable.innerHTML = data.failed_jobs.map(job => `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">${job.uuid.substring(0, 8)}...</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${job.job_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${job.queue}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${job.failed_at}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <button onclick="window.retryJob('${job.id}')" class="text-green-600 hover:text-green-900" title="Retry">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                            <button onclick="window.deleteJob('${job.id}')" class="text-red-600 hover:text-red-900" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <button onclick="window.viewException('${job.id}')" class="text-blue-600 hover:text-blue-900" title="View Error">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading jobs:', error);
            window.toast.error('Error loading jobs');
        });
}

function retryJob(id) {
    if (!confirm('Retry this failed job?')) return;
    
    axios.post(`/api/admin/queue/failed/${id}/retry`)
        .then(() => {
            window.toast.success('Job queued for retry');
            loadJobs();
        })
        .catch(error => {
            console.error('Error retrying job:', error);
            window.toast.error('Error retrying job');
        });
}

function retryAllFailed() {
    if (!confirm('Retry all failed jobs?')) return;
    
    axios.post('/api/admin/queue/failed/retry-all')
        .then(() => {
            window.toast.success('All failed jobs queued for retry');
            loadJobs();
        })
        .catch(error => {
            console.error('Error retrying jobs:', error);
            window.toast.error('Error retrying jobs');
        });
}

function deleteJob(id) {
    if (!confirm('Delete this failed job permanently?')) return;
    
    axios.delete(`/api/admin/queue/failed/${id}`)
        .then(() => {
            window.toast.success('Job deleted');
            loadJobs();
        })
        .catch(error => {
            console.error('Error deleting job:', error);
            window.toast.error('Error deleting job');
        });
}

function flushAllFailed() {
    if (!confirm('Delete ALL failed jobs permanently? This cannot be undone!')) return;
    
    axios.delete('/api/admin/queue/failed/flush')
        .then(() => {
            window.toast.success('All failed jobs deleted');
            loadJobs();
        })
        .catch(error => {
            console.error('Error flushing failed jobs:', error);
            window.toast.error('Error flushing failed jobs');
        });
}

function viewException(jobId) {
    const exception = jobExceptions[jobId];
    if (exception) {
        // Create a modal to display the exception nicely
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] flex flex-col">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Job Exception</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-auto flex-1">
                    <pre class="text-xs text-gray-800 whitespace-pre-wrap break-words font-mono bg-gray-50 p-4 rounded">${exception}</pre>
                </div>
                <div class="p-6 border-t">
                    <button onclick="this.closest('.fixed').remove()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                        Close
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    } else {
        window.toast.error('Exception details not available');
    }
}

// Expose functions needed by inline handlers
window.switchTab = switchTab;
window.loadJobs = loadJobs;
window.retryJob = retryJob;
window.retryAllFailed = retryAllFailed;
window.deleteJob = deleteJob;
window.flushAllFailed = flushAllFailed;
window.viewException = viewException;

// Load jobs on page load
document.addEventListener('DOMContentLoaded', () => {
    loadJobs();
    // Auto-refresh every 30 seconds
    setInterval(loadJobs, 30000);
});
