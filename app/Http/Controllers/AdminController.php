<?php

namespace App\Http\Controllers;
use App\Models\Category;

use App\Models\Product;
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
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Carbon\Carbon;

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
        $monthlyDatas = DB::select("SELECT M.id As MonthNo, M.name As MonthName,
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

        return view('admin.index', compact(
            'orders',
            'dashboardDatas',
            'AmountM',
            'OrderedAmountM',
            'DeliveredAmountM',
            'CanceledAmountM',
            'TotalAmount',
            'TotalOrderedAmount',
            'TotalDeliveredAmount',
            'TotalCanceledAmount'
        ));
    }

    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    public function category_add()
    {
        return view('admin.category-add');
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'code' => 'required|unique:categories,code|size:3', // يجب أن يكون طوله 3 أحرف
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->code = strtoupper($request->code); // تحويل الكود إلى أحرف كبيرة
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been added successfully!');
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $request->id,
            'code' => 'required|unique:categories,code,' . $request->id . '|size:3', // تعديل الكود بشرط فريد
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->code = strtoupper($request->code); // تحويل الكود إلى أحرف كبيرة
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
    }

    public function category_delete($id)
    {
        $category = Category::find($id);
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
        $product = Product::orderBy('created_at', 'DESC')->first();
        $categories = Category:: select('id', 'name')->orderBy('name')->get();

        return view('admin.product-add', compact('categories','product'));
    }



    public function product_store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'stock_status' => 'required|in:active,inactive',
            'description' => 'nullable',
'category_id' => 'required',

            'featured' => 'nullable|boolean',
            'specifications.*.name' => 'required|string',
            'specifications.*.title' => 'nullable|string',
            'specifications.*.paragraphs' => 'nullable',
            'specifications.*.images' => 'array', // تحقق من أن الصور مصفوفة
        ]);


        // إنشاء المنتج
        $product = new Product();
        $product->name = $request->name;
        $product->slug = $this->generateReferenceCode($request);
        $product->description = $request->description ?? 'No description available.';
        $product->category_id = $request->category_id;

        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured ?? false;
        $product->adding_date = Carbon::now();
        $product->save();

        // حفظ المواصفات
        if ($request->has('specifications')) {
            foreach ($request->specifications as $spec) {
                $specification = new ProductSpecification();
                $specification->product_id = $product->id;
                $specification->name = $spec['name'];
                $specification->title = $spec['title'] ?? null;

                // حفظ الجمل الموصوفة
                if (isset($spec['paragraphs'])) {
                    $specification->paragraphs = json_encode($spec['paragraphs']);
                }

                // حفظ الصور
                $images = [];
                if (isset($spec['images'])) {
                    foreach ($spec['images'] as $image) {
                        $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('uploads/products/specifications'), $imageName);
                        $images[] = $imageName;
                    }
                }
                $specification->images = json_encode($images);
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
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName, 100);
        $img->resize(104, 104, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail . '/' . $imageName, 100);
    }
    public function product_edit($id)
    {
        $product = Product::find($id);
        $specifications = ProductSpecification::where('product_id', $id)->get();

        return view('admin.product-edit', compact('product', 'specifications'));
    }

    public function product_update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock_status' => 'required|in:active,inactive',
            'description' => 'nullable',
            'category_id' => 'required',

            'featured' => 'nullable|boolean',
            'specifications.*.id' => 'nullable|integer|exists:product_specifications,id',
            'specifications.*.name' => 'required|string|max:255',
            'specifications.*.title' => 'nullable|string|max:255',
            'specifications.*.paragraphs' => 'nullable',
            'specifications.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // تحديث بيانات المنتج
        $product = Product::findOrFail($request->id);
        $product->name = $request->name;
        $product->slug = $this->generateReferenceCode($request);
        $product->description = $request->description ?? 'No description available.';
        $product->category_id = $request->category_id;

        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured ?? false;
        $product->save();

        // المواصفات الجديدة والمحدثة
        $updatedSpecIds = [];
        if ($request->has('specifications')) {
            foreach ($request->specifications as $spec) {
                if (isset($spec['id']) && $spec['id']) {
                    $specification = ProductSpecification::findOrFail($spec['id']);
                } else {
                    $specification = new ProductSpecification();
                    $specification->product_id = $product->id;
                }

                // تحديث الحقول
                $specification->name = $spec['name'];
                $specification->title = $spec['title'] ?? null;
                $specification->paragraphs = isset($spec['paragraphs']) ? $spec['paragraphs'] : null;

                // إدارة الصور
                $images = $specification->images ? json_decode($specification->images, true) : [];
                if (isset($spec['images'])) {
                    foreach ($spec['images'] as $image) {
                        if ($image instanceof \Illuminate\Http\UploadedFile) {
                            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('uploads/products/specifications'), $imageName);
                            $images[] = $imageName;
                        }
                    }
                }
                $specification->images = json_encode($images);

                $specification->save();
                $updatedSpecIds[] = $specification->id;
            }
        }

        // حذف المواصفات المحذوفة
        ProductSpecification::where('product_id', $product->id)
            ->whereNotIn('id', $updatedSpecIds)
            ->delete();

        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }


    public function product_delete($id)
    {
        $product = Product::find($id);
        if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
            File::delete(public_path('uploads/products') . '/' . $product->image);
        }
        if (File::exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
            File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
        }
        foreach (explode(',', $product->images) as $ofile) {
            if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                File::delete(public_path('uploads/products') . '/' . $ofile);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $ofile)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
            }
        }
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully!');
    }


    public function orders()
    {
        $orders = Order::orderBy('created_at', 'DESC')->paginate(12); // جلب البيانات مع التصفح
        $categories = Category::orderBy('name', 'ASC')->get();

        return view('admin.orders', compact('orders')); // تمرير المتغير إلى الـ View
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'note' => 'nullable|string',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->note = $request->note;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Note updated successfully.']);
    }



    public function order_details($order_id)
    {
        $order = Order::find($order_id);

        $orderItems = OrderItem::where('order_id', $order_id)
            ->orderBy('id')
            ->paginate(12);


        return view('admin.order-details', compact('order', 'orderItems'));
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
        $img->cover(400, 690, "top");
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
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
                File::delete(public_path('uploads/slides') . '/' . $slide->image);
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

        if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
            File::delete(public_path('uploads/slides') . '/' . $slide->image);
        }

        $slide->delete();

        return redirect()->route('admin.slides')->with("status", "Slide deleted successfully!");
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


    private function generateReferenceCode(Request $request)
    {
        // الحصول على تاريخ اليوم
        $date = Carbon::now()->format('ymd');
    
        // تحديد كود نوع المنتج بناءً على الاسم أو معايير أخرى
        //$productType = $this->getProductCodeByName($request->name);
    
        // الحصول على كود الفئة من قاعدة البيانات بناءً على الـ category_id
        $categoryCode = Category::find($request->category_id)->code;
    
        // الحصول على رقم الموظف
        $employeeId = str_pad(Auth::user()->id, 3, '0', STR_PAD_LEFT);
    
        // تحديد الرقم التسلسلي
        $sequence = Product::whereDate('created_at', Carbon::today())->count() + 1;
        $sequenceFormatted = str_pad($sequence, 3, '0', STR_PAD_LEFT);
    
        // صياغة الريفرنس كود الأساسي ليشمل كود الفئة
        $baseReferenceCode = "{$date}-{$categoryCode}-{$employeeId}-{$sequenceFormatted}";
        $referenceCode = $baseReferenceCode;
    
        $counter = 1;
    
        // التأكد من أن الكود فريد
        while (Product::where('slug', $referenceCode)->exists()) {
            $referenceCode = "{$baseReferenceCode}-{$counter}";
            $counter++;
        }
    
        return $referenceCode;
    }
    

    // دالة جديدة للحصول على كود نوع المنتج بناءً على الاسم
  




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
