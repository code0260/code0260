@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Cart Item</h1>

    <form action="{{ route('cart.update', ['rowId' => $item->rowId]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Name field - غير قابل للتعديل -->
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $item->name }}" disabled>
        </div>

        <!-- Quantity field - قابل للتعديل -->
        <div class="form-group">
            <label for="qty">Quantity</label>
            <input type="number" name="qty" id="qty" class="form-control" value="{{ $item->qty }}" min="1">
        </div>

        <!-- Price field - قابل للتعديل -->
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control" value="{{ $item->price }}" min="0.01" step="0.01">
        </div>

        <!-- Stock Status field - غير قابل للتعديل -->
        <div class="form-group">
            <label for="stock_status">Stock Status</label>
            <select name="stock_status" id="stock_status" class="form-control" disabled>
                <option value="active" {{ $item->options['stock_status'] === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $item->options['stock_status'] === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Featured field - غير قابل للتعديل -->
        <div class="form-group">
            <label for="featured">Featured</label>
            <input type="checkbox" name="featured" id="featured" value="1" {{ $item->options['featured'] ? 'checked' : '' }} disabled>
        </div>

        <!-- Specifications - قابل للتعديل فقط إذا رغبت -->
        <div class="form-group">
            <label for="specifications">Specifications</label>
            <div id="specifications-container">
                @foreach ($item->options['specifications'] as $index => $specification)
                    <div class="specification-item mb-3" data-index="{{ $index }}">
                        <label for="specifications[{{ $index }}][name]">Name</label>
                        <input type="text" name="specifications[{{ $index }}][name]" class="form-control" value="{{ $specification['name'] }}">

                        <label for="specifications[{{ $index }}][title]">Title</label>
                        <input type="text" name="specifications[{{ $index }}][title]" class="form-control" value="{{ $specification['title'] }}">

                        <label for="specifications[{{ $index }}][paragraphs]">Paragraphs</label>
                        <textarea name="specifications[{{ $index }}][paragraphs][]" class="form-control" rows="3">{{ implode(", ", $specification['paragraphs']) }}</textarea>

                        <label for="specifications[{{ $index }}][images]">Images</label>
                        <!-- تحقق من وجود المفتاح "images" في المصفوفة -->
                        <input type="file" name="specifications[{{ $index }}][images][]" class="form-control" multiple
                            @if(isset($specification['images'])) value="{{ implode(", ", $specification['images']) }}" @endif>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
