<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AuthAdmin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Middleware\RedirectIfNotAuthenticated;

Auth::routes();
Route::delete('/order/{order_id}/image', [UserController::class, 'deleteOrderImage'])->name('order.image.delete');
Route::delete('/specification/image/delete', [UserController::class, 'deleteSpecificationImage'])->name('specification.image.delete');

Route::middleware([RedirectIfNotAuthenticated::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
});
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');



Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::put('/cart/update-price/{rowId}', [CartController::class, 'update_price'])->name('cart.price.update');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');


// في ملف routes/web.php
Route::get('cart/{rowId}/edit', [CartController::class, 'edit_cart_item'])->name('cart.edit');
Route::prefix('cart')->group(function () {
    Route::put('update/{rowId}', [CartController::class, 'update_cart_item'])->name('cart.update');
    Route::put('specifications/update/{rowId}', [CartController::class, 'update_specifications'])->name('cart.specifications.update');
});

Route::get('/order/{orderId}/download-pdf', [CartController::class, 'downloadPdf'])->name('order.downloadPdf');

//Route::put('/cart/specifications/update/{rowId}/{specIndex}', [CartController::class, 'updateSpecifications'])->name('cart.specifications.update');
Route::put('/cart/description/update/{rowId}', [CartController::class, 'updateDescription'])->name('cart.description.update');



Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/place-an-order', [CartController::class, 'place_an_order'])->name('cart.place.an.order');
Route::get('/order-confirmation', [CartController::class, 'order_confirmation'])->name('cart.order.confirmation');

Route::get('/search', [HomeController::class, 'search'])->name('home.search');


Route::middleware(['auth'])->group(function () {

    Route::post('/admin/orders/update-note', [AdminController::class, 'updateNote'])->name('admin.orders.updateNote');


    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');

    Route::get('/account-orders', [UserController::class, 'orders'])->name('user.orders');

    Route::get('/user/order/edit/{order_id}', [UserController::class, 'editOrder'])->name('user.order.edit');
    Route::put('/user/order/update/{order_id}', [UserController::class, 'updateOrder'])->name('user.order.update');


    Route::get('/account-orders/{order_id}/details', [UserController::class, 'order_details'])->name('user.order.details');
    Route::put('/account-orders/cancel-order', [UserController::class, 'order_cancel'])->name('user.order.cancel');
});

Route::middleware(['auth', AuthAdmin::class])->group(function () {

    Route::get('/admin', [AdminController::class, 'index'])->name("admin.index");


    Route::post('/admin/generate-reference-code', [AdminController::class, 'generateReferenceCodeAjax'])->name('admin.generate.reference.code');



    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('/admin/category/store', [AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('/admin/category/edit/{id}', [AdminController::class, 'category_edit'])->name('admin.category-edit');
    Route::put('/admin/category/update', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete', [AdminController::class, 'category_delete'])->name('admin.category.delete');


    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/add', [AdminController::class, 'product_add'])->name('admin.product.add');
    Route::post('/admin/product/store', [AdminController::class, 'product_store'])->name('admin.product.store');
    Route::get('/admin/product/edit/{id}', [AdminController::class, 'product_edit'])->name('admin.product.edit');
    Route::put('/admin/product/update', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('/admin/product/{id}/delete', [AdminController::class, 'product_delete'])->name('admin.product.delete');




    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');


    Route::get('/admin/order/{order_id}/details', [AdminController::class, 'order_details'])->name('admin.order.details');
    Route::put('/admin/order/update-status', [AdminController::class, 'update_order_status'])->name('admin.order.status.update');


    Route::get('/admin/slides', [AdminController::class, 'slides'])->name('admin.slides');
    Route::get('/admin/slide/add', [AdminController::class, 'slide_add'])->name('admin.slide.add');
    Route::post('/admin/slide/store', [AdminController::class, 'slide_store'])->name('admin.slide.store');
    Route::get('/admin/slide/edit/{id}', [AdminController::class, 'slide_edit'])->name('admin.slide.edit');
    Route::put('/admin/slide/update', [AdminController::class, 'slide_update'])->name('admin.slide.update');
    Route::delete('/admin/slide/{id}/delete', [AdminController::class, 'slide_delete'])->name('admin.slide.delete');


    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');
    Route::get('/admin/orders/search', action: [AdminController::class, 'search_order'])->name('admin.orders.search');

    Route::get('/admin/order/{id}/generate-pdf', [AdminController::class, 'generateOrderPDF'])->name('admin.order.generate.pdf');
});