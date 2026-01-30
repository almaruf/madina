@extends('admin.layout')

@section('title', 'Queue Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Queue Management</h1>
        <p class="text-gray-600 mt-2">Monitor and manage email jobs and background tasks</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-600 text-sm font-semibold uppercase">Pending Jobs</p>
                    <p class="text-3xl font-bold text-blue-700 mt-2" id="pendingCount">0</p>
                </div>
                <div class="text-blue-500">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-600 text-sm font-semibold uppercase">Failed Jobs</p>
                    <p class="text-3xl font-bold text-red-700 mt-2" id="failedCount">0</p>
                </div>
                <div class="text-red-500">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="switchTab('pending')" id="tab-pending" class="tab-button active py-4 px-6 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                    Pending Jobs
                </button>
                <button onclick="switchTab('failed')" id="tab-failed" class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Failed Jobs
                </button>
            </nav>
        </div>

        <!-- Pending Jobs Table -->
        <div id="pending-jobs" class="tab-content p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Pending Jobs</h2>
                <button onclick="loadJobs()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Queue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attempts</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody id="pending-jobs-table" class="bg-white divide-y divide-gray-200">
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>
                <div id="no-pending" class="text-center py-12 text-gray-500 hidden">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-lg font-medium">No pending jobs</p>
                    <p class="text-sm mt-2">All jobs have been processed successfully</p>
                </div>
            </div>
        </div>

        <!-- Failed Jobs Table -->
        <div id="failed-jobs" class="tab-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Failed Jobs</h2>
                <div class="space-x-2">
                    <button onclick="confirmRetryAll()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Retry All
                    </button>
                    <button onclick="confirmFlushAll()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear All
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UUID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Queue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Failed At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="failed-jobs-table" class="bg-white divide-y divide-gray-200">
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>
                <div id="no-failed" class="text-center py-12 text-gray-500 hidden">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-lg font-medium">No failed jobs</p>
                    <p class="text-sm mt-2">All jobs are running smoothly</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tab-button.active {
    @apply border-blue-500 text-blue-600;
}
</style>

<script>
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
    axios.get('/api/admin/queue/jobs', {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
    })
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
                            <button onclick="retryJob('${job.id}')" class="text-green-600 hover:text-green-900" title="Retry">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                            <button onclick="deleteJob('${job.id}')" class="text-red-600 hover:text-red-900" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <button onclick="viewException('${job.id}')" class="text-blue-600 hover:text-blue-900" title="View Error">
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
            toast.error('Error loading jobs');
        });
}

function retryJob(id) {
    axios.post(`/api/admin/queue/failed/${id}/retry`, {}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
    })
        .then(response => {
            toast.success('Job queued for retry');
            loadJobs();
        })
        .catch(error => {
            console.error('Error retrying job:', error);
            toast.error('Error retrying job');
        });
}

function confirmRetryAll() {
    toast.warning('Click Retry All again to confirm', 3000);
    const btn = event.target;
    btn.textContent = 'Confirm Retry All';
    btn.onclick = retryAllFailed;
    setTimeout(() => {
        btn.textContent = 'Retry All';
        btn.onclick = confirmRetryAll;
    }, 3000);
}

function retryAllFailed() {
    axios.post('/api/admin/queue/failed/retry-all', {}, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
    })
        .then(response => {
            toast.success('All failed jobs queued for retry');
            loadJobs();
        })
        .catch(error => {
            console.error('Error retrying jobs:', error);
            toast.error('Error retrying jobs');
        });
}

function deleteJob(id) {
    axios.delete(`/api/admin/queue/failed/${id}`, {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
    })
        .then(response => {
            toast.success('Job deleted');
            loadJobs();
        })
        .catch(error => {
            console.error('Error deleting job:', error);
            toast.error('Error deleting job');
        });
}

function confirmFlushAll() {
    toast.warning('Click Flush All again to PERMANENTLY delete all failed jobs', 3000);
    const btn = event.target;
    btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Confirm Flush All';
    btn.onclick = flushAllFailed;
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-trash mr-2"></i>Flush All';
        btn.onclick = confirmFlushAll;
    }, 3000);
}

function flushAllFailed() {
    axios.delete('/api/admin/queue/failed/flush', {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
    })
        .then(response => {
            toast.success('All failed jobs deleted');
            loadJobs();
        })
        .catch(error => {
            console.error('Error flushing failed jobs:', error);
            toast.error('Error flushing failed jobs');
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
        toast.info('Exception details not available');
    }
}

// Load jobs on page load
document.addEventListener('DOMContentLoaded', function() {
    loadJobs();
    // Auto-refresh every 30 seconds
    setInterval(loadJobs, 30000);
});
</script>
@endsection
