@extends('admin.layout')

@section('title', 'Order Details')

@section('page-title', 'Order Details')

@section('content')
<div class="mb-6">
    <a href="/admin/orders" class="text-green-600 hover:text-green-700 inline-flex items-center gap-2">
        <i class="fas fa-arrow-left"></i>
        Back to Orders
    </a>
</div>

<div id="order-container" class="space-y-6" data-order-id="{{ request()->route('id') }}">
    <div class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
        <p class="text-gray-600 mt-4">Loading order details...</p>
    </div>
</div>
@endsection

@section('scripts')
@vite('resources/js/admin/orders/show.js')
@endsection
