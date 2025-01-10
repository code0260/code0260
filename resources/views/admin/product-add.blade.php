@extends('layouts.admin')
@section('content')
    <style>
        .specification-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }

        .spec-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .toggle-specification-btn {
            background: #f0f0f0;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
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
    </style>

    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Add Product</h3>
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
                        <div class="text-tiny">Add product</div>
                    </li>
                </ul>
            </div>
            <!-- form-add-product -->
            <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data"
                action="{{ route('admin.product.store') }}">

                @csrf
                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Product name <span class="tf-color-1">*</span>
                        </div>
                        <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0"
                            value="{{ old('name') }}" aria-required="true" required="">
                        <div class="text-tiny">Do not exceed 100 characters when entering the
                            product name.</div>
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
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </fieldset>
                    @error('category_id')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror



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
                        @error('stock_status')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror

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
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>

    <script>
        $(document).ready(function() {
            let specificationCounter = 0;

            // تهيئة محرر النصوص
            const initializeTextEditor = (selector) => {
                $(selector).each(function() {
                    if (!$(this).data('ckeditor-initialized')) {
                        CKEDITOR.replace(this);

                        // بعد تهيئة الـ CKEditor، نزيل التاغات أو الدبل كوتيشن إذا كانت موجودة
                        var content = $(this).val(); // الحصول على المحتوى
                        //content = content.replace(/<[^>]*>/g, ''); // إزالة أي تاغات HTML
                        // content = content.replace(/\"/g, ''); // إزالة الدبل كوتيشن

                        // تعيين المحتوى المعدل داخل الـ CKEditor
                        CKEDITOR.instances[$(this).attr('id')].setData(content);

                        $(this).data('ckeditor-initialized', true);
                    }
                });
            };
            $(document).ready(function() {
                $(".ckeditor").each(function() {
                    // الحصول على المحتوى مع إزالة أي تاغات HTML والفواصل
                    var content = $(this).val(); // أو استخدم .html() إذا كان المحتوى داخل HTML
                    // content = content.replace(/<[^>]*>/g, ''); // إزالة أي تاغات HTML
                    // content = content.replace(/[\r\n\t]/g, ''); // إزالة \r\n و \t (الفواصل)
                    // content = content.replace(/\"/g, ''); // إزالة الدبل كوتيشن (علامات الاقتباس)

                    // تهيئة CKEditor وتعيين المحتوى المعدل فيه
                    ClassicEditor
                        .create(this)
                        .then(editor => {
                            editor.setData(content);

                            // عند حدوث تغيير في البيانات داخل الـ CKEditor
                            editor.model.document.on('change:data', () => {
                                let data = editor.getData();
                                // data = data.replace(/<[^>]*>/g, ''); // إزالة التاغات HTML
                                // data = data.replace(/[\r\n\t]/g, ''); // إزالة الفواصل غير المرغوب فيها
                                // data = data.replace(/\"/g, ''); // إزالة الدبل كوتيشن
                                console.log(data); // طباعة النص المعدل
                            });
                        })
                        .catch(error => {
                            console.error(error);
                        });
                });
            });

            // إضافة قسم مواصفات جديد
            $('#add-specification-btn').on('click', function() {
                specificationCounter++;
                const newSpecification = `
            <div class="specification-item" id="specification-${specificationCounter}">
                <div class="spec-header">
                    <span id="specification-label-${specificationCounter}">Specification ${specificationCounter}</span>
                    <button type="button" class="toggle-specification-btn" data-spec-id="${specificationCounter}">Hide</button>
                </div>
                <div class="specification-content">
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
                        <textarea name="specifications[${specificationCounter}][paragraphs]" id="spec-paragraphs-${specificationCounter}" placeholder="Enter paragraphs"></textarea>
                    </div>
                  <div class="specification-gallery">
    <fieldset>
        <label for="specifications[${specificationCounter}][images]">Images</label>
        <div class="gallery-preview" id="preview-container-${specificationCounter}">
            <!-- سيتم عرض الصور الحالية هنا -->
            @foreach ($specification['images'] ?? [] as $image)
                <div class="gitems">
                    <img src="{{ asset('storage/' . $image) }}" alt="Specification Image">
                    <button type="button" class="remove-old-image-btn" data-image="{{ $image }}">X</button>
                </div>
            @endforeach
        </div>
        <input type="file" name="specifications[${specificationCounter}][images][]" id="gFile-${specificationCounter}" class="form-control modern-input" accept="image/*" multiple>
    </fieldset>
</div>

                    <button type="button" class="remove-specification-btn" data-spec-id="${specificationCounter}">Remove</button>
                </div>
            </div>`;

                $('#specifications-container').append(newSpecification);
                initializeTextEditor(`#spec-paragraphs-${specificationCounter}`);

                // تحديث النص الظاهر بناءً على إدخال اسم المواصفات
                $(`#spec-name-${specificationCounter}`).on('input', function() {
                    const name = $(this).val() || `Specification ${specificationCounter}`;
                    $(`#specification-label-${specificationCounter}`).text(name);
                });
                // تهيئة CKEditor للسبيسفكيشن الجديد
                ClassicEditor.create($(
                    `textarea[name='specifications[new_${specificationCounter}][paragraphs]']`)[0]);
            });

            // إظهار/إخفاء قسم المواصفات
            $(document).on('click', '.toggle-specification-btn', function() {
                const specId = $(this).data('spec-id');
                const specContent = $(`#specification-${specId} .specification-content`);
                const button = $(this);

                if (specContent.is(':visible')) {
                    specContent.slideUp();
                    button.text('Show');
                } else {
                    specContent.slideDown();
                    button.text('Hide');
                }
            });

            // معاينة الصور
            // معاينة الصور
            $(document).on('change', 'input[type="file"]', function(event) {
                if (event.target && event.target.id.startsWith('gFile-')) {
                    const fileInput = event.target;
                    const previewContainerId = fileInput.id.replace('gFile-', 'preview-container-');
                    const previewContainer = document.getElementById(previewContainerId);
                    const files = fileInput.files;

                    previewContainer.innerHTML = ''; // Clear existing previews
                    if (files && files.length > 0) {
                        Array.from(files).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const gItem = document.createElement('div');
                                gItem.className = 'gitems';
                                gItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview Image" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                        <button type="button" class="remove-old-image-btn">X</button>
                    `;
                                gItem.querySelector('.remove-old-image-btn').addEventListener(
                                    'click', () => gItem.remove());
                                previewContainer.appendChild(gItem);
                            };
                            reader.readAsDataURL(file);
                        });
                    } else {
                        previewContainer.innerHTML = '<p>No images selected</p>';
                    }
                }
            });



            // إزالة قسم المواصفات
            $(document).on('click', '.remove-specification-btn', function() {
                const specId = $(this).data('spec-id');
                $(`#specification-${specId}`).remove();
            });

            // تهيئة محرر النصوص للمواصفات
            initializeTextEditor('textarea[name^="specifications"]');
        });
    </script>
@endpush
