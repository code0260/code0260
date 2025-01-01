@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Product</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.products') }}">
                        <div class="text-tiny">Products</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit Product</div>
                </li>
            </ul>
        </div>
        <!-- form-edit-product -->
        <form class="tf-section-2 form-edit-product" method="POST" enctype="multipart/form-data" action="{{ route('admin.product.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $product->id }}" />

            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Product Name <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0" value="{{ old('name', $product->name) }}" aria-required="true" required="">
                    <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                </fieldset>
                @error('name')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror
                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="mb-10" name="description" placeholder="Description" tabindex="0" aria-required="true" required="">{{$product->description}}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('description')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Status</div>
                        <div class="select mb-10">
                            <select name="stock_status">
                                <option value="active" {{ $product->stock_status == "active" ? "selected" : "" }}>Active</option>
                                <option value="inactive" {{ $product->stock_status == "inactive" ? "selected" : "" }}>Inactive</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('stock_status')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror
                </div>
    
                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Update Product</button>
                </div>
            </div>

            <div class="wg-box">
                <fieldset class="specifications">
                    <button type="button" id="add-specification-btn" class="tf-button">Add Specification</button>
                    <div id="specifications-container">
                        @foreach($product->specifications as $specification)
                        <div class="specification-item" id="specification-{{ $specification->id }}">
                            <div class="cols gap10">
                                 
                                <fieldset class="image">
                                    <div class="specification-gallery">
                                        <label for="spec-image-{{ $specification->id }}">Specification Image</label>
                                        @php
                                            $images = json_decode($specification->images, true);
                                        @endphp
                                        @if(is_array($images) && count($images) > 0)
                                            <div class="upload-image mb-16">
                                                @foreach($images as $image)
                                                    <div class="gitems">
                                                        <img src="{{ asset('uploads/products/specifications/' . $image) }}" alt="Gallery Image" />
                                                        <input type="hidden" name="specifications[{{ $specification->id }}][existing_images][]" value="{{ $image }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p>No images available</p>
                                        @endif
                                        <input type="file" name="specifications[{{ $specification->id }}][images][]" class="form-control" multiple>
                                    </div>
                                </fieldset>
                        
                                <fieldset class="other-info">
                                    <label for="spec-name-{{ $specification->id }}">Specification Name</label>
                                    <input type="text" name="specifications[{{ $specification->id }}][name]" placeholder="Enter specification name" value="{{ old('specifications.' . $specification->id . '.name', $specification->name) }}" required>
                        
                                    <label for="spec-title-{{ $specification->id }}">Specification Title</label>
                                    <input type="text" name="specifications[{{ $specification->id }}][title]" placeholder="Enter specification title" value="{{ old('specifications.' . $specification->id . '.title', $specification->title) }}">
                        
                                    <label for="spec-paragraphs-{{ $specification->id }}">Specification Paragraphs</label>
                                    <textarea name="specifications[{{ $specification->id }}][paragraphs][]" placeholder="Enter paragraphs (comma-separated)">
                                        {{ is_array($specification->paragraphs) ? implode(',', $specification->paragraphs) : $specification->paragraphs }}
                                    </textarea>
                                </fieldset>
                        
                                <button type="button" class="remove-specification-btn" data-spec-id="{{ $specification->id }}">Remove</button>
                            </div>
                        </div>
                        
                        @endforeach
                    </div>
                </fieldset>
            </div>

           
        </form>
        <!-- /form-edit-product -->
    </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function () {
    let specificationCounter = 0;

    // عند الضغط على زر "Add Specification"
    $('#add-specification-btn').on('click', function () {
        specificationCounter++;

        // إنشاء HTML للمواصفة الجديدة
        const newSpecification = `
            <div class="specification-item" id="specification-${specificationCounter}">
                <div class="cols gap10">
                    
                    <fieldset class="image">
                        <label for="spec-image-${specificationCounter}">Specification Image</label>
                        <input type="file" name="specifications[new_${specificationCounter}][images][]" class="form-control" multiple>
                    </fieldset>
                    <div class="gallery-preview">
                        <div id="gallery-${specificationCounter}" class="gallery-container"></div>
                    </div>
                    <button type="button" class="remove-specification-btn" data-spec-id="${specificationCounter}">Remove</button>
                </div>
            </div>`;

        // إضافة المواصفة الجديدة
        $('#specifications-container').append(newSpecification);
    });

    // إزالة المواصفة عند الضغط على زر الحذف
    $(document).on('click', '.remove-specification-btn', function () {
        const specId = $(this).data('spec-id');
        $(`#specification-${specId}`).remove();
    });

    // عند تغيير صورة المواصفة
    $(document).on("change", "input[type='file'][name^='specifications']", function () {
        const specId = $(this).closest('.specification-item').attr('id').replace('specification-', '');
        const galleryContainer = $(`#gallery-${specId}`);
        const files = this.files;

        // تأكد من أن هناك صور مرفوعة
        if (files.length > 0) {
            $.each(files, function (_, file) {
                const imgPreview = `<div class="item gitems">
                                    <img src="${URL.createObjectURL(file)}" alt="Gallery Image" />
                                </div>`;
                galleryContainer.prepend(imgPreview);
            });
        }
    });
});
</script>
@endpush
