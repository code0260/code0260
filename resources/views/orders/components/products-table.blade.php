<table>
    <thead>
        <tr>
            <th>Product Description</th>
            <th>Quantity</th>
            <th>Unit Price (USD)</th>
            <th>Total Amount (USD)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cartItems as $cartItem)
            <tr>
                <td>{{ $cartItem->options->description ?? 'No description available' }}</td>
                <td>{{ $cartItem->qty }}</td>
                <td>{{ $cartItem->price }}</td>
                <td>{{ $cartItem->subtotal }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
