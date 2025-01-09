@extends('layouts.app')
@section('content')
    <div class="container pt-5">
        <h2>Edit Order #{{ $order->id }}</h2>
        <form action="{{ route('user.order.update', ['order_id' => $order->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $order->name }}">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ $order->phone }}">
            </div>

            <div class="mb-3">
                <label for="subtotal" class="form-label">Total</label>
                <input type="number" class="form-control" id="subtotal" name="subtotal" value="{{ $order->subtotal }}">
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="ordered" {{ $order->status == 'ordered' ? 'selected' : '' }}>Ordered</option>
                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="note" class="form-label">Note</label>
                <textarea class="form-control" id="note" name="note">{{ $order->note }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">Update Order</button>
        </form>
    </div>
@endsection