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
    $categories = Category:: orderBy('name')->get();
    $sproducts = Product::whereNotNull('sale_price')->where('sale_price','<>','')->inRandomOrder()->get()->take(8);
$fproducts = Product::where('featured', 1)->get()->take(8);
    return view('index', compact('slides','categories','sproducts','fproducts'));
}

public function contact()
{
  return view('contact')  ;
}

public function contact_store(Request $request)
{
    $request->validate([
        'name' => 'required|max:100', // إزالة المسافة غير الصحيحة بعد ":"
        'email' => 'required|email', // تصحيح صيغة التحقق
        'phone' => 'required|numeric|digits:10', // فصل القواعد باستخدام "|"
        'comment' => 'required'
    ]);

    $contact = new Contact();
    $contact->name = $request->name;
    $contact->email = $request->email;
    $contact->phone = $request->phone;
    $contact->comment = $request->comment;

    $contact->save();

    return redirect()->back()->with('success', 'Your message has been sent successfully');
}
public function search(Request $request)
{
    $query = $request->input('query');

    $results = Product::where('name', 'LIKE', "%{$query}%")->take(8)->get();

    return response()->json($results);
}

}
