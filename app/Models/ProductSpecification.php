<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpecification extends Model
{
    protected $fillable = ['product_id', 'description', 'image'];

    // علاقة مع المنتج
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
