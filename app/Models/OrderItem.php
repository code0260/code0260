<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'order_id', 'product_name', 'price', 'quantity', 
        'custom_specifications', 'custom_image', 'additional_notes', 'options', 'rstatus'
    ];

    /**
     * The product that the order item belongs to.
     */
   /* public function product()
    {
        return $this->belongsTo(Product::class);
    }

 
    public function specifications()
    {
        return $this->hasMany(ProductOrderSpecification::class, 'order_item_id');
    }

 
    public function order()
    {
        return $this->belongsTo(Order::class);
    }*/
    public function specifications()
{
    return $this->hasMany(ProductOrderSpecification::class, 'order_item_id');
}
public function productOrderSpecifications()
{
    return $this->hasMany(ProductOrderSpecification::class, 'order_item_id');
}

public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}

public function order()
{
    return $this->belongsTo(Order::class, 'order_id');
}

}
 
