<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
class CartController extends Controller
{
    public function index()
    {
        // جلب جميع المنتجات من قاعدة البيانات
             $products = Product::all();
        $items=Cart::instance('cart')->content();
        return view('cart',compact('products', 'items'));
    }
    public function add_to_cart(Request $request)
{
    // إذا لم يكن هناك سعر مُقدم من المستخدم، يمكننا تعيين السعر الافتراضي أو تركه فارغًا
    $price = $request->price ? $request->price : 0.00;

    // إضافة المنتج إلى السلة
    Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $price)->associate('App\Models\Product');

    return redirect()->back()->with('success', 'Product added to cart successfully!');
}

    
    public function increase_cart_quantity($rowId)

{

$product = Cart::instance('cart')->get($rowId); 
$qty =$product->qty + 1;

Cart::instance('cart')->update($rowId, $qty);

return redirect()->back();

}

public function decrease_cart_quantity($rowId)

{

$product= Cart::instance('cart')->get($rowId);

$qty =$product->qty - 1; Cart::instance('cart')->update($rowId, $qty);
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

public function apply_coupon_code(Request $request)
{
    $coupon_code = $request->coupon_code;

    if (isset($coupon_code)) {
        $coupon = Coupon::where('code', $coupon_code)
            ->where('expiry_date', '>=', Carbon::today())
            ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
            ->first();

        if (!$coupon) {
            return redirect()->back()->with('error', 'Invalid coupon code!');
        } else {
            Session::put('coupon', [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value,
            ]);

            $this->calculateDiscount();

            return redirect()->back()->with('success', 'Coupon has been applied!');
        }
    } else {
        return redirect()->back()->with('error', 'Invalid coupon code!');
    }
}

public function calculateDiscount()
{
    $discount = 0;
    $tax = 0;
    
    // Check if there is a coupon in session
    if (Session::has('coupon')) {
        // Apply the coupon discount based on its type (fixed or percentage)
        if (Session::get('coupon')['type'] == 'fixed') {
            $discount = Session::get('coupon')['value'];
        } else {
            $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
        }
    }

    // Calculate the subtotal after discount
    $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
    
    // Reset tax value to 0 (as requested to "zero out" taxes)
    $taxAfterDiscount = 0;

    // The total after applying the discount (no tax applied)
    $totalAfterDiscount = $subtotalAfterDiscount; // No taxes are included

    // Store the updated values in the session
    Session::put('discounts', [
        'discount' => number_format(floatval($discount), 2, '.', ''),
        'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
        'tax' => 0,  // Set tax to 0
        'total' => number_format(floatval($totalAfterDiscount), 2, '.', '')
    ]);
}


public function remove_coupon_code()

                        {
                        Session::forget('coupon');
                        Session::forget('discounts');
                        return back()->with('success', 'Coupon has been removed!');
                        }

                    public function checkout()
                    {
                    if(!Auth::check())
                    {
                    return redirect()->route('login');
                    }
                    $address = Address::where('user_id', Auth:: user()->id)->where('isdefault', 1)->first();
                    return view('checkout', compact('address'));
                   }


                   public function place_an_order(Request $request)
                   {
                       $user_id = Auth::user()->id;
                   
                       // التحقق من صحة المدخلات
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
                   
                       // إنشاء الطلب
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
                   
                       // إضافة العناصر في السلة للطلب
                       foreach (Cart::instance('cart')->content() as $item) {
                           $orderItem = new OrderItem();
                           $orderItem->product_id = $item->id;
                           $orderItem->order_id = $order->id;
                           $orderItem->price = $item->price;
                           $orderItem->quantity = $item->qty;
                           $orderItem->save();
                       }
                   
                       // إجراء المعاملة
                       $transaction = new Transaction();
                       $transaction->user_id = $user_id;
                       $transaction->order_id = $order->id;
                       $transaction->mode = $request->mode ?? 'cod';
                       $transaction->status = "pending";
                       $transaction->save();
                   
                       // تنظيف السلة بعد إتمام الطلب
                       Cart::instance('cart')->destroy();
                       Session::forget('checkout');
                       Session::forget('coupon');
                       Session::forget('discounts');
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
                   
                                       public function order_confirmation()

                    {
                    
                    if (Session::has('order_id'))
                    
                    {
                    
                    $order = Order::find (Session::get('order_id'));
                    
                    return view('order-confirmation', compact('order'));
                    
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
  
}



                    
                    

