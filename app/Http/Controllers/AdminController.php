<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Slide;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Contact;
use App\Models\ProductSpecification;
use Carbon\Carbon;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public $timestamps = false;

    public function index()
{
    $orders = Order::orderBy('created_at', 'DESC')->get()->take(10);

    $dashboardDatas = DB::select("
        SELECT 
            SUM(total) AS TotalAmount,
            SUM(IF(status='ordered', total, 0)) AS TotalOrderedAmount,
            SUM(IF(status='delivered', total, 0)) AS TotalDeliveredAmount,
            SUM(IF(status='canceled', total, 0)) AS TotalCanceledAmount,
            COUNT(*) AS Total,
            SUM(IF(status='ordered', 1, 0)) AS TotalOrdered,
            SUM(IF(status='delivered', 1, 0)) AS TotalDelivered,
            SUM(IF(status='canceled', 1, 0)) AS TotalCanceled

        FROM orders
    ");
    $monthlyDatas = DB:: select("SELECT M.id As MonthNo, M.name As MonthName,
    IFNULL(D.TotalAmount,0) As TotalAmount,
    IFNULL(D.TotalOrderedAmount,0) As TotalOrderedAmount,
    IFNULL(D.TotalDeliveredAmount,0) As TotalDeliveredAmount,   
    IFNULL(D.TotalCanceledAmount,0) As TotalCanceledAmount FROM month_names M   
    LEFT JOIN (Select DATE_FORMAT(created_at, '%b') As MonthName,    
    MONTH(created_at) As MonthNo,   
    sum(total) As TotalAmount,   
    sum(if(status='ordered', total, 0)) As TotalorderedAmount,   
    sum(if(status='delivered', total, 0)) As TotalDeliveredAmount,    
    sum(if(status='canceled', total,0)) As TotalCanceledAmount    
    From Orders WHERE YEAR(created_at)=YEAR (NOW()) GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b')   
    Order By MONTH(created_at)) D On D.MonthNo=M.id");   

                        $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
                        $OrderedAmountM = implode(',', collect($monthlyDatas)->pluck('TotalOrderedAmount')->toArray());
                        $DeliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
                        $CanceledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCanceledAmount')->toArray());
                        $TotalAmount = collect($monthlyDatas)->sum('TotalAmount');
                        $TotalOrderedAmount = collect($monthlyDatas)->sum('TotalOrderedAmount');
                        $TotalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
                        $TotalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');

    return view('admin.index', compact('orders', 'dashboardDatas','AmountM','OrderedAmountM','DeliveredAmountM','CanceledAmountM','TotalAmount',
                      'TotalOrderedAmount','TotalDeliveredAmount','TotalCanceledAmount' ));
}

    public function brands ()
    {
        $brands = Brand::orderBy('id','desc')->paginate(10);
        return view('admin.brands', compact('brands'));
    }
    public function brand_add()
    {
        return view('admin.brand-add');
    }
    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',  // تأكد من أن هذه الإشارة إلى جدول brands
            'image' => 'mimes:png,jpg,jpeg'
        ]);
    
        $brand = new Brand();  
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        // معالجة الصورة
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
    
        $this->GenerateBrandThumbailsImage1($image, $file_name);
        $brand->image = $file_name;
    
        // حفظ البيانات في جدول brands
        $brand->save();
    
        return redirect()->route('admin.brands')->with('status', 'Brand has added successfully');
    }
    

public function brand_edit($id)
{
    $brand= Brand::find($id);

    return view('admin.brand-edit', compact('brand'));
}

public function brand_update(Request $request)
{
$request->validate([
    'name' => 'required',

    'slug'=>'required|unique:brands,slug,'.$request->id,

    'image' => 'mimes:png,jpg,jpeg'
]);
$brand=Brand::find($request->id);
$brand->name = $request->name;
$brand->slug = Str::slug($request->name) ;
if ($request->hasFile('image')){

    if(File::exists(public_path('uploads/brand').'/'.$brand->image))
    
    {
    
    File::delete(public_path('uploads/brand').'/'.$brand->image);
    }
    $image =  $request->file('image');
    $file_extention = $request->file('image')->extension();
    
    $file_name=Carbon::now()->timestamp.'.'.$file_extention;
    $this->GenerateBrandThumbailsImage1($image, $file_name);
    $brand->image = $file_name;
}

$brand->save();
return redirect()->route('admin.brands')->with('status','Brand has updated succesfully');
}


public function GenerateBrandThumbailsImage1($image, $imageName) {
    
$destinationPath = public_path('uploads/brands');

$img = Image::read($image->path());

$img->cover(124,124, "top");

$img->resize(124, 124, function($constraint){

$constraint->aspectRatio();

})->save($destinationPath . '/' . $imageName, 100); // جودة 100%
    

}

public function brand_delete($id)
{

$brand = Brand::find($id);

if (File::exists(public_path('uploads/brands').'/'.$brand->image))

{

File::delete(public_path('uploads/brands').'/'.$brand->image);

}

$brand->delete();

return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully!');

}
   
public function categories()

{

$categories = Category:: orderBy('id', 'DESC')->paginate (10);

return view('admin.categories', compact('categories'));
 }
 public function category_add()
 {
    return view('admin.category-add');
 }
 public function category_store (Request $request)

{
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:categories,slug',
        'image' => 'mimes:png,jpg,jpeg',
    ]);
    

$category = new Category();

$category->name = $request->name;

$category->slug = str::slug($request->name);

$image = $request->file('image');

$file_extention = $request->file('image')->extension();

$file_name = Carbon::now()->timestamp.'.'.$file_extention;

$this->GenerateCategoryThumbailsImage($image, $file_name);

$category->image = $file_name;

$category->save();

return redirect()->route('admin.categories')->with('status', 'Category has been added succesfully!');

}




public function GenerateCategoryThumbailsImage($image, $imageName)

{

$destinationPath = public_path('uploads/categories');

$img = Image::read($image->path());

$img->cover(124,124, "top");

$img->resize(124, 124, function($constraint){

$constraint->aspectRatio();

})->save($destinationPath . '/' . $imageName, 100); // جودة 100%
}

public function category_edit($id){

$category = Category:: find($id);

return view('admin.category-edit', compact('category'));
} 

public function category_update(Request $request)
{
$request->validate([
    'name' => 'required',

    'slug'=>'required|unique:categories,slug,'.$request->id,

    'image' => 'mimes:png,jpg,jpeg'
]);
$category=Category::find($request->id);
$category->name = $request->name;
$category->slug = Str::slug($request->name) ;
if ($request->hasFile('image')){

    if(File::exists(public_path('uploads/categories').'/'.$category->image))
    
    {
    
    File::delete(public_path('uploads/categories').'/'.$category->image);
    }
    $image =  $request->file('image');
    $file_extention = $request->file('image')->extension();
    
    $file_name=Carbon::now()->timestamp.'.'.$file_extention;
    $this->GenerateCategoryThumbailsImage($image, $file_name);
    $category->image = $file_name;
}

$category->save();
return redirect()->route('admin.categories')->with('status','Category has updated succesfully');
}

public function category_delete($id)
{

$category = Category::find($id);

if (File::exists(public_path('uploads/categories').'/'.$category->image))

{

File::delete(public_path('uploads/categories').'/'.$category->image);

}

$category->delete();

return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully!');

}

public function products()

{

$products = Product::orderBy('created_at', 'DESC')->paginate(10);

return view('admin.products', compact('products'));
}

public function product_add()

{

$categories = Category:: select('id', 'name')->orderBy('name')->get();

$brands = Brand::select('id', 'name')->orderBy('name')->get();

return view('admin.product-add',compact('categories', 'brands'));
}



public function product_store (Request $request)
{
    $request->validate([
        'name' => 'required',
        'short_description' => 'nullable',
        'description' => 'nullable',
        'regular_price' => 'nullable',
        'sale_price' => 'nullable',
        'SKU' => 'nullable',
        'stock_status' => 'required',
        'featured' => 'nullable',
        'quantity' => 'nullable',
        'image' => 'nullable|mimes:png,jpg,jpeg',
        'category_id' => 'required',
        'brand_id' => 'required',
        'specifications.*.description' => 'nullable|string',
        'specifications.*.image' => 'nullable|mimes:png,jpg,jpeg',
    ]);

    $product = new Product();
    $product->name = $request->name;
    $product->slug= $this->generateReferenceCode($request);
    $product->short_description = $request->short_description ?? null;  // إذا كانت فارغة، يتم تخزين null
    $product->description = $request->description ?? null;
    $product->regular_price = $request->regular_price ?? null;
    $product->sale_price = $request->sale_price ?? null;
    $product->SKU = $request->SKU ?? null;
    $product->stock_status = $request->stock_status;
    $product->featured = $request->featured ?? 0;
    $product->quantity = $request->quantity ?? 0;
    $product->category_id = $request->category_id;
    $product->brand_id = $request->brand_id;
    $product->adding_date = Carbon::now();

$current_timestamp = Carbon::now()->timestamp;
if($request->hasFile('image'))
{
$image = $request->file('image');
$imageName = $current_timestamp. '.' . $image->extension();
$this->GenerateProductThumbnailImage($image,$imageName);
$product->image = $imageName;
}


$gallery_arr = array();
$gallery_images = "";
$counter = 1;
if ($request->hasFile('images'))
{
$allowedfileExtion = ['jpg', 'png', 'jpeg'];
$files = $request->file('images');
foreach($files as $file)
{
$gextension = $file->getClientOriginalExtension();
$gcheck=in_array($gextension, $allowedfileExtion);
if($gcheck)
{
    $gfileName = $current_timestamp. "-" . $counter. "." . $gextension;

    $this->GenerateProductThumbnailImage($file, $gfileName);
    
    array_push($gallery_arr, $gfileName);
    
    $counter = $counter + 1;
}
}
$gallery_images = implode(',', $gallery_arr);
}

$product->images = $gallery_images;

$product->save();
if ($request->has('specifications')) {
    foreach ($request->specifications as $spec) {
        $specification = new ProductSpecification();
        $specification->product_id = $product->id;
        $specification->description = $spec['description'];

        if (isset($spec['image'])) {
            $imageName = time() . '.' . $spec['image']->getClientOriginalExtension();
            $spec['image']->move(public_path('uploads/products/specifications'), $imageName);
            $specification->image = $imageName;
        }

        $specification->save();
    }
}

return redirect()->route('admin.products')->with('status', 'Product added successfully');
} 


public function GenerateProductThumbnailImage($image, $imageName)
{
$destinationPathThumbnail = public_path('uploads/products/thumbnails');
$destinationPath = public_path('uploads/products');
$img = Image::read($image->path());
$img->cover(540, 689, "top");
$img->resize (540, 689, function($constraint) {
$constraint->aspectRatio();
})->save($destinationPath.'/'.$imageName, 100);
$img->resize (104, 104, function($constraint) {
    $constraint->aspectRatio();
    })->save($destinationPathThumbnail.'/'.$imageName, 100);
}
public function product_edit($id)
{
$product= Product::find($id);
$categories= Category::select('id', 'name')->orderBy('name')->get();
$brands = Brand::select('id', 'name')->orderBy('name')->get();
$specifications = ProductSpecification::where('product_id', $id)->get();

return view('admin.product-edit', compact('product', 'categories', 'brands','specifications'));
}

public function product_update (Request $request)
{
    $request->validate([
        'name' => 'required',
        'short_description' => 'nullable',
        'description' => 'nullable',
        'regular_price' => 'nullable',
        'sale_price' => 'nullable',
        'SKU' => 'nullable',
        'stock_status' => 'required',
        'featured' => 'nullable',
        'quantity' => 'nullable',
        'image' => 'nullable|mimes:png,jpg,jpeg',
        'category_id' => 'required',
        'brand_id' => 'required',
         'specifications.*.description' => 'nullable|string',
        'specifications.*.image' => 'nullable|mimes:png,jpg,jpeg'
    ]);

    $product = Product::find($request->id);
    $product->name = $request->name;
    $product->slug= $this->generateReferenceCode($request);
    $product->short_description = $request->short_description ?? null;
    $product->description = $request->description ?? null;
    $product->regular_price = $request->regular_price ?? null;
    $product->sale_price = $request->sale_price ?? null;
    $product->SKU = $request->SKU ?? null;
    $product->stock_status = $request->stock_status;
    $product->featured = $request->featured ?? 0;
    $product->quantity = $request->quantity ?? 0;
    $product->category_id = $request->category_id;
    $product->brand_id = $request->brand_id;
$current_timestamp = Carbon::now()->timestamp;

if($request->hasFile('image'))
{
    if(File::exists(public_path('uploads/products').'/'.$product->image))
    {
        File::delete(public_path('uploads/products').'/'.$product->image);
    }
    if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image))
    {
        File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
    }
$image = $request->file('image');
$imageName = $current_timestamp. '.' . $image->extension();
$this->GenerateProductThumbnailImage($image,$imageName);
$product->image = $imageName;
}


$gallery_arr = array();
$gallery_images = "";
$counter = 1;
if ($request->hasFile('images'))
{
foreach(explode(',', $product->images)as $ofile)
{
    if(File::exists(public_path('uploads/products').'/'.$ofile))
    {
        File::delete(public_path('uploads/products').'/'.$ofile);
    }
    if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile))
    {
        File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
    }
}
$allowedfileExtion = ['jpg', 'png', 'jpeg'];
$files = $request->file('images');
foreach($files as $file)
{
$gextension = $file->getClientOriginalExtension();
$gcheck=in_array($gextension, $allowedfileExtion);
if($gcheck)
{
    $gfileName = $current_timestamp. "-" . $counter. "." . $gextension;

    $this->GenerateProductThumbnailImage($file, $gfileName);
    
    array_push($gallery_arr, $gfileName);
    
    $counter = $counter + 1;
}
}
$gallery_images = implode(',', $gallery_arr);
$product->images = $gallery_images;

}
 
if ($request->has('specifications')) {
    foreach ($request->specifications as $index => $spec) {
        // إذا كانت المواصفة تحتوي على "id"، تحقق من وجودها
        if (isset($spec['id']) && $spec['id']) {
            $specification = ProductSpecification::find($spec['id']);
        } else {
            // إذا لم تحتوي على "id"، أنشئ مواصفة جديدة
            $specification = new ProductSpecification();
            $specification->product_id = $product->id;
        }

        // تحديث الوصف للمواصفة
        $specification->description = $spec['description'];

        // تحقق من وجود صورة قبل إضافتها
        if (isset($spec['image']) && $spec['image']) {
            $imageName = time() . '.' . $spec['image']->extension();
            $spec['image']->move(public_path('uploads/specifications'), $imageName);
            $specification->image = $imageName;
        }

        // حفظ المواصفة
        $specification->save();
    }
}

$product->save();

return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
}
public function product_delete($id)
{
$product=Product::find($id);
if (File::exists(public_path('uploads/products').'/'.$product->image))
{
File::delete(public_path('uploads/products').'/'.$product->image);
}
if(File:: exists(public_path('uploads/products/thumbnails').'/'.$product->image))
{
File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
}
foreach (explode(',', $product->images) as $ofile)
{
if (File::exists(public_path('uploads/products').'/'.$ofile))
{
File::delete (public_path('uploads/products').'/'.$ofile);
}
if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile))
{
File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
}
}
$product->delete();
return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully!');
}

public function coupons()

            {

                $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate (12);
                return view('admin.coupons', compact('coupons'));
            }

            public function coupon_add()
            {
                return view('admin.coupon-add');
            }

   public function coupon_store(Request $request)
{
    $request->validate([
        'code' => 'required',
        'type' => 'required',
        'value' => 'required|numeric',
        'cart_value' => 'required|numeric',
        'expiry_date' => 'required|date',
    ]);

    $coupon = new Coupon();
    $coupon->code = $request->code;
    $coupon->type = $request->type;
    $coupon->value = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date = $request->expiry_date;
    $coupon->save();

    return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully!');
}

public function coupon_edit($id)
{
    $coupon=  Coupon::find($id);
    return view('admin.coupon-edit',compact('coupon'));
}

public function coupon_update(Request $request)
{
    $request->validate([
        'code' => 'required',
        'type' => 'required',
        'value' => 'required|numeric',
        'cart_value' => 'required|numeric',
        'expiry_date' => 'required|date',
    ]);

    $coupon = Coupon::find($request->id);
    $coupon->code = $request->code;
    $coupon->type = $request->type;
    $coupon->value = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date = $request->expiry_date;
    $coupon->save();
    return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully!');
}

public function coupon_delete($id)
{
    $coupon = Coupon::find($id); // تصحيح: إضافة علامة "=" بعد "$coupon"
    $coupon->delete();
    return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted successfully!');
}

public function orders()
{
    $orders = Order::orderBy('created_at', 'DESC')->paginate(12); // جلب البيانات مع التصفح

    return view('admin.orders', compact('orders')); // تمرير المتغير إلى الـ View
}


public function order_details($order_id)
{
    $order = Order::find($order_id);

    $orderItems = OrderItem::where('order_id', $order_id)
        ->orderBy('id')
        ->paginate(12);

    $transaction = Transaction::where('order_id', $order_id)->first();

    return view('admin.order-details', compact('order', 'orderItems', 'transaction'));
}


public function update_order_status(Request $request)
{
    // Find the order by ID
    $order = Order::find($request->order_id);

    // Check if the order exists
    if (!$order) {
        return back()->withErrors(['status' => 'Order not found.']);
    }

    // Update order status
    $order->status = $request->order_status;

    // Set the respective dates based on the status
    if ($request->order_status == 'delivered') {
        $order->delivered_date = Carbon::now();
    } elseif ($request->order_status == 'canceled') {
        $order->canceled_date = Carbon::now();
    }

    // Save the updated order
    $order->save();

    // If the order is delivered, update the transaction status
    if ($request->order_status == 'delivered') {
        $transaction = Transaction::where('order_id', $request->order_id)->first();
        if ($transaction) {
            $transaction->status = 'approved';
            $transaction->save();
        }
    }

    return back()->with("status", "Status changed successfully!");
}


public function slides()
{
    $slides = Slide::orderBy('id', 'DESC')->paginate(12);
    return view('admin.slides', compact('slides'));
}

                    public function slide_add()
                    {
                    return view('admin.slide-add');
                    }


                    public function slide_store(Request $request)
{
    // التحقق من صحة المدخلات
    $request->validate([
        'tagline' => 'required',
        'title' => 'required',
        'subtitle' => 'required',
        'link' => 'required',
        'status' => 'required',
        'image' => 'required|mimes:png,jpg,jpeg'
    ]);

    // إنشاء السلايد
    $slide = new Slide();
    $slide->tagline = $request->tagline;
    $slide->title = $request->title;
    $slide->subtitle = $request->subtitle;
    $slide->link = $request->link;
    $slide->status = $request->status;

    // معالجة الصورة
    $image = $request->file('image');
    $file_extension = $request->file('image')->extension(); // الحصول على امتداد الملف
    $file_name = Carbon::now()->timestamp . '.' . $file_extension; // تعيين اسم الملف باستخدام الوقت الحالي

    // إنشاء الصورة المصغرة
    $this->GenerateSlideThumbailsImage($image, $file_name);

    // حفظ السلايد
    $slide->image = $file_name;
    $slide->save();

    // إعادة التوجيه مع رسالة النجاح
    return redirect()->route('admin.slides')->with("status", "Slide added successfully!");
}

public function GenerateSlideThumbailsImage($image, $imageName)
{
    // تحديد المسار الذي سيتم حفظ الصورة فيه
    $destinationPath = public_path('uploads/slides');

    // استخدام Intervention Image لقراءة وتعديل الصورة
    $img = Image::read($image->path());

    // تغيير حجم الصورة والحفاظ على نسبة الأبعاد
    $img->cover(400,690,"top");
    $img->resize(400, 690, function ($constraint) {
        $constraint->aspectRatio();
    })

    // حفظ الصورة في المسار المحدد
    ->save($destinationPath . '/' . $imageName);
}


            public function slide_edit($id)

            {
            $slide = Slide::find($id);
            return view('admin.slide-edit', compact('slide'));
            }

            public function slide_update(Request $request)
            {

                $request->validate([
                    'tagline' => 'required',
                    'title' => 'required',
                    'subtitle' => 'required',
                    'link' => 'required',
                    'status' => 'required',
                    'image' => 'mimes:png,jpg,jpeg'
                ]);
            
                // إنشاء السلايد
                $slide =  Slide::find($request->id);
                $slide->tagline = $request->tagline;
                $slide->title = $request->title;
                $slide->subtitle = $request->subtitle;
                $slide->link = $request->link;
                $slide->status = $request->status;
            if($request->hasFile('image'))
            {
                if(File::exists(public_path('uploads/slides').'/'.$slide->image))
                {
                    File::delete(public_path('uploads/slides').'/'.$slide->image);
                }
                    $image = $request->file('image');
                    $file_extension = $request->file('image')->extension(); // الحصول على امتداد الملف
                    $file_name = Carbon::now()->timestamp . '.' . $file_extension; // تعيين اسم الملف باستخدام الوقت الحالي

                    // إنشاء الصورة المصغرة
                    $this->GenerateSlideThumbailsImage($image, $file_name);

                    // حفظ السلايد
                    $slide->image = $file_name;
            }
              
                $slide->save();
            
                // إعادة التوجيه مع رسالة النجاح
                return redirect()->route('admin.slides')->with("status", "Slide update successfully!");
            }


            public function slide_delete($id)
            {
                $slide = Slide::find($id);
            
                if (File::exists(public_path('uploads/slides').'/'.$slide->image))
                 {
                    File::delete(public_path('uploads/slides').'/'.$slide->image);
                }
            
                $slide->delete();
            
                return redirect()->route('admin.slides')->with("status", "Slide deleted successfully!");
            }
            

            public function contacts()
            {
                $contacts = Contact::orderBy('created_at', 'DESC')->paginate(10);
            
                return view('admin.contacts', compact('contacts'));
            }
            

                         public function contact_delete($id)

                        {

                        $contact = Contact::find($id);

                        $contact->delete();

                        return redirect()->route('admin.contacts')->with("status", "Contact deleted successfully!");

                        }


                        public function search(Request $request)
                        {
                            $query = $request->input('query');
                            
                            $results = Product::where('name', 'LIKE', "%{$query}%")->limit(8)->get();

                            return response()->json($results);
                        }


                        public function search_order(Request $request)
                        {
                            $query = $request->input('query');
                            
                            // البحث عن الأوامر باستخدام اسم العميل فقط
                            $results = Order::where('name', 'LIKE', "%{$query}%")
                                            ->limit(8)  // تحديد الحد الأقصى للنتائج
                                            ->get();
                        
                            return response()->json($results);  // إرجاع النتائج بتنسيق JSON
                        }


                        private function getProductCode($categoryId)
    {
        $productCodes = [
            1 => 'DEC', // Demountable Container
            2 => 'FLS', // Flatpack Storage Container
            3 => 'FLC', // Flatpack Container
            4 => 'HSH', // Heavy Steel Hangar
            5 => 'LSK', // LGS Modern Kiosk
            6 => 'LSB', // Light Gauge Steel Building
            7 => 'LSH', // Light Gauge Steel House
            8 => 'MOB', // Modular Building
            9 => 'MOH', // Modular House
            10 => 'PAC', // Panel Cabin
            11 => 'PER', // Pergola
            12 => 'POC', // Polyester Cabin
            13 => 'PEB', // Pre-Engineered Building
            14 => 'TEN', // Tent
        ];

        return $productCodes[$categoryId] ?? 'UNK'; // UNK للمنتجات غير المعروفة
    }

                        private function generateReferenceCode(Request $request)
{
    // **الحصول على تاريخ الطلب**
    $date = Carbon::now()->format('ymd');

    // **تحديد كود المنتج**
    $productType = $this->getProductCode($request->category_id); // يجب كتابة دالة getProductCode

    // **الحصول على رقم الموظف**
    $employeeId = str_pad(Auth::user()->id, 3, '0', STR_PAD_LEFT);

    // **تحديد الرقم التسلسلي**
    $sequence = Product::whereDate('created_at', Carbon::today())
                ->where('category_id', $request->category_id)
                ->count() + 1;

    $sequenceFormatted = str_pad($sequence, 3, '0', STR_PAD_LEFT);

    // **صياغة الريفرنس كود**
    return "{$date}-{$productType}-{$employeeId}-{$sequenceFormatted}";
}

public function generateReferenceCodeAjax(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
    ]);

    $referenceCode = $this->generateReferenceCode($request);

    return response()->json(['reference_code' => $referenceCode]);
}


public function generateOrderPDF($id)
{
    $order = Order::with('orderItems.product.category')->findOrFail($id);

    $pdf = Pdf::loadView('admin.orders.pdf', compact('order'))->setPaper('a4', 'portrait');

    $fileName = 'Order-' . $order->id . '.pdf';

    // حفظ الملف في مجلد
    $path = public_path('uploads/orders/');
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    $pdf->save($path . $fileName);

    // تحديث عمود landmark لحفظ مسار PDF
    $order->landmark = 'uploads/orders/' . $fileName;
    $order->save();

    return response()->download($path . $fileName);
}





}