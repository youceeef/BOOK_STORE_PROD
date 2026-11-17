<x-mail::message>
# Thank You For Your Order!

Your order **#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}** has been received and is being prepared for dispatch.

<x-mail::table>
| Item | Qty | Price |
| :--- | :--: | :---: |
@foreach ($order->items as $item)
| {{ $item->book?->title ?? 'Book' }} | {{ $item->quantity }} | ${{ number_format($item->price * $item->quantity, 2) }} |
@endforeach
</x-mail::table>

**Total Billed:** ${{ number_format($order->total_amount, 2) }}

<x-mail::button :url="route('orders.show', $order->id)">
View Order Online
</x-mail::button>

Warm regards,<br>
The {{ config('app.name', 'BookStore') }} Team
</x-mail::message>
