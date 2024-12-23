@extends('layouts.admin')
@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add Order</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('admin.orders')}}">
                        <div class="text-tiny">Orders</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Add Order</div>
                </li>
            </ul>
        </div>

        <form class="tf-section-2 form-add-order" method="POST" action="{{ route('admin.order.store') }}">
            @csrf
            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Client's Name <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter client's name" name="name" value="{{ old('name') }}" required>
                </fieldset>
                @error('name')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
        
                <fieldset class="name">
                    <div class="body-title mb-10">Email <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="email" placeholder="Enter email" name="email" value="{{ old('email') }}" required>
                </fieldset>
                @error('email')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
        
                <fieldset class="name">
                    <div class="body-title mb-10">Phone <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter phone number" name="phone" value="{{ old('phone') }}" required>
                </fieldset>
                @error('phone')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
        
                <fieldset class="name">
                    <div class="body-title mb-10">Country <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter country" name="country" value="{{ old('country') }}" required>
                </fieldset>
                @error('country')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
            </div>
        
            <div class="wg-box">
                <fieldset>
                    <div class="body-title">Product Details</div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="product_id" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="quantity" value="1" min="1" required></td>
                                <td><input type="number" name="unit_price" step="0.01" value="" required></td>
                                <td><input type="number" name="total_price" step="0.01" value="" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        
            <div class="cols gap10">
                <button class="tf-button w-full" type="submit">Add Order</button>
            </div>
        </form>
        
    </div>
</div>

@endsection
