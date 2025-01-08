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
                <input type="text" name="name" id="name" class="form-control modern-input" value="{{ $item->name }}" disabled>
            </div>

            <!-- Quantity field - قابل للتعديل -->
            <div class="form-group">
                <label for="qty">Quantity</label>
                <input type="number" name="qty" id="qty" class="form-control modern-input" value="{{ $item->qty }}" min="1">
            </div>

            <!-- Price field - قابل للتعديل -->
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" class="form-control modern-input" value="{{ $item->price }}" min="0.01" step="0.01">
            </div>

            <!-- Stock Status field - غير قابل للتعديل -->
            <div class="form-group">
                <label for="stock_status">Stock Status</label>
                <select name="stock_status" id="stock_status" class="form-control modern-input" disabled>
                    <option value="active" {{ isset($item->options['stock_status']) && $item->options['stock_status'] === 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="inactive" {{ isset($item->options['stock_status']) && $item->options['stock_status'] === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" name="description" id="description" class="form-control modern-input" value="{{ $item->options['description'] }}">
            </div>

            <!-- Featured field - غير قابل للتعديل -->
            <div class="form-group">
                <label for="featured">Featured</label>
                <input type="checkbox" name="featured" id="featured" value="1" {{ isset($item->options['featured']) && $item->options['featured'] ? 'checked' : '' }} disabled>
            </div>

            <!-- Specifications - قابل للتعديل فقط إذا رغبت -->
            <div class="form-group">
                <label for="specifications">Specifications</label>
                <div id="specifications-container">
                    @foreach ($item->options['specifications'] as $index => $specification)
                        <div class="specification-item mb-3" data-index="{{ $index }}">
                            <label for="specifications[{{ $index }}][name]">Name</label>
                            <input type="text" name="specifications[{{ $index }}][name]" class="form-control modern-input" value="{{ $specification['name'] }}">

                            <label for="specifications[{{ $index }}][title]">Title</label>
                            <input type="text" name="specifications[{{ $index }}][title]" class="form-control modern-input" value="{{ $specification['title'] }}">

                            <label for="specifications[{{ $index }}][paragraphs]">Paragraphs</label>
                            <textarea name="specifications[{{ $index }}][paragraphs]" class="ckeditor form-control modern-textarea" rows="3">
                                {{ is_array($specification['paragraphs']) ? implode(', ', $specification['paragraphs']) : $specification['paragraphs'] }}
                            </textarea>

                            <label for="specifications[{{ $index }}][images]">Images</label>
<div class="gallery-preview">
    @foreach ($specification['images'] ?? [] as $image)
        <div class="gitems">
            <img src="{{ asset('uploads/specifications/' . $image) }}" alt="Specification Image">
            <button type="button" class="remove-old-image-btn" data-image="{{ $image }}">X</button>
        </div>
    @endforeach
</div>
<input type="file" name="specifications[{{ $index }}][images][]" class="form-control modern-input" multiple>

                        </div>
                    @endforeach
                </div>
            </div>

            <!-- زر في النص -->
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary modern-btn">Save Changes</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    /* تحديث الحقول */
    .modern-input,
    .modern-textarea {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 12px 16px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .modern-input:focus,
    .modern-textarea:focus {
        border-color: #109faf;
        box-shadow: 0 0 5px rgba(16, 159, 175, 0.5);
    }

    .modern-textarea {
        resize: vertical;
    }

    /* تحديث الزر */
    .modern-btn {
        background-color: #109faf; /* الفيروزي */
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        display: inline-block;
        margin-top: 20px;
    }

    .modern-btn:hover {
        background-color: #168f9c;
        transform: scale(1.05);
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    /* إضافة تأثيرات عند التركيز */
    .form-group label {
        font-weight: bold;
        color: #333;
    }

    /* توسيط الزر */
    .form-group.text-center {
        text-align: center;
    }

    .gallery-preview {
        margin-bottom: 10px;
    }

    .gitems {
        display: inline-block;
        margin-right: 10px;
        position: relative;
    }

    .gitems img {
        max-width: 150px;
        max-height: 150px;
        border-radius: 5px;
    }

    .remove-old-image-btn {
        position: absolute;
        top: 0;
        right: 0;
        background-color: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
<script>
    $(document).ready(function() {
        // Initialize CKEditor for specification paragraphs
        $(".ckeditor").each(function() {
            ClassicEditor
                .create(this)
                .catch(error => {
                    console.error(error);
                });
        });

        // Remove old images on click
        $(document).on('click', '.remove-old-image-btn', function() {
            var imageDiv = $(this).closest('.gitems');
            imageDiv.remove();
        });
    });
</script>
@endpush
