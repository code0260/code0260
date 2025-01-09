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
            'phone' => 'nullable|digits:10',
            'zip' => 'nullable|digits:6',
            'state' => 'nullable',
            'city' => 'nullable',
            'address' => 'nullable',
            'locality' => 'nullable',
            'extra' => 'required',
            'images' => 'nullable|array', // Validate as an array
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048', // Validate each file in the array

        ]);

        $uploadedImages = [];
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {
                $path = $image->store('orders/images', 'public'); // Store in the public disk
                $uploadedImages[] = $path;
            }
        }



        // إضافة العنوان الجديد
        // إضافة العنوان الجديد للمستخدم
        $address = new Address();
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->zip = '00000';
        $address->state = '02135';
        $address->city = 'Mashru Dummar';
        $address->address = 'Mashru Dummar';
        $address->locality = 'Mashru Dummar';
        $address->country =  $request->country;
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
        $order->extra = $request->extra;
        $order->zip = $address->zip;
        $order->images = $uploadedImages ? json_encode($uploadedImages) : null;

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



                ProductOrderSpecification::create([
                    'name' => $spec['name'] ?? null,
                    'title' => $spec['title'] ?? null,
                    'paragraphs' => $spec['paragraphs'] ?? null,
                    'images' => json_encode($spec['images']),
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

            if (!$order) {
                return redirect()->route('cart.index')->with('error', 'Order not found');
            }

            // Fetch order items
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            // Get all cart items
            $cartItems = Cart::instance('cart')->content();

            // Attach specifications and descriptions to order items
            foreach ($orderItems as $item) {
                foreach ($cartItems as $cartItem) {
                    if ($cartItem->id == $item->product_id) { // Match product ID
                        $specifications = $cartItem->options->specifications ?? [];

                        // Ensure specifications are always an array
                        if (!is_array($specifications)) {
                            $specifications = json_decode($specifications, true);
                        }

                        $item->specifications = $specifications;
                        $item->description = $cartItem->options->description ?? '';
                        break;
                    }
                }
            }

            Session::forget(['checkout', 'coupon', 'discounts']);

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
            'description' => 'nullable|string',  // التحقق من الوصف
        ]);

        $specifications = $request->specifications;

        if ($specifications && is_array($specifications)) {
            foreach ($specifications as &$spec) {
                if (isset($spec['images']) && is_array($spec['images'])) {
                    $imagePaths = [];
                    foreach ($spec['images'] as $image) {
                        if ($image instanceof \Illuminate\Http\UploadedFile) {
                            $imagePaths[] = $image->store('specifications', 'public');
                        }
                    }
                    $spec['images'] = $imagePaths;
                }
            }
        }

        Cart::instance('cart')->update($rowId, [
            'qty' => $validated['qty'],
            'price' => $validated['price'],
            'options' => array_merge((array)$item->options, [
                'specifications' => $specifications,
                'description' => $validated['description'],
            ]),
        ]);

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

    /*public function downloadPdf($orderId)
    {
        $order = Order::findOrFail($orderId);

        $orderItems = OrderItem::with('product.specifications')->where('order_id', $order->id)->get();

        $pdf = PDF::loadView('orders.pdf', [
            'order' => $order,
            'orderItems' => $orderItems,
            'base64EncodeImage' => [$this, 'base64EncodeImage']
        ]);


        return $pdf->download('order_' . $order->id . '.pdf');
    }*/

    public function base64EncodeImageA($image)
    {
        // مسار الصورة الكامل
        $fullPath = public_path('storage/' . $image);

        // تحقق إذا كانت الصورة موجودة
        if (file_exists($fullPath)) {
            $imageData = file_get_contents($fullPath);
            return 'data:image/' . pathinfo($fullPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageData);
        }

        // إذا لم تكن الصورة موجودة
        return null;
    }

    public function downloadPdf($orderId)
    {
        // جلب الطلب
        $order = Order::findOrFail($orderId);
        $cartItems = Cart::instance('cart')->content();

        // جلب عناصر الطلب مع مواصفات المنتج
        $orderItems = OrderItem::with(['product' => function ($query) {
            $query->select('id', 'name', 'slug');
        }])->where('order_id', $order->id)->get();

        // دمج المواصفات والوصف
        foreach ($orderItems as $item) {
            foreach ($cartItems as $cartItem) {
                if ($cartItem->id == $item->product_id) { // مطابقة المنتج
                    $specifications = $cartItem->options->specifications ?? [];

                    // التأكد من أن المواصفات هي مصفوفة
                    if (!is_array($specifications)) {
                        $specifications = json_decode($specifications, true);
                    }

                    $item->specifications = $specifications;
                    $item->description = $cartItem->options->description ?? '';
                    break;
                }
            }
        }

        // تحميل ملف PDF
        $pdf = PDF::loadView('orders.pdf', [
            'order' => $order,
            'orderItems' => $orderItems,
            'cartItems' => $cartItems,
            'base64EncodeImageA' => [$this, 'base64EncodeImageA'], // تم تمرير دالة تحويل الصورة
        ]);

        Cart::instance('cart')->destroy();

        return $pdf->download('order_' . $order->id . '.pdf');
    }



    public function updateDescription($rowId, Request $request)
    {
        // التحقق من صحة المدخلات
        $request->validate([
            'description' => 'required|string|max:255'  // التحقق من الوصف
        ]);

        // الحصول على المنتج في السلة
        $product = Cart::instance('cart')->get($rowId);

        // الاحتفاظ بالمواصفات الحالية في حالة عدم تعديلها
        $currentSpecifications = $product->options['specifications'] ?? [];

        // تحديث الوصف مع الاحتفاظ بالمواصفات
        Cart::instance('cart')->update($rowId, [
            'options' => [
                'description' => $request->description,
                'specifications' => $currentSpecifications,  // الحفاظ على المواصفات كما هي
            ],
            'qty' => $product->qty  // الحفاظ على الكمية كما هي
        ]);

        // العودة إلى الصفحة السابقة مع رسالة النجاح
        return redirect()->back()->with('success', 'Description updated successfully!');
    }
}
