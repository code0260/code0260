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
    <style>
    .btn-primary:hover {
        background-color: #168f9c; /* فيروزي داكن */
        transform: scale(1.05); /* تكبير الزر قليلاً */
    }
</style>

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

                    <!-- Table showing product names and subtotals -->
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

                    <!-- Order subtotal -->
                    <table class="checkout-totals">
                        <tbody>
                            <tr>
                                <th>SUBTOTAL</th>
                                <td class="text-right">${{ $order->subtotal }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Display Extras -->
                    @if (!empty($order->extra))
                        <div class="order-extras">
                            <ul>
                                <li>
                                    {!! $order->extra !!}
                                </li>
                            </ul>
                        </div>
                    @endif

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
                            @foreach ($orderItems as $item)
                                <tr>
                                    <td>{{ $item->product->name }} x {{ $item->quantity }}</td>
                                    <td>
                                        @if (!empty($item->specifications))
                                            @foreach ($item->specifications as $spec)
                                                <div class="specification">
                                                    <strong>{{ $spec['name'] ?? 'Specification' }}:</strong>
                                                    <p>{{ $spec['title'] ?? 'No title' }}</p>

                                                    <!-- Display paragraphs -->
                                                    @if (!empty($spec['paragraphs']))
                                                        <p>{!! $spec['paragraphs'] !!}</p>
                                                    @endif

                                                    <!-- Display images -->
                                                    @if (!empty($spec['images']))
                                                        <div class="spec-images">
                                                            @foreach ($spec['images'] as $image)
                                                                <img src="{{ asset('storage/' . $image) }}"
                                                                    alt="spec image" width="100" height="100"
                                                                    style="margin-right: 10px;">
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No specifications available.</p>
                                        @endif
                                    </td>
                                    <td class="text-right">${{ $item->price }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <strong>Description:</strong>
                                        <p>{{ $item->description }}</p>
                                    </td>
                                </tr>
                            @endforeach
                            <div class="order-images">
                                <h3>Order Images</h3>
                                <div class="image-gallery">
                                    @if (!empty($order->images))
                                        @foreach (json_decode($order->images) as $image)
                                            <img src="{{ asset('storage/' . $image) }}" alt="Order Image"
                                                width="100" height="100" style="margin-right: 10px;">
                                        @endforeach
                                    @else
                                        <p>No images uploaded for this order.</p>
                                    @endif
                                </div>
                            </div>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Images -->


        <!-- Download PDF Button at the end -->
        <div class="checkout__pdf-button mt-4 text-center">
            <a href="{{ route('order.downloadPdf', ['orderId' => $order->id]) }}" class="btn btn-primary" style="background-color: #109faf; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; text-decoration: none; transition: transform 0.3s ease, background-color 0.3s ease;">
                Download Order PDF
            </a>
        </div>
        
    </section>
</main>
@endsection