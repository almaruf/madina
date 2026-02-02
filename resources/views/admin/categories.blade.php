@extends('admin.layout')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Categories</h2>
    <a href="/admin/categories/create" class="nav-link flex items-center gap-2 px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white font-semibold transition">
        <i class="fas fa-plus"></i> Add Category
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
    <table class="min-w-full divide-y divide-gray-200" id="categories-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Categories will be loaded here -->
        </tbody>
    </table>
</div>

@endsection

@section('scripts')
    @vite('resources/js/admin/categories.js')
@endsection

