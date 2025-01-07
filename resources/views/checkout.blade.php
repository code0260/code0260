@extends('layouts.app')

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

    .form-floating label {
        font-size: 14px;
        color: #222;
    }

    /* تنسيق الحقول بشكل عصري */
    .form-floating .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 12px 16px;
        font-size: 16px;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
    }

    .form-floating .form-control:focus {
        border-color: #109faf;
        box-shadow: 0 0 5px rgba(16, 159, 175, 0.5);
        background-color: #ffffff;
    }

    .form-floating label {
        font-weight: bold;
        font-size: 16px;
        color: #444;
    }

    .form-floating .form-control::placeholder {
        color: #888;
    }

    /* تحديث زر الإرسال */
    .btn-primary,
    .btn-info {
        background-color: #109faf; /* الفيروزي */
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .btn-primary:hover,
    .btn-info:hover {
        background-color: #168f9c; /* الفيروزي الداكن عند التمرير */
        transform: scale(1.05);
    }
.btn-primary:hover {
    background-color: #168f9c; /* لون فيروزي داكن عند التمرير */
    transform: scale(1.05); /* تكبير الزر قليلاً */
}

    /* تحديث الأنماط للصندوق الجانبي */
    .checkout__totals-wrapper {
        margin-top: 20px;
        padding: 20px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .order-summary p {
        font-size: 16px;
        color: #333;
    }

    .order-summary strong {
        color: #109faf;
    }

    /* تنسيق حقل التعديلات المتعدد */
    .ckeditor {
        height: 150px;
        resize: vertical;
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px;
        font-size: 14px;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
    }

    .ckeditor:focus {
        border-color: #109faf;
        box-shadow: 0 0 5px rgba(16, 159, 175, 0.5);
    }
    .btn-primary {
    background-color: #109faf; /* اللون الفيروزي */
    color: white; /* لون النص */
}
.form-floating input[type="file"] {
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    border-radius: 3px; /* الحواف المخففة */
}

.form-floating input[type="file"]:focus {
    border-color: #109faf;
    box-shadow: 0 0 5px rgba(16, 159, 175, 0.5);
    border-radius: 3px; /* التناسق عند التفاعل */
}

</style>

@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Enter Customer Information</h2>

        <form name="checkout-form" action="{{ route('cart.place.an.order') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="checkout-form">
                <div class="billing-info__wrapper">
                    <div class="row">
                        <div class="col-6">
                            <h4>Customer DETAILS</h4>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="name" required="">
                                <label for="name">Full Name *</label>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="phone" required="">
                                <label for="phone">Phone Number *</label>
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="zip" required="">
                                <label for="zip">Pincode *</label>
                                @error('zip')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mt-3 mb-3">
                                <input type="text" class="form-control" name="state" required="">
                                <label for="state">State *</label>
                                @error('state')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="city" required="">
                                <label for="city">Town / City *</label>
                                @error('city')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="address" required="">
                                <label for="address">House no, Building Name *</label>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="locality" required="">
                                <label for="locality">Road Name, Area, Colony *</label>
                                @error('locality')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group my-3">
                                <label for="extra">Extras *</label>
                                <textarea class="form-control ckeditor" name="extra" id="extra" rows="5"></textarea>
                                @error('extra')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="checkout__totals-wrapper">
                    <div class="sticky-content">
                        <div class="checkout__totals">
                            <h3>Your Order</h3>
                            <table class="checkout-cart-items">
                                <thead>
                                    <tr>
                                        <th>PRODUCT</th>
                                        <th align="right">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (Cart::instance('cart') as $item)
                                        <tr>
                                            <td>
                                                {{ $item->name }} x {{ $item->qty }}
                                            </td>
                                            <td align="right">
                                                ${{ $item->subtotal() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if (Session::has('discounts'))
                                <table class="checkout-totals">
                                    <tbody>
                                        <tr>
                                            <th>Subtotal</th>
                                            <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                        </tr>
                                        <tr>
                                            <th>Discount {{ Session::get('coupon')['code'] }}</th>
                                            <td class="text-right">${{ Session::get('discounts')['discount'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Subtotal After Discount</th>
                                            <td class="text-right">${{ Session::get('discounts')['subtotal'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <table class="checkout-totals">
                                    <tbody>
                                        <tr>
                                            <th>TOTAL</th>
                                            <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating my-3">
                            <input 
                                type="file" 
                                class="form-control" 
                                name="images[]" 
                                id="images" 
                                multiple 
                                style="padding: 15px; border-radius: 3px; border: 1px solid #e5e5e5;">
                            <label for="images">Upload Images</label>
                            <small class="form-text text-muted">You can upload multiple images (JPG, PNG).</small>
                            @error('images.*')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" style="background-color: #109faf; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; cursor: pointer; transition: transform 0.3s ease, background-color 0.3s ease;">
                            Submit Information
                        </button>
                    </div>
                    
                                    </div>
            </div>
        </form>
    </section>
</main>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#extra'))
                .catch(error => {
                    console.error(error);
                });
        });
    </script>
@endpush
