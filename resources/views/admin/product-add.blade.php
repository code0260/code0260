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
                    
               


                <div class="gap22 cols">
                    <fieldset class="category">
                        <div class="body-title mb-10">Category <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="category_id">
                                <option>Choose category</option>
                                @foreach ($categories as $category )
                                <option value="{{$category->id}}">{{$category->name}}</option>

                                @endforeach
                            
                            </select>
                        </div>
                    </fieldset>
                    @error('category_id')<span class="alert alert-danger text-center">{{$message}}</span>@enderror

                    <fieldset class="brand">
                        <div class="body-title mb-10">Company <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="brand_id">
                                @foreach ($brands as $brand )
                                <option value="{{$brand->id}}">{{$brand->name}}</option>

                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                    @error('brand_id')<span class="alert alert-danger text-center">{{$message}}</span>@enderror

                </div>

                {{--<fieldset class="shortdescription">
                    <div class="body-title mb-10">Short Description <span class="tf-color-1">*</span></div>
                    <textarea class="mb-10 ht-150" name="short_description" placeholder="Short Description" tabindex="0" aria-required="true">{{old('short_description')}}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('short_description')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
                       --}}    
                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="mb-10" name="description" placeholder="Description" tabindex="0" aria-required="true">{{old('description')}}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('description')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
                
            </div>
            <div class="wg-box">
               {{-- <fieldset class="name">
                    <div class="body-title mb-10">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none">
                            <img src="../../../localhost_8000/images/upload/upload-1.png" class="effect8" alt="">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
                
                <fieldset>
                    <div class="body-title mb-10">Upload Gallery Images</div>
                    <div class="upload-image mb-16">
                        <div id="galUpload" class="item up-load">
                            <label class="uploadfile" for="gFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="text-tiny">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                        <input type="file" id="gFile" name="images[]" accept="image/*" multiple="">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('images')<span class="alert alert-danger text-center">{{$message}}</span>@enderror
                --}}
                <fieldset class="specifications">
                    <div class="body-title mb-10">Specifications</div>
                    
                    <!-- Container for specifications -->
                    <div id="specifications-container">
                        <!-- Dynamic specifications will be added here -->
                    </div>
                    
                    <button type="button" id="add-specification-btn" class="tf-button">Add Specification</button>
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

$(function(){
$("#myFile").on("change", function(e){
const photoInp = $("#myFile");
const [file] = this.files;
if(file)
{
$("#imgpreview img").attr('src', URL.createObjectURL(file));
$("#imgpreview").show();
}
});
$("#gFile").on("change", function(e){
const photoInp = $("#gFile");
const gphotos = this.files;
$.each(gphotos, function (key, val) {
$("#galUpload").prepend(`<div class="item gitems"><img src="${URL.createObjectURL (val)}" /></div>`);
});
});


$(document).on('change', 'select[name="category_id"], input[name="name"]', function () {
    const categoryId = $('select[name="category_id"]').val();
    const name = $('input[name="name"]').val();

    if (categoryId && name) {
        $.ajax({
            url: '/admin/generate-reference-code',
            method: 'POST',
            data: {
                category_id: categoryId,
                name: name,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#reference_code').val(response.reference_code);
            }
        });
    }
});


});
function StringToSlug(Text)
{
return Text.toLowerCase()
.replace(/[^\w ]+/g,"")
.replace(/ +/g,"-");
}

    $(document).ready(function() {
        let specificationCounter = 0; // Counter to uniquely identify each specification
    
        // Event listener for the "Add Specification" button
        $('#add-specification-btn').on('click', function() {
            specificationCounter++;
    
            // Create the new specification fields (description and image upload)
            let newSpecification = `
                <div class="specification-item" id="specification-${specificationCounter}">
                    <div class="specification-description">
                        <label for="spec-description-${specificationCounter}">Specification Description:</label>
                        <textarea name="specifications[${specificationCounter}][description]" id="spec-description-${specificationCounter}" class="mb-10" placeholder="Enter specification description"></textarea>
                    </div>
                    
                    <div class="specification-image">
                        <label for="spec-image-${specificationCounter}">Specification Image:</label>
                        <input type="file" name="specifications[${specificationCounter}][image]" id="spec-image-${specificationCounter}" accept="image/*">
                    </div>
                    
                    <button type="button" class="remove-specification-btn" data-spec-id="${specificationCounter}">Remove</button>
                </div>
            `;
    
            // Append the new specification to the container
            $('#specifications-container').append(newSpecification);
        });
    
        // Event listener for removing a specification
        $(document).on('click', '.remove-specification-btn', function() {
            let specId = $(this).data('spec-id');
            $(`#specification-${specId}`).remove();
        });
    });
    </script>
    
@endpush