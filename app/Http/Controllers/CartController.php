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
 use Illuminate\Support\Facades\Log;
 use Illuminate\Http\UploadedFile;
class CartController extends Controller
{
    public function index()
    {
        $products = Product::with('specifications')->get();
        $items = Cart::instance('cart')->content();

        foreach ($items as $item) {
            $product = Product::with('specifications')->find($item->id);
            $item->specifications = $product ? $product->specifications : collect();
        }

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
                'paragraphs' => is_string($spec->paragraphs) ? json_decode($spec->paragraphs, true) : $spec->paragraphs,
                'images' => isset($spec->images) && is_string($spec->images) ? json_decode($spec->images, true) : [],
            ];
        })->toArray();
        
        Cart::instance('cart')->add(
            $request->id,
            $product->name,
            $request->quantity,
            $price,
            [
                'description' => $product->description,
                'stock_status' => $product->stock_status,
                'featured' => $product->featured,
                'specifications' => $specifications,  // Use specifications with file paths
                'status' => $product->status
            ]
        )->associate(Product::class);

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
                Session::get('coupon')['value'] :
                (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
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
        $address = Address::where('user_id', Auth:: user()->id)->where('isdefault', 1)->first();
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
    
        // تحديث بيانات السلة
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
    
        // تحديث التفاصيل الخاصة بالعناصر في السلة والطلب
        foreach (Cart::instance('cart')->content() as $item) {
            $product = Product::find($item->id);
    
            if ($product) {
                $orderItem = OrderItem::create([
                    'product_id' => $item->id,
                    'order_id' => $order->id,
                    'price' => $item->price,
                    'product_name' => $product->name,
                    'quantity' => $item->qty,
                    'custom_specifications' => $product->specifications ? $product->specifications->toJson() : null,
                ]);
    
                foreach ($product->specifications as $spec) {
                    ProductOrderSpecification::create([
                        'name' => $spec->name,
                        'title' => $spec->title,
                        'paragraphs' => json_encode($spec->paragraphs),
                        'images' => $this->encodeImages($spec->images),
                        'description' => $spec->description,
                        'order_item_id' => $orderItem->id,
                        'product_id' => $item->id,
                    ]);
                }
                
            }
        }
    
        // إنشاء معاملة الدفع
        Transaction::create([
            'user_id' => $user_id,
            'order_id' => $order->id,
            'mode' => $request->mode ?? 'cod',
            'status' => 'pending',
        ]);
    
        // تفريغ سلة التسوق وإلغاء القسائم
        //Cart::instance('cart')->destroy();
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
        
        // إعادة تعيين قيم الجلسة بناءً على البيانات الجديدة
        if (Session::has('coupon')) {
            Session::put('checkout', [
                'discount' => number_format(floatval(Session::get('discounts')['discount']), 2, '.', ''),
                'subtotal' => number_format(floatval(Session::get('discounts')['subtotal']), 2, '.', ''),
                'tax' => 0, // إعادة تعيين الضرائب إلى 0
                'total' => number_format(floatval(Session::get('discounts')['total']), 2, '.', '')
            ]);
        } else {
            // إذا لم يتم تطبيق القسيمة، احسب بدون خصم
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => number_format(floatval(Cart::instance('cart')->subtotal()), 2, '.', ''),
                'tax' => 0, // إعادة تعيين الضرائب إلى 0
                'total' => number_format(floatval(Cart::instance('cart')->total()), 2, '.', '')
            ]);
        }
    }
  
    /*public function order_confirmation() {
        if (Session::has('order_id')) {
            // Retrieve the order based on the stored order_id in the session
            $order = Order::find(Session::get('order_id'));
        
            if (!$order) {
                return redirect()->route('cart.index')->with('error', 'Order not found');
            }
        
            // Ensure $order is not null before accessing its properties
            if ($order->address) {
                // Access properties of the order if address exists
                $address = $order->address;
            } else {
                return redirect()->route('cart.index')->with('error', 'Address not found for this order');
            }
        
            // Retrieve the cart items (without updating the cart)
            $cartItems = Cart::instance('cart')->content();
        
            // Return the confirmation view with the order and cart items
            return view('order-confirmation', compact('order', 'cartItems', 'address'));
        }
    
        return redirect()->route('cart.index');
    }
    */
    public function order_confirmation() {
        if (Session::has('order_id')) {
            // Retrieve the order based on the stored order_id in the session
            $order = Order::find(Session::get('order_id'));
    
            if (!$order) {
                return redirect()->route('cart.index')->with('error', 'Order not found');
            }
    
            // Ensure $order is not null before accessing its properties
            if ($order->address) {
                $address = $order->address;
            } else {
                return redirect()->route('cart.index')->with('error', 'Address not found for this order');
            }
    
            // Retrieve the cart items (without updating the cart)
            $cartItems = Cart::instance('cart')->content();
    
            // Return the confirmation view with the order and cart items
            return view('order-confirmation', compact('order', 'cartItems', 'address'));
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
   /* public function update_cart_item(Request $request, $rowId) {
        // استرجاع العنصر من السلة
        $item = Cart::instance('cart')->get($rowId);
    
        if (!$item) {
            return redirect()->route('cart.index')->with('error', 'Item not found in the cart');
        }
    
        // التحقق من صحة المدخلات
        $validated = $request->validate([
            'qty' => 'required|integer|min:1', // التحقق من الكمية
            'price' => 'required|numeric|min:0', // التحقق من السعر
            'specifications' => 'nullable|array', // المواصفات اختيارية ويجب أن تكون مصفوفة
        ]);
    
        // تحديث العنصر في السلة
        $updatedCartOptions = $item->options;
        
        // تحديث المواصفات إذا كانت موجودة في الطلب
        if ($request->has('specifications')) {
            $updatedCartOptions['specifications'] = $request->specifications;
        }
    
        // تحديث الكمية والسعر
        Cart::instance('cart')->update($rowId, [
            'qty' => $validated['qty'],
            'price' => $validated['price'],
            'options' => $updatedCartOptions,  // تحديث الخيارات مع المواصفات الجديدة
        ]);
    
        // معالجة الصور للمواصفات
        if ($request->has('specifications')) {
            foreach ($request->specifications as $spec) {
                // معالجة الصور
                $imagePaths = [];
                if (isset($spec['images']) && is_array($spec['images'])) {
                    foreach ($spec['images'] as $image) {
                        if ($image instanceof UploadedFile) {
                            $imagePaths[] = $image->store('specifications', 'public');
                        } else {
                            $imagePaths[] = $image;
                        }
                    }
                }
    
                // تحديث أو إضافة المواصفات في قاعدة البيانات
                $existingSpec = ProductOrderSpecification::where('order_item_id', $item->id)
                    ->where('name', $spec['name'])
                    ->first();
    
                if ($existingSpec) {
                    $existingSpec->update([
                        'title' => $spec['title'] ?? null,
                        'paragraphs' => isset($spec['paragraphs']) ? json_encode($spec['paragraphs']) : null,
                        'images' => json_encode($imagePaths),
                        'description' => $spec['description'] ?? null,
                    ]);
                } else {
                    ProductOrderSpecification::create([
                        'name' => $spec['name'],
                        'title' => $spec['title'] ?? null,
                        'paragraphs' => isset($spec['paragraphs']) ? json_encode($spec['paragraphs']) : null,
                        'images' => json_encode($imagePaths),
                        'order_item_id' => $item->id,
                        'product_id' => $item->id,
                        'description' => $spec['description'] ?? null,
                    ]);
                }
            }
        }
    
        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('cart.index')->with('success', 'Cart item and specifications updated successfully!');
    }*/
    
    public function update_cart_item(Request $request, $rowId) {
        // استرجاع العنصر من السلة
        $item = Cart::instance('cart')->get($rowId);
    
        if (!$item) {
            return redirect()->route('cart.index')->with('error', 'Item not found in the cart');
        }
    
        // التحقق من صحة المدخلات
        $validated = $request->validate([
            'qty' => 'required|integer|min:1', // التحقق من الكمية
            'price' => 'required|numeric|min:0', // التحقق من السعر
            'specifications' => 'nullable|array', // المواصفات اختيارية ويجب أن تكون مصفوفة
        ]);
    
        // تحديث العنصر في السلة
        $updatedCartOptions = $item->options;
    
        // تحديث المواصفات إذا كانت موجودة في الطلب
        if ($request->has('specifications')) {
            $updatedCartOptions['specifications'] = $request->specifications;
        }
    
        // تحديث الكمية والسعر
        Cart::instance('cart')->update($rowId, [
            'qty' => $validated['qty'],
            'price' => $validated['price'],
            'options' => $updatedCartOptions,  // تحديث الخيارات مع المواصفات الجديدة
        ]);
    
        // معالجة الصور للمواصفات
        if ($request->has('specifications')) {
            foreach ($request->specifications as $spec) {
                // استخدام دالة encodeImages لمعالجة الصور
                $imagePaths = $this->encodeImages($spec['images'] ?? []);
                
                // تحديث أو إضافة المواصفات في قاعدة البيانات
                $existingSpec = ProductOrderSpecification::where('order_item_id', $item->id)
                    ->where('name', $spec['name'])
                    ->first();
    
                if ($existingSpec) {
                    $existingSpec->update([
                        'title' => $spec['title'] ?? null,
                        'paragraphs' => isset($spec['paragraphs']) ? json_encode($spec['paragraphs']) : null,
                        'images' => $imagePaths,
                        'description' => $spec['description'] ?? null,
                    ]);
                } else {
                    ProductOrderSpecification::create([
                        'name' => $spec['name'],
                        'title' => $spec['title'] ?? null,
                        'paragraphs' => isset($spec['paragraphs']) ? json_encode($spec['paragraphs']) : null,
                        'images' => $imagePaths,
                        'order_item_id' => $item->id,
                        'product_id' => $item->id,
                        'description' => $spec['description'] ?? null,
                    ]);
                }
            }
        }
    
        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('cart.index')->with('success', 'Cart item and specifications updated successfully!');
    }
    
    private function encodeImages($images)
{
    $imagePaths = [];
    if ($images && is_array($images)) {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                // Store the image file and save the path in the array
                $imagePaths[] = $image->store('specifications', 'public');
            } else {
                // If already a path, add it directly
                $imagePaths[] = $image;
            }
        }
    }
    return json_encode($imagePaths); // Return the image paths as a JSON-encoded string
}

    
    
    
    
      /* private function encodeImages($images)
    {
        $imagePaths = [];
        if ($images && is_array($images)) {
            foreach ($images as $image) {
                $imagePaths[] = is_string($image) ? $image : (isset($image['path']) ? $image['path'] : '');
            }
        }
        return json_encode($imagePaths);
    }*/
    

    
    public function base64EncodeImage($imagePath)
    {
        if (file_exists($imagePath)) {
            $imageData = file_get_contents($imagePath);
            return 'data:image/png;base64,' . base64_encode($imageData); // ترميز الصورة إلى base64
        }
        return null;
    }

  
   
    
    public function downloadPdf($orderId)
    {
        // استرجاع الطلب بناءً على الـ ID
        $order = Order::find($orderId);
    
        // التأكد من وجود الطلب
        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Order not found');
        }
    
        // استرجاع العناصر الموجودة في السلة
        $cartItems = Cart::instance('cart')->content();
    
        // توليد PDF باستخدام الـ View مع المسار الجديد
        $pdf = PDF::loadView('orders.pdf', ['order' => $order, 'cartItems' => $cartItems]);
    
        // تنزيل الـ PDF
        return $pdf->download('order-details.pdf');
    }
    



}
