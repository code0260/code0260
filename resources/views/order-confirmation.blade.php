@extends('layouts.app')
@section('content')

<style>
    /* إضافة أو تعديل الـ CSS الخاص بالزر */
    .btn-primary {
        background-color: #109faf; /* اللون الفيروزي */
        color: white;
    }

    .btn-primary:hover {
        background-color: #168f9c; /* الفيروزي الداكن عند التمرير */
        transform: scale(1.05);
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    /* توسيط الزر داخل الحاوية */
    .checkout__pdf-button {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }

    /* إضافة خاصية كسر النص */
    .order-info__item span {
        word-wrap: break-word; /* يسمح للنص بالانكسار عند الحاجة */
        word-break: break-word; /* تكسير الكلمة إذا كانت طويلة جدًا */
    }
</style>

<main class="pt-90">
    <section class="shop-checkout container">
        <div class="order-complete">
            <div class="order-complete__message">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="40" fill="#109faf" />
                    <path d="M52.9743 35.7612C52.9743 35.3426 ..." fill="white" />
                </svg>
                <h3>Your order is completed!</h3>
            </div>

            <div class="order-info">
                <div class="order-info__item">
                    <label>Order Number</label>
                    <span>{{ $order->id }}</span>
                </div>
                <div class="order-info__item">
                    <label>Date</label>
                    <span>{{ $order->created_at }}</span>
                </div>
                <div class="order-info__item">
                    <label>Total</label>
                    <span>${{ $order->subtotal }}</span>
                </div>
            </div>

            <div class="checkout__totals-wrapper">
                <div class="checkout__totals">
                    <h3>Order Details</h3>
                    <table class="checkout-cart-items">
                        <thead>
                            <tr>
                                <th>PRODUCT</th>
                                <th>SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                <tr>
                                    <td>{{ $item->product->name }} x {{ $item->quantity }}</td>
                                    <td class="text-right">${{ $item->price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h3>Product Specifications</h3>
                    <table class="checkout-cart-items">
                        <thead>
                            <tr>
                                <th>PRODUCT</th>
                                <th>SPECIFICATIONS</th>
                                <th>SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                <tr>
                                    <td>{{ $item->product->name }} x {{ $item->quantity }}</td>
                                    <td>
                                        @foreach ($item->product->specifications as $spec)
                                            <div class="specification">
                                                <strong>{{ $spec->name }}:</strong>
                                                <p>{{ $spec->title ?? 'No title' }}</p>
                                                @foreach ($item->specifications as $spec2)
                                                    {!! $spec2->paragraphs !!}
                                                @endforeach
                                                @if (!empty($spec->images))
                                                    <div class="spec-images">
                                                        @foreach (json_decode($spec->images) as $image)
                                                            <img src="{{ asset('uploads/products/specifications/' . $image) }}" width="100" />
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="text-right">${{ $item->price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="order-images">
                        <h3>Order Images</h3>
                        <div class="image-gallery">
                            @if (!empty($order->images))
                                @foreach (json_decode($order->images) as $image)
                                    <img src="{{ asset('storage/' . $image) }}" width="100" />
                                @endforeach
                            @else
                                <p>No images uploaded for this order.</p>
                            @endif
                        </div>
                    </div>

                    <div class="checkout__pdf-button">
                        <a href="{{ route('order.downloadPdf', ['orderId' => $order->id]) }}" class="btn btn-primary">Download Order PDF</a>
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>

@endsection
