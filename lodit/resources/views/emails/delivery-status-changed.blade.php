<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Order Update</title>
  </head>
  <body>
    <h2>{{ $status === 'delivered' ? 'Order Delivered' : 'Order Cancelled' }}</h2>
    <p>Order #{{ $order->id }}</p>
    <p>Status: <strong>{{ ucfirst($status) }}</strong></p>
    @if($notes)
      <p>Notes: {{ $notes }}</p>
    @endif
    <p>Thank you for shopping with us.</p>
  </body>
</html>
