@extends('layouts.app')

<style>
    /* تحديث الأنماط للهوية البصرية */
    .shop-checkout {
        font-family: 'Roboto', sans-serif;
        margin: 40px auto;
        max-width: 1200px;
    }

    .page-title {
        text-align: center;
        font-size: 28px;
        color: #222;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .form-floating label {
        font-size: 14px;
        color: #222;
    }

    /* تنسيق الحقول بشكل عصري */
    .form-floating .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 12px 16px;
        font-size: 16px;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
    }

    .form-floating .form-control:focus {
        border-color: #20bec6;
        box-shadow: 0 0 5px rgba(32,190,198,255);
        background-color: #ffffff;
    }

    .form-floating label {
        font-weight: bold;
        font-size: 16px;
        color: #444;
    }

    .form-floating .form-control::placeholder {
        color: #888;
    }

    /* تحديث زر الإرسال */
    .btn-primary,
    .btn-info {
        background-color: #20bec6; /* الفيروزي */
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .btn-primary:hover,
    .btn-info:hover {
        background-color: #20bec6; /* الفيروزي الداكن عند التمرير */
        transform: scale(1.05);
    }
.btn-primary:hover {
    background-color: #20bec6; /* لون فيروزي داكن عند التمرير */
    transform: scale(1.05); /* تكبير الزر قليلاً */
}

    /* تحديث الأنماط للصندوق الجانبي */
    .checkout__totals-wrapper {
        margin-top: 20px;
        padding: 20px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .order-summary p {
        font-size: 16px;
        color: #333;
    }

    .order-summary strong {
        color: #20bec6;
    }

    /* تنسيق حقل التعديلات المتعدد */
    .ckeditor {
        height: 150px;
        resize: vertical;
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px;
        font-size: 14px;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
    }

    .ckeditor:focus {
        border-color: #20bec6;
        box-shadow: 0 0 5px rgba(16, 159, 175, 0.5);
    }
    .btn-primary {
    background-color: #20bec6; /* اللون الفيروزي */
    color: white; /* لون النص */
}
.form-floating input[type="file"] {
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    border-radius: 3px; /* الحواف المخففة */
}

.form-floating input[type="file"]:focus {
    border-color: #20bec6;
    box-shadow: 0 0 5px rgba(16, 159, 175, 0.5);
    border-radius: 3px; /* التناسق عند التفاعل */
}

</style>

@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
        <h2 class="page-title">Enter Customer Information</h2>

        <form name="checkout-form" action="{{ route('cart.place.an.order') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="checkout-form">
                <div class="billing-info__wrapper">
                    <div class="row">
                        <div class="col-6">
                            <h4>Customer DETAILS</h4>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="name" required="">
                                <label for="name">Full Name *</label>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6"> 
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="phone" required="">
                                <label for="phone">Phone Number *</label>
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror 
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating my-3">
                                <input type="text" class="form-control" name="country" required="">
                                <label for="country">country *</label>
                                @error('country')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                      
                        <div class="col-md-12">
                            <div class="form-group my-3">
                                <label for="extra">Extras *</label>
                                <textarea class="form-control ckeditor" name="extra" id="extra" rows="5"></textarea>
                                @error('extra')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
 
                <div class="checkout__totals-wrapper">
                    <fieldset>
                        <div class="body-title mb-10">Upload Technical Drawings or Images</div>
                        <div class="upload-image mb-16">
                            <label class="uploadfile" for="images">
                                <span class="icon"><i class="icon-upload-cloud"></i></span>
                                 <input type="file" id="images" name="images[]" accept="image/*" multiple>
                            </label>
                            <div id="preview-container" class="preview-container"></div>
                        </div>
                    </fieldset>
                    
                    <!-- Preview of selected images as a grid -->
                    <div id="image-preview" class="my-3"></div>
                
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" style="background-color: #20bec6; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; cursor: pointer; transition: transform 0.3s ease, background-color 0.3s ease;">
                            Order Confirmation
                        </button>
                    </div>
                </div>
                
        </form>
    </section>
</main>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#extra'))
                .catch(error => {
                    console.error(error);
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
    // تفعيل المعاينة
    function previewImages(event) {
        var preview = document.getElementById('preview-container');
        preview.innerHTML = ''; // إزالة أي صور سابقة
        
        // الحصول على الملفات
        var files = event.target.files;

        // حلقة من أجل عرض الصور
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            
            var reader = new FileReader();
            reader.onload = function(e) {
                // إنشاء عنصر صورة
                var img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Image Preview';
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.objectFit = 'cover';

                // إضافة الصورة إلى المعاينة
                preview.appendChild(img);
            };
            reader.readAsDataURL(file); // قراءة الملف كـ Data URL لعرضه
        }
    }

    // إضافة حدث الـ "change" لرفع الصور
    document.getElementById('images').addEventListener('change', previewImages);
});


document.querySelector("form[name='checkout-form']").addEventListener('submit', function(event) {
    var images = document.getElementById('images').files;
    if (images.length === 0) {
        alert("Please upload at least one image before confirming the order.");
        event.preventDefault(); // منع الإرسال حتى يتم رفع صورة
    }
});


     </script>
    <style>.preview-container {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
    
    .preview-container img {
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
    

    </style>
@endpush
