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
                    <button onclick="retryAllFailed()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Retry All
                    </button>
                    <button onclick="flushAllFailed()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
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

@endsection

@section('scripts')
    @vite('resources/js/admin/queue/index.js')
@endsection
