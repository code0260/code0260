@extends('layouts.app')
@section('content')
<style>
  .shop-checkout {
    font-family: Arial, sans-serif;
    margin: 20px auto;
  }
  .page-title {
    text-align: center;
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
  }
  .cart-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }
  .cart-table th, .cart-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
  }
  .cart-table th {
    background-color: #f8f8f8;
    color: #555;
  }
  .cart-table__wrapper {
    overflow-x: auto;
  }
  .shopping-cart__product-item__detail h4 {
    font-size: 16px;
    color: #333;
  }
  .specification {
    margin: 10px 0;
    font-size: 14px;
    color: #555;
  }
  .specification p {
    margin: 5px 0;
  }
  .spec-images img {
    margin: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100px;
    height: 100px;
    object-fit: cover;
  }
  .qty-control {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .qty-control__number {
    width: 50px;
    text-align: center;
    margin: 0 5px;
  }
  .btn {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  .btn-primary {
    background-color: #007bff;
    color: white;
  }
  .btn-info {
    background-color: #17a2b8;
    color: white;
  }
  .shopping-cart__totals-wrapper {
    margin-top: 20px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9;
  }
  .mobile_fixed-btn_wrapper {
    text-align: center;
    margin-top: 20px;
  }
  .specifications-list {
    display: none;
  }
</style>

<main class="pt-90">
    <section class="shop-checkout container">
      <h2 class="page-title">Order</h2>

      <div class="shopping-cart">
        @if ($items->count() > 0)
          <div class="cart-table__wrapper">
            <table class="cart-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Specifications</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Subtotal</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($items as $item)
                <tr>
                  <td>
                    <h4>{{ $item->name }}</h4>
                  </td>
                  <td>
                    <button class="btn btn-info" onclick="toggleSpecifications({{ $loop->index }})">Toggle Specifications</button>
                    <div class="specifications-list" id="specifications-{{ $loop->index }}">
                      @foreach ($item->options['specifications'] as $key => $spec)
                        <form method="POST" action="{{ route('cart.specifications.update', ['rowId' => $item->rowId, 'specIndex' => $key]) }}">
                          @csrf
                          @method('PUT')
                          <div class="specification">
                            <strong>{{ $spec['name'] }}:</strong>
                            <input type="text" name="specifications[{{ $key }}][title]" value="{{ $spec['title'] ?? '' }}" placeholder="Enter title" class="form-control">
                            @foreach ($spec['paragraphs'] as $index => $paragraph)
                              <input type="text" name="specifications[{{ $key }}][paragraphs][{{ $index }}]" value="{{ $paragraph }}" placeholder="Enter paragraph" class="form-control">
                            @endforeach
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Update Specification</button>
                          </div>
                        </form>
                      @endforeach
                    </div>
                  </td>
                  <td>
                    <span class="shopping-cart__product-price">${{ $item->price }}</span>
                    <form method="POST" action="{{ route('cart.price.update', ['rowId' => $item->rowId]) }}" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <div class="d-flex align-items-center">
                            <input type="number" name="price" value="{{ $item->price }}" step="0.01" class="form-control form-control-sm" style="width: 80px; margin-right: 5px;">
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </div>
                    </form>
                  </td>
                  <td>
                    <div class="qty-control">
                      <form method="POST" action="{{ route('cart.qty.decrease', ['rowId' => $item->rowId]) }}">
                        @csrf @method('PUT')
                        <button class="btn btn-primary">-</button>
                      </form>
                      <input type="number" class="qty-control__number" value="{{ $item->qty }}">
                      <form method="POST" action="{{ route('cart.qty.increase', ['rowId' => $item->rowId]) }}">
                        @csrf @method('PUT')
                        <button class="btn btn-primary">+</button>
                      </form>
                    </div>
                  </td>
                  <td>${{ $item->subTotal() }}</td>
                  <td>
                    <form method="POST" action="{{ route('cart.item.remove', ['rowId' => $item->rowId]) }}">
                      @csrf @method('DELETE')
                      <button class="btn btn-danger">Remove</button>
                    </form>
                    <a href="{{ route('cart.edit', ['rowId' => $item->rowId]) }}" class="btn btn-info">Edit</a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>

            <div class="shopping-cart__totals-wrapper">
              <h3>Product Totals</h3>
              <p>Total: ${{ Cart::instance('cart')->subtotal() }}</p>
              <div class="mobile_fixed-btn_wrapper">
                <a href="{{ route('cart.checkout') }}" class="btn btn-primary">PROCEED TO CHECKOUT</a>
              </div>
            </div>

          </div>
        @else
        <p class="text-center">No items in your cart. <a href="{{ route('shop.index') }}" class="btn btn-info">Order Now</a></p>
        @endif
      </div>
    </section>
</main>

@endsection

@push('scripts')

<script>
  function toggleSpecifications(index) {
    var specifications = document.getElementById('specifications-' + index);
    specifications.style.display = (specifications.style.display === 'none' || specifications.style.display === '') ? 'block' : 'none';
  }

  $(function() {
    $(".qty-control__increase").on("click", function() {
      $(this).closest('form').submit();
    });
    $(".qty-control__reduce").on("click", function() {
      $(this).closest('form').submit();
    });
    $('.remove-cart').on("click", function() {
      $(this).closest('form').submit();
    });
  });
</script>

@endpush
