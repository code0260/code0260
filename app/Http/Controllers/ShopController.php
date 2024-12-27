<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $o_column = "";
        $o_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;

        // تحديد ترتيب المنتجات بناءً على الـ order
        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order = 'DESC';
                break;

            case 2:
                $o_column = 'created_at';
                $o_order = 'ASC';
                break;

            default:
                $o_column = 'id';
                $o_order = 'DESC';
        }

        // استعلام المنتجات مع تحميل المواصفات
        $products = Product::with('specifications')  // تحميل المواصفات
            ->orderBy($o_column, $o_order)
            ->paginate($size);

        return view('shop', compact('products', 'size', 'order'));
    }

    public function product_details($product_slug)
    {
        // استعلام المنتج بناءً على الـ slug مع تحميل المواصفات
        $product = Product::with('specifications')
            ->where('slug', $product_slug)
            ->first();

        // الحصول على منتجات أخرى مشابهة
        $rproducts = Product::where('slug', '<>', $product_slug)->get()->take(8);

        return view('details', compact('product', 'rproducts'));
    }
}

