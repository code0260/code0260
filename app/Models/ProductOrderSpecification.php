<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOrderSpecification extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'title', 'paragraphs', 'images', 'order_item_id', 'product_id'];

    // العلاقات
    
 
    // علاقة مع المنتج
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
public function order()
    {
        return $this->belongsTo(Order::class);  // ربط المواصفات بالطلب
    }
}
  