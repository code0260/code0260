@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Cart Item</h2>

        @foreach ($cartItems as $item)
            <form action="{{ route('cart.update.item', $item->rowId) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" value="{{ $item->name }}" disabled>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $item->qty }}" min="1">
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="text" class="form-control" id="price" name="price" value="{{ $item->price }}">
                </div>

                <div class="form-group">
                    <label for="specifications">Specifications</label>
                    <div id="specifications">
                        @foreach ($item->specifications as $spec)
                            <div class="specification-group">
                                <label for="spec_name_{{ $spec->id }}">Specification Name</label>
                                <input type="text" class="form-control" id="spec_name_{{ $spec->id }}" name="specifications[{{ $spec->id }}][name]" value="{{ $spec->name }}">

                                <label for="spec_title_{{ $spec->id }}">Title</label>
                                <input type="text" class="form-control" id="spec_title_{{ $spec->id }}" name="specifications[{{ $spec->id }}][title]" value="{{ $spec->title }}">

                                <label for="spec_paragraphs_{{ $spec->id }}">Paragraphs</label>
                                <textarea class="form-control" id="spec_paragraphs_{{ $spec->id }}" name="specifications[{{ $spec->id }}][paragraphs]">{{ json_encode($spec->paragraphs) }}</textarea>

                                <label for="spec_images_{{ $spec->id }}">Images</label>
                                <textarea class="form-control" id="spec_images_{{ $spec->id }}" name="specifications[{{ $spec->id }}][images]">{{ json_encode($spec->images) }}</textarea>

                                <label for="spec_description_{{ $spec->id }}">Description</label>
                                <textarea class="form-control" id="spec_description_{{ $spec->id }}" name="specifications[{{ $spec->id }}][description]">{{ $spec->description }}</textarea>
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Item</button>
            </form>
        @endforeach
    </div>
@endsection
