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
                <input type="text" name="name" id="name" class="form-control modern-input"
                    value="{{ $item->name }}" disabled>
            </div>

            <!-- Quantity field - قابل للتعديل -->
            <div class="form-group">
                <label for="qty">Quantity</label>
                <input type="number" name="qty" id="qty" class="form-control modern-input"
                    value="{{ $item->qty }}" min="1">
            </div>

            <!-- Price field - قابل للتعديل -->
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" class="form-control modern-input"
                    value="{{ $item->price }}" min="0.01" step="0.01">
            </div>
 
            <!-- Stock Status field - غير قابل للتعديل -->
            <div class="form-group">
                <label for="stock_status">Status</label>
                <select name="stock_status" id="stock_status" class="form-control modern-input" disabled>
                    <option value="active"
                        {{ isset($item->options['stock_status']) && $item->options['stock_status'] === 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="inactive"
                        {{ isset($item->options['stock_status']) && $item->options['stock_status'] === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">PRODUCT DESCRIPTION</label>
                <input type="text" name="description" id="description" class="form-control modern-input"
                    value="{{ $item->options['description'] }}">
            </div>
            <label for="specifications"><strong>Specifications</strong></label>

            <!-- Specifications - قابل للتعديل فقط إذا رغبت -->
            <div class="form-group">
                <div id="specifications-container">
                    @foreach ($item->options['specifications'] as $index => $specification)
                        <div class="specification-item mb-3" data-index="{{ $index }}">
                            <button type="button" class="btn btn-info toggle-specification"
                                data-target="#specification-{{ $index }}" data-spec-id="{{ $index }}">
                                Show {{ $specification['name'] }} <!-- عرض اسم السبيسفكيشن هنا -->
                            </button>
                            <div class="specification-details" id="specification-{{ $index }}"
                                style="display: none;">
                                <!-- Add Specification Fields Here -->
                                <label for="specifications[{{ $index }}][name]">Name</label>
                                <input type="text" name="specifications[{{ $index }}][name]"
                                    id="spec-name-{{ $index }}" class="form-control modern-input"
                                    value="{{ $specification['name'] }}">

                                <label for="specifications[{{ $index }}][title]">Title</label>
                                <input type="text" name="specifications[{{ $index }}][title]"
                                    class="form-control modern-input" value="{{ $specification['title'] }}">

                                <label for="specifications[{{ $index }}][paragraphs]">Paragraphs</label>
                                <textarea name="specifications[{{ $index }}][paragraphs]" class="ckeditor form-control modern-textarea"
                                    rows="3">
                                {{ is_array($specification['paragraphs']) ? implode(', ', $specification['paragraphs']) : $specification['paragraphs'] }}
                            </textarea>

                                <label for="specifications[{{ $index }}][images]">Images</label>
                                <div class="gallery-preview">
                                    @foreach ($specification['images'] ?? [] as $image)
                                        <div class="gitems">
                                            <img src="{{ asset('storage/' . $image) }}" alt="Specification Image">
                                            <button type="button" class="remove-old-image-btn"
                                                data-image="{{ $image }}">X</button>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="file" name="specifications[{{ $index }}][images][]"
                                    class="form-control modern-input" multiple>
                            </div>
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
        /* تحديث الأزرار */


        /* زر إزالة الصورة */
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

        /* توسيط باستخدام Flexbox */


        /* تحسين النصوص */
        .form-group label {
            font-weight: bold;
            color: #333;
        }

        /* معرض الصور */
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

        /* تفاصيل المواصفات */
        .specification-details {
            margin-top: 15px;
        }

        .toggle-specification {
            margin-bottom: 10px;
        }

        .modern-btn {
            background-color: #000000;
            /* اللون الأسود */
            color: white;
            /* النص باللون الأبيض */
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            display: inline-block;
            margin-top: 15px;
            text-align: center;
            transition: transform 0.3s ease, background-color 0.3s ease;
            width: 200px;
            box-sizing: border-box;
        }

        /* عند تحريك الفأرة فوق الزر */
        .modern-btn:hover {
            background-color: #333333;
            /* لون أغمق عند التحويم */
            transform: scale(1.05);
        }

        /* تحديث الحقول */
        .modern-input,
        .modern-textarea {
            border-radius: 8px;
            border: 1px solid #120202;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        /* إزالة تأثير hover على زر Show */
        /* إزالة تأثير hover على زر Show */
        .toggle-specification {
            background-color: #20bec6;
            /* اللون الأساسي */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
            margin-top: 10px;
            width: 100%;
            /* ضمان أن يكون الأزرار بعرض 100% */
            text-align: center;
            transition: none;
            /* إيقاف تأثيرات التغيير عند التحويم */
        }

        /* عند تحريك الفأرة فوق الزر، لا تغير اللون أو الحجم */
        .toggle-specification:hover {
            background-color: #20bec6;
            /* نفس اللون كما هو في الحالة العادية */
            transform: none;
            /* إيقاف التأثير عند تحريك الفأرة */
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            // تهيئة CKEditor لجميع الحقول التي تحتوي على .ckeditor
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


            // إزالة الصور القديمة عند النقر عليها
            $(document).on('click', '.remove-old-image-btn', function() {
                var imageDiv = $(this).closest('.gitems');
                imageDiv.remove();
            });

            // زر إظهار/إخفاء السبيسفكيشنات
            $(document).on('click', '.toggle-specification', function() {
                const specId = $(this).data('spec-id');
                const specContent = $(`#specification-${specId}`);
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

            // عند إضافة سبيسفكيشن جديدة
            let specificationCounter = 0;

            $('#add-specification-btn').on('click', function() {
                specificationCounter++;

                // إضافة سبيسفكيشن جديدة
                const newSpecification = `
        <div class="specification-item" id="specification-${specificationCounter}">
            <div class="cols gap10">
                <fieldset class="image">
                    <label for="spec-image-${specificationCounter}">Specification Images</label>
                    <div class="upload-image mb-16">
                        <div id="gallery-preview-${specificationCounter}" class="gallery-preview"></div>
                    </div>
                    <input type="file" name="specifications[new_${specificationCounter}][images][]" class="form-control gallery-input" data-preview-id="gallery-preview-${specificationCounter}" multiple>
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
                <button type="button" class="toggle-specification" data-spec-id="${specificationCounter}">Show ${$('#spec-name-' + specificationCounter).val()}</button>

                <button type="button" class="remove-specification-btn" data-spec-id="${specificationCounter}">Remove</button>
            </div>
        </div>`;

                $('#specifications-container').append(newSpecification);

                // تحديث النص في الزر ليعرض اسم السبيسفكيشن الجديد
                const specName = $(`#spec-name-${specificationCounter}`).val();
                $(`.toggle-specification[data-spec-id="${specificationCounter}"]`).text(`Show ${specName}`);

                // تهيئة CKEditor للسبيسفكيشن الجديد
                ClassicEditor.create($(
                    `textarea[name='specifications[new_${specificationCounter}][paragraphs]']`)[0]);
            });
        });
    </script>
@endpush
