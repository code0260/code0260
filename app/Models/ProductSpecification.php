<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSpecification extends Model
{
    use HasFactory;
    protected $table = 'product_specifications';

    protected $fillable = ['name', 'title', 'paragraphs', 'images', 'product_id'];

    // العلاقة
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
   