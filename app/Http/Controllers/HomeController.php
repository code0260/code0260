<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Category;
use App\Models\Product;
use App\Models\Contact;

class HomeController extends Controller
{
    public function index()
{
    $slides = Slide::where('status', 1)->get()->take(3);
$fproducts = Product::where('featured', 1)->get()->take(8);
    return view('index', compact('slides','fproducts'));
}


public function search(Request $request)
{
    $query = $request->input('query');

    $results = Product::where('name', 'LIKE', "%{$query}%")->take(8)->get();

    return response()->json($results);
}

}
