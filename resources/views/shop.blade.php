@extends('layouts.app')
@section('content')
<style>
  table {
     width: 100%; /* عرض الجدول بالكامل */
     border-collapse: collapse;
     margin-bottom: 20px;
     border-radius: 8px;
     overflow: hidden;
 }
 
 /* تنسيق الرأس */
 th {
     background-color: #007bff; /* اللون الأزرق */
     color: white;
     padding: 12px;
     font-weight: bold;
     text-align: left;
 }
 
 /* تنسيق الأعمدة */
 td {
     padding: 12px;
     vertical-align: middle;
     border-bottom: 1px solid #ddd; /* خط يفصل بين الصفوف */
 }
 
 /* محاذاة اسم المنتج في العمود الأول إلى اليسار */
 .product-name {
     text-align: left;
     font-weight: 500;
     color: #333;
 }
 
 /* محاذاة الأزرار في العمود الثاني إلى المنتصف */
 .action-buttons {
     text-align: right;
 }
 
 /* محاذاة نص الأكشن إلى اليمين */
 th {
     text-align: left;
 }
 
 /* تنسيق الأزرار */
 button,
 .btn {
     padding: 10px 20px;
     border-radius: 4px;
     font-size: 14px;
     background-color: #109faf; /* اللون الأزرق */
     color: rgb(0, 0, 0);
     border: none;
     transition: background-color 0.3s ease, transform 0.3s ease;
 }
 
 /* تأثير عند التمرير فوق الأزرار */
 button:hover,
 .btn:hover {
     background-color: #109faf;
     transform: scale(1.05);
 }
 
 /* تنسيق الأزرار عند التمكين */
 button:disabled,
 .btn:disabled {
     background-color: #ccc;
     color: #666;
     cursor: not-allowed;
 }
 /* تنسيق زر "Go To Order" */
 .btn-warning {
     background-color: #109faf; /* اللون الفيروزي */
     border-color: #109faf; /* اللون الفيروزي */
 }
 
 .btn-warning:hover {
     background-color: #109faf; /* لون فيروزي أفتح قليلاً عند التمرير */
     border-color: #109faf; /* نفس اللون في حالة التمرير */
 }
 
 
 /* تحسين ظهور الصفوف عند التمرير عليها */
 tr:hover {
     background-color: #f1f1f1;
 }
 
 /* تنسيق جدول عند عدم وجود منتجات */
 .table-empty {
     text-align: center;
     font-size: 18px;
     color: #888;
     padding: 50px 0;
 }
 
 /* التأكد من تنسيق عرض الجدول على الشاشة بأكملها */
 .shop-list {
     width: 100%; /* تأكد من أن الحاوية تأخذ العرض الكامل */
     overflow-x: auto; /* التأكد من إمكانية التمرير الأفقي إذا كانت الأعمدة كبيرة */
 }
 </style>
 <style>
   th {
       background-color: #168f9c !important; /* اللون الفيروزي خلفية */
       color: white !important; /* النص أبيض */
       border: 2px solid #168f9c!important; /* الحدود باللون الفيروزي */
       padding: 12px !important; /* لضبط المسافة داخل العنصر */
       text-align: left !important; /* محاذاة العمود الأول لليسار */
   }
 
   th:last-child {
       text-align: right !important; /* محاذاة العمود الأخير (Actions) لليمين */
   }
 
   th:hover {
       background-color:  #109faf !important; /* تغيير الخلفية عندما يمر الفأرة فوق العنصر */
       border-color: #109faf  !important; /* تغيير اللون عند المرور */
   }
 </style>
 
<main class="pt-90">
    <section class="shop-main container d-flex pt-4 pt-xl-5"> 
      <div class="shop-sidebar side-sticky bg-body" id="shopFilter">
        <div class="aside-header d-flex d-lg-none align-items-center">
          <h3 class="text-uppercase fs-6 mb-0">Filter By</h3>
          <button class="btn-close-lg js-close-aside btn-close-aside ms-auto"></button>
        </div>

        <div class="pt-4 pt-lg-0"></div>

        <div class="accordion" id="categories-list">
          <div class="accordion-item mb-4 pb-3">
            <h5 class="accordion-header" id="accordion-heading-1">
              <button class="accordion-button p-0 border-0 fs-5 text-uppercase" type="button" data-bs-toggle="collapse"
                data-bs-target="#accordion-filter-1" aria-expanded="true" aria-controls="accordion-filter-1">
                 <svg class="accordion-button__icon type2" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                  <g aria-hidden="true" stroke="none" fill-rule="evenodd">
                    <path
                      d="M5.35668 0.159286C5.16235 -0.053094 4.83769 -0.0530941 4.64287 0.159286L0.147611 5.05963C-0.0492049 5.27473 -0.049205 5.62357 0.147611 5.83813C0.344427 6.05323 0.664108 6.05323 0.860924 5.83813L5 1.32706L9.13858 5.83867C9.33589 6.05378 9.65507 6.05378 9.85239 5.83867C10.0492 5.62357 10.0492 5.27473 9.85239 5.06018L5.35668 0.159286Z" />
                  </g>
                </svg>
              </button>
            </h5>
            <div id="accordion-filter-1" class="accordion-collapse collapse show border-0"
              aria-labelledby="accordion-heading-1" data-bs-parent="#categories-list">
               
            </div>
          </div>
        </div>


           
          </div>
        </div>
      </div>
      <div class="shop-list flex-grow-1">
       
        <div class="d-flex justify-content-between mb-4 pb-md-2">

          <div class="shop-acs d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">
            <select class="shop-acs__select form-select w-auto border-0 py-0 order-1 order-md-0" aria-label="Sort Items"
              name="orderby" id="orderby">
              <option value="-1" {{$order == -1 ? 'selected':''}}>Default</option>
              <option value="1" {{$order == 1 ? 'selected':''}}>Data, New To Old</option>
              <option value="2" {{$order == 2 ? 'selected':''}}>Data, Old To New</option>
          
            </select>
              
            <div class="shop-filter d-flex align-items-center order-0 order-md-3 d-lg-none">
              <button class="btn-link btn-link_f d-flex align-items-center ps-0 js-open-aside" data-aside="shopFilter">
                <svg class="d-inline-block align-middle me-2" width="14" height="10" viewBox="0 0 14 10" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_filter" />
                </svg>
                <span class="text-uppercase fw-medium d-inline-block align-middle">Filter</span>
              </button>
            </div>
          </div>
        </div>

        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Product</th>
 
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($products as $product)
            <tr>
                <!-- العمود الأول: اسم المنتج -->
                <td class="product-name">
                    <a href="{{route('shop.product.details',['product_slug'=>$product->slug])}}">
                        {{$product->name}}
                    </a>
                </td>
                <!-- العمود الثاني: الإجراءات -->
                <td class="action-buttons">
                    @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                        <a href="{{route('cart.index')}}" class="btn btn-warning">Go To Order</a>
                    @else
                    <form name="addtocart-form" method="post" action="{{route('cart.add')}}">
                        @csrf
                        <input type="hidden" name="id" value="{{$product->id}}" />
                        <input type="hidden" name="quantity" value="1" />
                        <input type="hidden" name="name" value="{{$product->name}}" />
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        
        
        
        </table>

        <div class="divider"></div>

        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
          {{$products->withQueryString()->links('pagination::bootstrap-5')}}
        </div>

      </div>
    </section>
</main>

<form id="frmfilter" method="GET" action="{{route('shop.index')}}">
  <input type="hidden" name="page" value="{{$products->currentPage()}}">
  <input type="hidden" name="size" id="size" value="{{$size}}" />    
  <input type="hidden" name="order" id="order" value="{{$order}}" /> 
</form>

@endsection

@push('scripts')   
<script>    
$(function(){    
    // عند تغيير عدد العناصر في الصفحة
    $("#pagesize").on("change", function(){   
        $("#size").val($("#pagesize option:selected").val());   
        $("#frmfilter").submit();   
    });

    // عند تغيير ترتيب العناصر
    $("#orderby").on("change", function(){   
        $("#order").val($("#orderby option:selected").val());   
        $("#frmfilter").submit();  
    });
 </script>   
@endpush
