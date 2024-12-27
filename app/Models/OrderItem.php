<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    use HasFactory;

    protected $fillable = ['product_id', 'order_id', 'product_name', 'price', 'quantity', 
                           'custom_specifications', 'custom_image', 'additional_notes', 'options', 'rstatus'];

    // العلاقات
  
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productSpecifications()  // تعديل الاسم لتجنب التكرار
    {
        return $this->hasMany(ProductSpecification::class, 'product_id');
    }

    // علاقة مع ProductOrderSpecification
    public function productOrderSpecifications() 
    {
        return $this->hasMany(ProductOrderSpecification::class);
    }
}
