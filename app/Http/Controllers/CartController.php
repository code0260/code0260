<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\ProductOrderSpecification;
use Barryvdh\DomPDF\Facade\Pdf;

class CartController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $items = Cart::instance('cart')->content();
        return view('cart', compact('products', 'items'));
    }

    public function add_to_cart(Request $request)
    {
        $product = Product::with('specifications')->find($request->id);
        $price = $request->price ?? 0.00;

        $specifications = $product->specifications->map(function ($spec) {
            return [
                'name' => $spec->name,
                'title' => $spec->title,
                'paragraphs'  => $spec->paragraphs,
                'images' => is_string($spec->images) ? json_decode($spec->images, true) : $spec->images,
            ];
        })->toArray();

        Cart::instance('cart')->add($request->id, $product->name, $request->quantity, $price, [
            'description' => $product->description,
            'stock_status' => $product->stock_status,
            'featured' => $product->featured,
            'specifications' => $specifications,
            'status' => $product->status
        ])->associate('App\Models\Product');

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = max($product->qty - 1, 1); // Prevents quantity from dropping below 1
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function calculateDiscount()
    {
        $discount = 0;

        if (Session::has('coupon')) {
            $discount = Session::get('coupon')['type'] == 'fixed' ?
                Session::get('coupon')['value'] : (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
        }

        $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
        $totalAfterDiscount = $subtotalAfterDiscount;

        Session::put('discounts', [
            'discount' => number_format($discount, 2, '.', ''),
            'subtotal' => number_format($subtotalAfterDiscount, 2, '.', ''),
            'tax' => 0,
            'total' => number_format($totalAfterDiscount, 2, '.', '')
        ]);
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        return view('checkout', compact('address'));
    }

    public function place_an_order(Request $request)
    {
        $user_id = Auth::user()->id;
        $request->validate([
            'name' => 'required|max:100',
            'phone' => 'required|digits:10',
            'zip' => 'required|digits:6',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',

        ]);

        // إضافة العنوان الجديد
        // إضافة العنوان الجديد للمستخدم
        $address = new Address();
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->zip = $request->zip;
        $address->state = $request->state;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->locality = $request->locality;
        $address->landmark = $request->landmark;
        $address->country = 'Syria';
        $address->user_id = $user_id;
        $address->isdefault = false;
        $address->save();

        // تحديد المبلغ للطلب
        $this->setAmountforCheckout();
        $order = new Order();
        $order->user_id = $user_id;

        // معالجة القيم
        $subtotal = str_replace(',', '', Session::get('checkout')['subtotal']);
        $subtotal = (float)$subtotal;
        $order->subtotal = $subtotal;

        $total = str_replace(',', '', Session::get('checkout')['total']);
        $total = (float)$total;
        $order->total = $total;

        $order->discount = Session::get('checkout')['discount'];
        $order->tax = Session::get('checkout')['tax'];
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->save();


        // إضافة تفاصيل الطلب
        foreach (Cart::instance('cart')->content() as $item) {
            $product = Product::find($item->id);
            $orderItem = OrderItem::create([
                'product_id' => $item->id,
                'order_id' => $order->id,
                'price' => $item->price,
                'product_name' => $product->name,
                'quantity' => $item->qty,
                'custom_specifications' => json_encode($item->options['specifications']), // Serialize the array
            ]);


            foreach ($item->options['specifications'] as $spec) {
                // Access 'images' as an array key
                $images = isset($spec['images']) && is_string($spec['images'])
                    ? json_decode($spec['images'], true)
                    : [];

                $encodedImages = $this->encodeImages($images);

                ProductOrderSpecification::create([
                    'name' => $spec['name'] ?? null,
                    'title' => $spec['title'] ?? null,
                    'paragraphs' => $spec['paragraphs'] ?? null,
                    'images' => $encodedImages,
                    'description' => $spec['description'] ?? null,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $item->id,
                ]);
            }
        }

        // إضافة المعاملة
        Transaction::create([
            'user_id' => $user_id,
            'order_id' => $order->id,
            'mode' => $request->mode ?? 'cod',
            'status' => 'pending',
        ]);

        // تفريغ السلة وتنظيف الجلسة
        Cart::instance('cart')->destroy();
        Session::forget(['checkout', 'coupon', 'discounts']);
        Session::put('order_id', $order->id);

        return redirect()->route('cart.order.confirmation');
    }
    public function setAmountforCheckout()
    {
        if (!Cart::instance('cart')->content()->count() > 0) {
            Session::forget('checkout');
            return;
        }

        // Reset checkout session values
        if (Session::has('coupon')) {
            Session::put('checkout', [
                'discount' => number_format(floatval(Session::get('discounts')['discount']), 2, '.', ''),
                'subtotal' => number_format(floatval(Session::get('discounts')['subtotal']), 2, '.', ''),
                'tax' => 0, // Reset taxes to 0
                'total' => number_format(floatval(Session::get('discounts')['total']), 2, '.', '')
            ]);
        } else {
            // If no coupon is applied, calculate without discounts
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => number_format(floatval(Cart::instance('cart')->subtotal()), 2, '.', ''),
                'tax' => 0, // Reset taxes to 0
                'total' => number_format(floatval(Cart::instance('cart')->total()), 2, '.', '')
            ]);
        }
    }

    /*public function order_confirmation()
                   {
                       if (Session::has('order_id')) {
                           $order = Order::find(Session::get('order_id'));

                           // استرجاع تفاصيل الطلب مع المواصفات المرتبطة بالمنتج
                           $orderItems = OrderItem::with('product.specifications')->where('order_id', $order->id)->get();

                           return view('order-confirmation', compact('order', 'orderItems'));
                       }

                       return redirect()->route('cart.index');
                   }*/
    public function order_confirmation()
    {
        if (Session::has('order_id')) {
            $order = Order::find(Session::get('order_id'));

            // استرجاع تفاصيل الطلب مع المواصفات المعدلة
            $orderItems = OrderItem::with(['product.specifications' => function ($query) {
                $query->orderBy('updated_at', 'desc');
            }])->where('order_id', $order->id)->get();

            return view('order-confirmation', compact('order', 'orderItems'));
        }

        return redirect()->route('cart.index');
    }








    public function update_price(Request $request, $rowId)
    {
        // التحقق من صحة المدخلات
        $request->validate([
            'price' => 'required|numeric|min:0'
        ]);

        // الحصول على المنتج في السلة
        $product = Cart::instance('cart')->get($rowId);

        // تحديث السعر
        Cart::instance('cart')->update($rowId, [
            'price' => $request->price,  // تحديث السعر
            'qty' => $product->qty       // الحفاظ على الكمية كما هي
        ]);

        // العودة إلى الصفحة السابقة مع رسالة النجاح
        return redirect()->back()->with('success', 'Price updated successfully!');
    }


    // في CartController
    public function edit_cart_item($rowId)
    {
        // استرجاع العنصر من السلة
        $item = Cart::instance('cart')->get($rowId);

        if (!$item) {
            return redirect()->route('cart.index')->with('error', 'Item not found in the cart');
        }

        // عرض نموذج التعديل
        return view('cart.edit', compact('item'));
    }

    public function update_cart_item(Request $request, $rowId)
    {
        // استرجاع العنصر من السلة
        $item = Cart::instance('cart')->get($rowId);

        if (!$item) {
            return redirect()->route('cart.index')->with('error', 'Item not found in the cart');
        }

        // التحقق من صحة المدخلات (الكمية والسعر)
        $validated = $request->validate([
            'qty' => 'required|integer|min:1',  // التحقق من الكمية
            'price' => 'required|numeric|min:0',  // التحقق من السعر
        ]);

        // تحديث الكمية والسعر في السلة
        Cart::instance('cart')->update($rowId, [
            'qty' => $validated['qty'],
            'price' => $validated['price'],
            'options' => array_merge((array)$item->options, [
                'specifications' => $request->specifications, // Update specifications in cart
            ]),
        ]);



        // تحديث المواصفات في قاعدة البيانات
        if ($request->has('specifications')) {
            foreach ($request->specifications as $spec) {
                $images = isset($spec['images']) ? $spec['images'] : [];

                // إذا كانت الصور موجودة، نقوم بتخزينها أولاً
                if (isset($spec['images']) && count($spec['images']) > 0) {
                    // رفع الصور وتخزين المسارات
                    $imagePaths = [];
                    foreach ($spec['images'] as $image) {
                        // تخزين الصور في المجلد المناسب
                        $imagePaths[] = $image->store('specifications', 'public');
                    }
                    $images = $imagePaths; // تخزين المسارات الجديدة للصور
                }

                // البحث عن المواصفة الموجودة بناءً على order_item_id و name
                $existingSpecification = ProductOrderSpecification::where('order_item_id', $item->id) // استخدام order_item_id هنا
                    ->where('name', $spec['name'])
                    ->first();

                // إذا كانت المواصفة موجودة، نقوم بتحديثها
                if ($existingSpecification) {
                    $existingSpecification->update([
                        'title' => $spec['title'],
                        'paragraphs' => json_encode($spec['paragraphs']),
                        'images' => json_encode($images),
                    ]);
                } else {
                    // إذا كانت المواصفة غير موجودة، نقوم بإنشائها
                    ProductOrderSpecification::create([
                        'name' => $spec['name'],
                        'title' => $spec['title'],
                        'paragraphs' => json_encode($spec['paragraphs']),
                        'images' => json_encode($images),
                        'order_item_id' => $item->id, // استخدام id من السلة
                        'product_id' => $item->id,    // استخدام id من السلة
                    ]);
                }
            }
        }

        return redirect()->route('cart.index')->with('success', 'Cart item updated successfully!');
    }

    private function encodeImages($images)
    {
        if (is_array($images)) {
            return json_encode($images);
        }

        return $images;
    }


    public function base64EncodeImage($imagePath)
    {
        if (file_exists($imagePath)) {
            $imageData = file_get_contents($imagePath);
            return 'data:image/png;base64,' . base64_encode($imageData);
        }
        return null;
    }

    public function downloadPdf($orderId)
    {
        $order = Order::findOrFail($orderId);

        $orderItems = OrderItem::with('product.specifications')->where('order_id', $order->id)->get();

        $pdf = PDF::loadView('orders.pdf', [
            'order' => $order,
            'orderItems' => $orderItems,
            'base64EncodeImage' => [$this, 'base64EncodeImage']
        ]);


        return $pdf->download('order_' . $order->id . '.pdf');
    }
}
