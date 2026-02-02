@extends('admin.layout')

@section('title', 'Product Details')

@section('page-title', 'Product Details')

@section('content')
<div class="mb-6">
    <a href="/admin/products" class="text-green-600 hover:text-green-700 inline-flex items-center gap-2">
        <i class="fas fa-arrow-left"></i>
        Back to Products
    </a>
</div>

<div id="product-container" class="space-y-6" data-product-slug="{{ request()->route('slug') }}">
    <div class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
        <p class="text-gray-600 mt-4">Loading product details...</p>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/admin/products/show.js')
@endsection

