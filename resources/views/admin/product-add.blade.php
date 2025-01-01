@extends('layouts.admin')
@section('content')
  <div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add Product</h3>
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
                    <a href="{{route('admin.products')}}">
                        <div class="text-tiny">Products</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Add product</div>
                </li>
            </ul>
        </div>
        <!-- form-add-product -->
        <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{route('admin.product.store')}}">
            
                @csrf
            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span>
                    </div>
                    <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0" value="{{old('name')}}" aria-required="true" required="">
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('name')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
                    
                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="mb-10" name="description" placeholder="Description" tabindex="0" aria-required="true" required="">{{old('description')}}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('description')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
               

 
               
            </div>
            <div class="wg-box">
               
                <fieldset class="specifications">
                   <!-- زر إضافة مواصفات -->
<button type="button" id="add-specification-btn" class="tf-button">Add Specification</button>

<!-- مكان إضافة الحقول الجديدة -->
<div id="specifications-container">
    <!-- الحقول المضافة ديناميكياً ستظهر هنا -->
</div>

                </fieldset>
                
                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Status</div>
                        <div class="select mb-10">
                            <select class="" name="stock_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('stock_status')<span class="alert alert-danger text-center">{{$message}}</span>@enderror

                </div>
                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Add product</button>
                </div>
            </div>
        </form>
        <!-- /form-add-product -->
    </div>
    <!-- /main-content-wrap -->
</div>
<!-- /main-content-wrap -->
@endsection

@push('scripts')
<script>
// عند تحميل الصفحة
$(document).ready(function () {
    // معاينة صورة واحدة عند التغيير
    $("#myFile").on("change", function () {
        const [file] = this.files;
        if (file) {
            $("#imgpreview img").attr('src', URL.createObjectURL(file));
            $("#imgpreview").show();
        }
    });

    // إضافة صور الغاليري عند التغيير
    $(document).on("change", "input[type='file'][id^='gFile']", function () {
        const galleryId = $(this).attr('id').replace('gFile-', 'galUpload-');
        const galleryContainer = $(`#${galleryId}`);
        const files = this.files;

        if (files.length > 0) {
            $.each(files, function (_, file) {
                const imgPreview = `<div class='item gitems'><img src='${URL.createObjectURL(file)}' alt='Gallery Image' /></div>`;
                galleryContainer.prepend(imgPreview);
            });
        }
    });

    // عداد للمواصفات الجديدة
    let specificationCounter = 0;

    // عند الضغط على زر "Add Specification"
    $('#add-specification-btn').on('click', function () {
        specificationCounter++;

        // إنشاء HTML للمواصفة الجديدة
        const newSpecification = `
            <div class="specification-item" id="specification-${specificationCounter}">
                <div class="specification-name">
                    <label for="spec-name-${specificationCounter}">Specification Name:</label>
                    <input type="text" name="specifications[${specificationCounter}][name]" id="spec-name-${specificationCounter}" placeholder="Enter specification name" required>
                </div>
                <div class="specification-title">
                    <label for="spec-title-${specificationCounter}">Specification Title:</label>
                    <input type="text" name="specifications[${specificationCounter}][title]" id="spec-title-${specificationCounter}" placeholder="Enter specification title">
                </div>
                <div class="specification-paragraphs">
                    <label for="spec-paragraphs-${specificationCounter}">Specification Paragraphs:</label>
                    <textarea name="specifications[${specificationCounter}][paragraphs][]" id="spec-paragraphs-${specificationCounter}" placeholder="Enter paragraphs (comma-separated)"></textarea>
                </div>
                <div class="specification-gallery">
                    <fieldset>
                        <div class="body-title mb-10">Upload Gallery Images</div>
                        <div class="upload-image mb-16">
                            <div id="galUpload-${specificationCounter}" class="item up-load">
                                <label class="uploadfile" for="gFile-${specificationCounter}">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="text-tiny">Drop your images here or select <span class="tf-color">click to browse</span></span>
                                    <input type="file" id="gFile-${specificationCounter}" name="specifications[${specificationCounter}][images][]" accept="image/*" multiple>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <button type="button" class="remove-specification-btn" data-spec-id="${specificationCounter}">Remove</button>
            </div>`;

        // إضافة المواصفة الجديدة
        $('#specifications-container').append(newSpecification);
    });

    // إزالة المواصفة عند الضغط على زر الحذف
    $(document).on('click', '.remove-specification-btn', function () {
        const specId = $(this).data('spec-id');
        $(`#specification-${specId}`).remove();
    });

    // توليد كود المرجع بناءً على اسم المنتج
    $(document).on('change', 'input[name="name"]', function () {
        const name = $(this).val();

        if (name) {
            $.ajax({
                url: '/admin/generate-reference-code',
                method: 'POST',
                data: {
                    name: name,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#reference_code').val(response.reference_code);
                },
                error: function () {
                    console.error('Failed to generate reference code.');
                }
            });
        }
    });
});


    </script>
    
@endpush