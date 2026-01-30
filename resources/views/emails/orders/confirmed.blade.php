<x-mail::message>
# Order Confirmed - {{ $order->order_number }}

Dear {{ $order->user->name ?? 'Customer' }},

Your order has been confirmed and is being prepared! 🎉

## Order Details

**Order Number:** {{ $order->order_number }}  
**Order Date:** {{ $order->created_at->format('d M Y, h:i A') }}  
**Status:** {{ ucfirst($order->status) }}

## Items

<x-mail::table>
| Product | Quantity | Price |
|:--------|:---------|:------|
@foreach($order->items as $item)
| {{ $item->product_name }} @if($item->variation_name)({{ $item->variation_name }})@endif | {{ $item->quantity }} | {{ $order->shop->currency_symbol ?? '£' }}{{ number_format($item->price * $item->quantity, 2) }} |
@endforeach
</x-mail::table>

## Order Summary

**Subtotal:** {{ $order->shop->currency_symbol ?? '£' }}{{ number_format($order->subtotal, 2) }}  
**Delivery Fee:** {{ $order->shop->currency_symbol ?? '£' }}{{ number_format($order->delivery_fee, 2) }}  
**Total:** {{ $order->shop->currency_symbol ?? '£' }}{{ number_format($order->total, 2) }}

## Delivery Information

**Type:** {{ ucfirst($order->fulfillment_type) }}  
@if($order->deliverySlot)
**Scheduled:** {{ $order->deliverySlot->date->format('d M Y') }} - {{ $order->deliverySlot->start_time }} to {{ $order->deliverySlot->end_time }}
@endif

@if($order->address)
**Address:**  
{{ $order->address->address_line1 }}  
@if($order->address->address_line2){{ $order->address->address_line2 }}@endif  
{{ $order->address->city }}, {{ $order->address->postcode }}
@endif

@if($order->customer_notes)
**Your Notes:** {{ $order->customer_notes }}
@endif

<x-mail::button :url="config('app.url') . '/orders/' . $order->id">
View Order
</x-mail::button>

If you have any questions, please contact us at {{ $shopPhone }} or {{ $shopEmail }}.

Thanks,<br>
{{ $shopName }}
</x-mail::message>
