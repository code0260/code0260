@extends('layouts.admin')


@section('content')
    <style>
        .specification-item {
            border: 1px solid #dddddd28;
            padding: 10px;
            margin-bottom: 10px;
        }

        .spec-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }


        #preview-container-$ {
            specificationCounter
        }

        img {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .specification-content {
            display: block;
        }

        .toggle-specification-btn {
            background-color: #007bff;
            /* خلفية الزر زرقاء */
            color: white;
            /* لون النص أبيض */
            border: 2px solid #007bff;
            /* تحديد حد أزرق حول الزر */
            padding: 10px 0;
            /* إضافة حشوة حول النص */
            cursor: pointer;
            /* تغيير شكل المؤشر عند المرور على الزر */
            border-radius: 15px;
            /* زيادة الحواف لتكون مستديرة بشكل أكبر */
            width: 100%;
            /* جعل الزر يغطي المساحة بالكامل */
            text-align: center;
            /* محاذاة النص في الوسط */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            /* تحديد حجم الخط */
            transition: background-color 0.3s, color 0.3s;
            /* تأثير التغيير في اللون */
        }

        .toggle-specification-btn:hover {
            background-color: white;
            /* تغيير خلفية الزر إلى الأبيض عند مرور الماوس */
            color: #007bff;
            /* تغيير النص إلى الأزرق عند مرور الماوس */
            border-color: #007bff;
            /* الحفاظ على اللون الأزرق للحدود عند التمرير */
        }
    </style>
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
            <form class="tf-section-2 form-edit-product" method="POST" enctype="multipart/form-data"
                action="{{ route('admin.product.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $product->id }}" />

                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Product Name <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0"
                            value="{{ old('name', $product->name) }}" aria-required="true" required="">
                        <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                    </fieldset>
                    @error('name')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <fieldset class="category">
                        <div class="body-title mb-10">Product Type <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="category_id">
                                <option>Choose Product Type </option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </fieldset>
                    @error('category_id')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <div class="wg-box">
                        <fieldset class="name">
                            <div class="body-title mb-10">Status</div>
                            <div class="select mb-10">
                                <select name="stock_status">
                                    <option value="active" {{ $product->stock_status == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive" {{ $product->stock_status == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>
                        </fieldset>
                        @error('stock_status')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="cols gap10">
                        <button class="tf-button w-full" type="submit">Update Product</button>
                    </div>
                </div>

                <div class="wg-box">
                    <fieldset class="specifications">
                        <div id="specifications-container">
                            @foreach ($product->specifications as $specification)
                                <div class="specification-item" id="specification-{{ $specification->id }}">
                                    <button type="button" class="toggle-specification-btn tf-button"
                                        data-spec-id="{{ $specification->id }}">
                                        Edit {{ $specification->name }}
                                    </button>
                                    <div class="specification-content" id="specification-content-{{ $specification->id }}"
                                        style="display: none;">
                                        <div class="cols gap10">
                                            <fieldset class="image">
                                                <label for="spec-image-{{ $specification->id }}">Specification
                                                    Images</label>
                                                @php
                                                    $images = json_decode($specification->images, true);
                                                @endphp
                                                <div class="upload-image mb-16">
                                                    <div id="gallery-preview-{{ $specification->id }}"
                                                        class="gallery-preview">
                                                        @if (is_array($images) && count($images) > 0)
                                                            @foreach ($images as $image)
                                                                <div class="gitems">
                                                                    <img src="{{ asset('storage/' . $image) }}"
                                                                        alt="Specification Image">
                                                                    <button type="button" class="remove-old-image-btn"
                                                                        data-image="{{ $image }}">X</button>
                                                                    <input type="hidden"
                                                                        name="specifications[{{ $specification->id }}][existing_images][]"
                                                                        value="{{ $image }}">
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <p>No images available</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <input type="file"
                                                    name="specifications[{{ $specification->id }}][images][]"
                                                    class="form-control gallery-input"
                                                    data-preview-id="gallery-preview-{{ $specification->id }}" multiple>
                                            </fieldset>


                                            <fieldset class="other-info">
                                                <label for="spec-name-{{ $specification->id }}">Specification Name</label>
                                                <input type="text" id="spec-name-{{ $specification->id }}"
                                                    name="specifications[{{ $specification->id }}][name]"
                                                    placeholder="Enter specification name"
                                                    value="{{ old('specifications.' . $specification->id . '.name', $specification->name) }}"
                                                    required>
                                                <label for="spec-title-{{ $specification->id }}">Specification
                                                    Title</label>
                                                <input type="text"
                                                    name="specifications[{{ $specification->id }}][title]"
                                                    placeholder="Enter specification title"
                                                    value="{{ old('specifications.' . $specification->id . '.title', $specification->title) }}">

                                                <label for="spec-paragraphs-{{ $specification->id }}">Specification
                                                    Paragraphs</label>
                                                <textarea name="specifications[{{ $specification->id }}][paragraphs]" id="spec-paragraphs-{{ $specification->id }}"
                                                    class="ckeditor" placeholder="Enter paragraphs">
                                                   {!! htmlspecialchars_decode(stripslashes($specification['paragraphs'])) !!}
                                                </textarea>

                                                <button type="button" class="remove-specification-btn"
                                                    data-spec-id="{{ $specification->id }}">Remove</button>
                                        </div>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>

    <script>
        $(document).ready(function() {
            // زر إظهار/إخفاء السبيسفكيشنات الفردية
            $(document).on('click', '.toggle-specification-btn', function() {
                const specId = $(this).data('spec-id');
                const specContent = $(`#specification-content-${specId}`);
                const button = $(this);

                // جلب اسم السبيسفكيشن من الحقل الموجود داخل الـ HTML
                const specName = $(`#spec-name-${specId}`).val();

                if (specContent.is(':visible')) {
                    specContent.slideUp();
                    button.text(`Show ${specName}`); // عرض اسم السبيسفكيشن في الزر
                } else {
                    specContent.slideDown();
                    button.text(`Hide ${specName}`); // عرض اسم السبيسفكيشن في الزر
                }
            });
            $(document).ready(function() {
                let specificationCounter = 0;

                // Initialize CKEditor for existing specification paragraphs
                document.addEventListener("DOMContentLoaded", function() {
                    document.querySelectorAll('.ckeditor').forEach(editorElement => {
                        ClassicEditor
                            .create(editorElement)
                            .catch(error => console.error(error));
                    });
                });
                // Initialize CKEditor for existing .ckeditor fields on document ready

                $('#add-specification-btn').on('click', function() {
                    specificationCounter++;

                    // New Specification HTML
                    const newSpecification = `
        <div class="specification-item" id="specification-${specificationCounter}">
            <div class="cols gap10">
<fieldset class="image">
    <label for="spec-image-${specificationCounter}">Specification Images</label>
    <div class="upload-image mb-16">
        <div id="gallery-preview-${specificationCounter}" class="gallery-preview">
           @php
               $images = isset($specification['images']) ? json_decode($specification['images'], true) : [];
           @endphp

@if (!empty($images))
    @foreach ($images as $image)
        <div class="gitems">
<img src="{{ asset('storage/products/specifications/' . $image) }}" alt="Specification Image">
            <button type="button" class="remove-old-image-btn btn btn-danger btn-sm" data-image="{{ $image }}">
                Remove
            </button>
        </div>
    @endforeach
@else
    <p>No images available.</p>
@endif

        </div>
    </div>

    <!-- رفع صور جديدة -->
    <input type="file"
           name="specifications[new_${specificationCounter}][images][]"
           class="form-control gallery-input"
           data-preview-id="gallery-preview-${specificationCounter}"
           multiple>
</fieldset>






                <fieldset class="other-info">
                    <label for="spec-name-${specificationCounter}">Specification Name</label>
                    <input type="text" id="spec-name-${specificationCounter}" name="specifications[new_${specificationCounter}][name]" placeholder="Enter specification name" required>

                    <label for="spec-title-${specificationCounter}">Specification Title</label>
                    <input type="text" name="specifications[new_${specificationCounter}][title]" placeholder="Enter specification title">

                    <label for="spec-paragraphs-${specificationCounter}">Specification Paragraphs</label>
                    <textarea name="specifications[new_${specificationCounter}][paragraphs]" class="ckeditor" placeholder="Enter paragraphs"></textarea>
                </fieldset>

                <!-- إضافة الزر مع اسم السبيسفكيشن مباشرة -->
                <button type="button" class="toggle-specification-btn" data-spec-id="${specificationCounter}">Show ${$('#spec-name-' + specificationCounter).val()}</button>

                <button type="button" class="remove-specification-btn" data-spec-id="${specificationCounter}">Remove</button>
            </div>
        </div>`;

                    $('#specifications-container').append(newSpecification);

                    // تحديث النص في الزر ليعرض اسم السبيسفكيشن الجديد
                    const specName = $(`#spec-name-${specificationCounter}`).val();
                    $(`.toggle-specification-btn[data-spec-id="${specificationCounter}"]`).text(
                        `Show ${specName}`);

                    // Initialize CKEditor for the new specification paragraphs
                    initializeCKEditor(
                        `textarea[name='specifications[new_${specificationCounter}][paragraphs]']`
                    );
                });




                // حذف الصور القديمة والجديدة
                // حذف الصور القديمة والجديدة
                $(document).on('click', '.remove-old-image-btn, .remove-new-image-btn', function() {
                    const imageDiv = $(this).closest('.gitems');
                    if (imageDiv.length) {
                        imageDiv.remove();
                    } else {
                        console.error('Image container not found.');
                    }
                });


                // إزالة قسم المواصفات
                $(document).on('click', '.remove-specification-btn', function() {
                    const specId = $(this).data('spec-id');
                    $(`#specification-${specId}`).remove();
                });


                // Toggle Specification visibility
                $(document).on('click', '.toggle-specification-btn', function() {
                    const specId = $(this).data('spec-id');
                    const specItem = $(`#specification-${specId}`);
                    specItem.toggleClass('collapsed');
                    const text = specItem.hasClass('collapsed') ? 'Show' : 'Hide';
                    $(this).text(`${text} ${$('#spec-name-' + specId).val()}`);
                });
            });

        });


        $(document).on('change', '.gallery-input', function(event) {
            const fileInput = event.target;
            const previewContainerId = fileInput.getAttribute('data-preview-id');
            const previewContainer = document.getElementById(previewContainerId);
            const files = fileInput.files;

            if (files && files.length > 0) {
                Array.from(files).forEach(file => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const gItem = document.createElement('div');
                        gItem.className = 'gitems';
                        gItem.innerHTML = `
                    <img src="${e.target.result}" alt="Gallery Image" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                    <button type="button" class="remove-new-image-btn">X</button>
                `;

                        // إزالة الصورة عند النقر على زر الإزالة
                        gItem.querySelector('.remove-new-image-btn').addEventListener('click', () =>
                            gItem.remove());

                        previewContainer.appendChild(gItem);
                    };

                    reader.readAsDataURL(file);
                });
            }
        });
    </script>
@endpush