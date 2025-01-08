@extends('layouts.app')
@section('content')
<style>
    /* تحديث الأنماط للهوية البصرية */
    .shop-checkout {
        font-family: 'Roboto', sans-serif;
        margin: 40px auto;
        max-width: 1200px;
    }

    .page-title {
        text-align: center;
        font-size: 28px;
        color: #222;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* تحديث الرأس ليكون باللون الفيروزي */
    .cart-table th,
    .cart-table td {
        padding: 15px;
        text-align: center;
        font-size: 16px;
        border: 1px solid #e5e5e5;
    }

    /* تغيير لون الرأس إلى الفيروزي */
    .cart-table th {
        background-color: #109faf; /* الفيروزي */
        color: white;
        font-weight: bold;
        text-transform: uppercase;
    }

    .cart-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .specification {
        margin: 10px 0;
        text-align: left;
    }

    .specification p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }

    .spec-images img {
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 60px;
        height: 60px;
    }

    .qty-control {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .qty-control__number {
        width: 60px;
        text-align: center;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .btn {
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary,
    .btn-info {
        background-color: #109faf; /* الفيروزي */
        color: white;
    }

    /* التأثيرات */
    .btn-primary:hover,
    .btn-info:hover {
        background-color: #168f9c; /* الفيروزي الداكن */
        transform: scale(1.05);
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .btn-danger {
        background-color: #000000;
        color: white;
    }

    .shopping-cart__totals-wrapper {
        margin-top: 20px;
        padding: 20px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .shopping-cart__totals-wrapper h3 {
        font-size: 22px;
        margin-bottom: 15px;
        color: #333;
    }

    .mobile_fixed-btn_wrapper {
        text-align: center;
        margin-top: 30px;
    }

    .mobile_fixed-btn_wrapper a {
        text-decoration: none;
        font-weight: bold;
        padding: 12px 20px;
        background-color: #109faf;
        color: white;
    }

    /* إضافة تأثيرات عند التمرير على الأزرار */
    .btn:hover {
        transform: scale(1.05);
        transition: transform 0.3s ease;
    }

    /* تحسين عرض الجدول */
    .cart-table th:hover {
        background-color: #168f9c; /* التغيير عند التمرير */
        border-color: #168f9c;
    }

    /* توسيط المحتويات داخل الـ <td> */
.checkout-cart-items td {
    text-align: center;
    vertical-align: middle; /* للتوسيط الرأسي */
}

/* إذا أردت توسيط المحتوى داخل .specification */
.specification {
    text-align: center;
    margin: 10px 0;
}

.specification img {
    display: block;
    margin: 0 auto;
}

</style>
    <main class="pt-90">
        <section class="shop-checkout container">
            <h2 class="page-title">Order</h2>

            <div class="shopping-cart">
                @if ($items->count() > 0)
                    <div class="cart-table__wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Specifications</th>
                                     
                                    <th>Total</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <h4>{{ $item->name }}</h4>
                                        </td>
                                    
                                          
                                         
                                        <td>${{ $item->subTotal() }}</td>
                                        <td>{{ $item->options['description'] }}</td>
                                        <td>
                                            <form method="POST"
                                                action="{{ route('cart.item.remove', ['rowId' => $item->rowId]) }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger">Remove</button>
                                            </form>
                                            <a href="{{ route('cart.edit', ['rowId' => $item->rowId]) }}"
                                                class="btn btn-info">Edit</a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <div class="shopping-cart__totals-wrapper">
                                <h3>Product Totals</h3>
                                <p>Total: ${{ Cart::instance('cart')->subtotal() }}</p>
                                <div class="mobile_fixed-btn_wrapper">
                                    <a href="{{ route('cart.checkout') }}" class="btn btn-primary">PROCEED TO CHECKOUT</a>
                                </div>
                            </div>
                        </table>
                    </div>
                @else
                    <p class="text-center">No items in your cart. <a href="{{ route('shop.index') }}"
                            class="btn btn-info">Order Now</a></p>
                @endif
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        $(function() {
            $(".qty-control__increase").on("click", function() {
                $(this).closest('form').submit();
            });
            $(".qty-control__reduce").on("click", function() {
                $(this).closest('form').submit();
            });
            $('.remove-cart').on("click", function() {
                $(this).closest('form').submit();
            });
        })
    </script>
@endpush
