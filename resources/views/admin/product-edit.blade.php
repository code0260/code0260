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
                    <div class="text-tiny">Edit Product</div>
                </li>
            </ul>
        </div>
        <!-- form-add-product -->
        <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data" action="{{route('admin.product.update')}}">
            
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value={{$product->id}} />

            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span>
                    </div>
                    <input class="mb-10" type="text" placeholder="Enter product name" name="name" tabindex="0" value="{{$product->name}}" aria-required="true" required="">
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
                                <option value="{{$category->id}}" {{$product->category_id == $category->id ? "selected":""}}>{{$category->name}}</option>

                                @endforeach
                            
                            </select>
                        </div>
                    </fieldset>
                    @error('category_id')<span class="alert alert-danger text-center">{{$message}}</span>@enderror

                    <fieldset class="brand">
                        <div class="body-title mb-10">Brand <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="brand_id">
                                @foreach ($brands as $brand )
                                <option value="{{$brand->id}}" {{$product->brand_id == $brand->id ? "selected":""}}>{{$brand->name}}</option>

                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                    @error('brand_id')<span class="alert alert-danger text-center">{{$message}}</span>@enderror

                </div>

             

                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="mb-10" name="description" placeholder="Description" tabindex="0" aria-required="true">{{$product->description}}</textarea>
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
                @error('description')<span class="alert alert-danger text-center">{{$message}}</span>@enderror

            </div>
            <div class="wg-box">
           
               
                
                <div class="cols gap22">
                    <div class="form-group">
                        <label for="specifications">Specifications</label>
                        <div id="specifications">
                            @foreach($specifications as $specification)
                                <div class="specification">
                                    <!-- إضافة حقل id للمواصفة -->
                                    <input type="hidden" name="specifications[{{ $loop->index }}][id]" value="{{ $specification->id }}">
                    
                                    <label for="description">Description</label>
                                    <input type="text" name="specifications[{{ $loop->index }}][description]" value="{{ old('specifications.'.$loop->index.'.description', $specification->description) }}" class="form-control">
                    
                                    <label for="image">Image</label>
                                    @if($specification->image)
                                        <img src="{{ asset('uploads/products/specifications/'.$specification->image) }}" alt="Specification Image" width="100">
                                    @endif
                                    <input type="file" name="specifications[{{ $loop->index }}][image]" class="form-control">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <fieldset class="name">
                        <div class="body-title mb-10">Status</div>
                        <div class="select mb-10">
                            <select class="" name="stock_status">active', 'inactive
                                <option value="active" {{$product->stock_status == "active" ? "selected":""}}>Active</option>
                                <option value="inactive" {{$product->stock_status == "inactive" ? "selected":""}}>Inactive</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('stock_status')<span class="alert alert-danger text-center">{{$message}}</span>@enderror

                
                </div>
                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Update product</button>
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

$("input[name='name']").on("change", function(){
$("input[name='slug']").val(StringToSlug($(this).val()));
});
});
function StringToSlug(Text)
{
return Text.toLowerCase()
.replace(/[^\w ]+/g,"")
.replace(/ +/g,"-");
}
$(function(){
    // عند الضغط على زر "Add Specification"
    $("#add-specification-btn").on("click", function() {
        var specificationHtml = `
            <div class="specification-item">
                <div class="cols gap10">
                    <fieldset class="description">
                        <label for="spec_description">Specification Description</label>
                        <textarea name="specifications[description][]" placeholder="Enter specification description" required></textarea>
                    </fieldset>
                    <fieldset class="image">
                        <label for="spec_image">Specification Image</label>
                        <input type="file" name="specifications[image][]" accept="image/*">
                    </fieldset>
                    <button type="button" class="remove-specification-btn">Remove</button>
                </div>
            </div>
        `;

        // إضافة الحقل الجديد إلى الـ container
        $("#specifications-container").append(specificationHtml);
    });

    // عند الضغط على زر "Remove" لحذف حقل specification
    $(document).on("click", ".remove-specification-btn", function() {
        $(this).closest(".specification-item").remove();
    });

    // تحميل الصورة عند تحديدها
    $("#myFile").on("change", function(e) {
        const photoInp = $("#myFile");
        const [file] = this.files;
        if (file) {
            $("#imgpreview img").attr('src', URL.createObjectURL(file));
            $("#imgpreview").show();
        }
    });

    $("#gFile").on("change", function(e) {
        const gphotos = this.files;
        $.each(gphotos, function (key, val) {
            $("#galUpload").prepend(`<div class="item gitems"><img src="${URL.createObjectURL(val)}" /></div>`);
        });
    });

    $("input[name='name']").on("change", function() {
        $("input[name='slug']").val(StringToSlug($(this).val()));
    });
});

function StringToSlug(Text) {
    return Text.toLowerCase()
        .replace(/[^\w ]+/g, "")
        .replace(/ +/g, "-");
}

</script>

@endpush