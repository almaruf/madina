@extends('admin.layout')

@section('title', 'User Details')

@section('content')
<div class="container mx-auto px-4 py-8" data-user-id="{{ request()->route('id') }}">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/admin/users" class="text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
        <p class="mt-2 text-gray-600">Loading user details...</p>
    </div>

    <!-- Error State -->
    <div id="error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <p class="text-red-800"></p>
    </div>

    <!-- User Details -->
    <div id="user-details" class="hidden">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- User Header -->
            <div id="user-header" class="p-6 border-b"></div>

            <!-- User Info -->
            <div id="user-info" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6"></div>

            <!-- Addresses Section -->
            <div id="addresses-section" class="p-6 border-t"></div>

            <!-- Orders Section -->
            <div id="orders-section" class="p-6 border-t"></div>

            <!-- Actions -->
            <div id="user-actions" class="p-6 bg-gray-50 border-t flex gap-4"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/admin/users/show.js')
@endsection

