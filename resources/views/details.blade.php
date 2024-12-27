@extends('layouts.app')

@section('content')
<style>
  /* تخصيص الألوان والأنماط لتناسب تصميم شركة بناء */
  .product-single__name {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
  }

  .product-single__price .current-price {
    font-size: 1.5rem;
    color: #e67e22;
    font-weight: bold;
  }

  .product-single__short-desc p {
    font-size: 1.1rem;
    color: #666;
  }

  .product-single__details-tab {
    margin-top: 30px;
  }

  .meta-item {
    font-size: 1rem;
    color: #555;
    margin-bottom: 10px;
  }

  /* إعادة الزر إلى اللون الأسود */
  .btn-primary {
    background-color: #000; /* اللون الأسود */
    color: #fff;
    padding: 12px 20px;
    font-size: 1.1rem;
    border-radius: 5px;
    border: none;
    text-transform: uppercase;
    font-weight: bold;
  }

  .btn-primary:hover {
    background-color: #333; /* اللون الأسود الداكن عند التمرير */
  }

  .product-single__image img {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
  }

  .product-single__thumbnail img {
    width: 100%;
    max-height: 80px;
    object-fit: cover;
  }

  .product-single__description {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
    margin-top: 15px;
  }
</style>

<main class="pt-90">
  <div class="mb-md-1 pb-md-3"></div>
  <section class="product-single container">
    <div class="row">
      <!-- عرض الصور الرئيسية للمنتج -->
      <div class="col-lg-7">
        <div class="product-single__media" data-media-type="vertical-thumbnail">
          <div class="product-single__image">
            <div class="swiper-container">
              <div class="swiper-wrapper">


                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{asset('uploads/products')}}/{{$product->image}}" width="674"
                    height="674" alt="" />
                  <a data-fancybox="gallery" href="{{asset('uploads/products')}}/{{$product->image}}" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Zoom">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_zoom" />
                    </svg>
                  </a>
                </div>

                @foreach (explode(",",$product->images) as $gimg )
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{asset('uploads/products')}}/{{$gimg}}" width="674"
                    height="674" alt="" />
                  <a data-fancybox="gallery" href="{{asset('uploads/products')}}/{{$gimg}}" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Zoom">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_zoom" />
                    </svg>
                  </a>
                </div>
                @endforeach
              </div>
              <div class="swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                  xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_prev_sm" />
                </svg></div>
              <div class="swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11"
                  xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_next_sm" />
                </svg></div>
            </div>
          </div>
       
          <div class="product-single__thumbnail">
            <div class="swiper-container swiper-container-vertical">
                <div class="swiper-wrapper">
                    <div class="swiper-slide product-single__image-item">
                        <img class="h-auto" src="{{asset('uploads/products/thumbnails')}}/{{$product->image}}" width="104" height="104" alt="{{$product->name}}" />
                    </div>
                    @foreach (explode(",", $product->images) as $gimg)
                        <div class="swiper-slide product-single__image-item">
                            <img class="h-auto" src="{{asset('uploads/products/thumbnails')}}/{{$gimg}}" width="104" height="104" alt="{{$product->name}}" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        


        </div>
      </div>

      <!-- معلومات المنتج -->
      <div class="col-lg-5">
        <h1 class="product-single__name">{{$product->name}}</h1>
        <div class="product-single__price">
          <span class="current-price">
            @if ($product->sale_price) 
              <s>${{$product->regular_price}}</s>  ${{$product->sale_price}}
            @else
              ${{$product->regular_price}}
            @endif
          </span>
        </div>
        <div class="product-single__short-desc">
          <p>{{$product->short_description}}</p>
        </div>

        @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
        <a href="{{route('cart.index')}}" class="btn btn-warning mb-3">Go To Order</a>
        @else
        <form name="addtocart-form" method="post" action="{{route('cart.add')}}">
          @csrf
          <input type="hidden" name="id" value="{{$product->id}}" />
          <input type="hidden" name="quantity" value="1" />
          <input type="hidden" name="name" value="{{$product->name}}" />
          <input type="hidden" name="price" value="{{$product->sale_price == '' ? $product->regular_price : $product->sale_price}}" />

          <button type="submit" class="btn btn-primary">Add to Product</button>
      </form>
      
        @endif
        <div class="product-single__meta-info">
          <div class="meta-item">
            <label>SKU:</label>
            <span>{{$product->SKU}}</span>
          </div>
           
        </div>
      </div>
    </div>

    <!-- تفاصيل إضافية حول المنتج -->
    <div class="product-single__details-tab">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab"
            href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Description</a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-description" role="tabpanel" aria-labelledby="tab-description-tab">
          <div class="product-single__description">
            {{$product->description}}
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- تضمين مكتبة Swiper لتفعيل التنقل بين الصور -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
  var swiper = new Swiper('.swiper-container', {
    slidesPerView: 1,
    spaceBetween: 10,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    loop: true,
  });
</script>

@endsection
