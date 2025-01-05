@extends('layouts.app')

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
                            <div class="col-6">
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
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="note" required="">
                                    <label for="note">Notes *</label>
                                    @error('note')
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



                                            {{-- <tr>

                        <th>Total</th>

                        <td class="text-right">${{Session::get('discounts') ['total']}}</td>

                        </tr> --}}

                                        </tbody>
                                    </table>
                                @else
                                    <table class="checkout-totals">
                                        <tbody>
                                            <tr>
                                                <th>TOTAL</th>
                                                <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>


                                            {{-- <tr>
                      <th>TOTAL</th>
                      <td class="text-right">${{Cart::instance('cart')->total()}}</td>
                    </tr> --}}
                                        </tbody>
                                    </table>
                                @endif

                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="images">Upload Images</label>
                                    <input type="file" class="form-control" name="images[]" id="images" multiple>
                                    <small class="form-text text-muted">You can upload multiple images (JPG, PNG).</small>
                                    @error('images.*')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <button class="btn btn-primary btn-checkout">PLACE ORDER</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main>
@endsection
