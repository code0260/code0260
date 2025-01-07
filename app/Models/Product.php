<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
  /*  public function orderSpecifications()
    {
        return $this->hasMany(ProductOrderSpecification::class, 'product_id');
    }
    
 
    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class, 'product_id');
    }
    public function productSpecifications()
{
    return $this->hasMany(ProductSpecification::class);
}

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    
    public function productOrderSpecifications()
    {
        return $this->hasMany(ProductOrderSpecification::class);
    }*/
    public function specifications()
{
    return $this->hasMany(ProductSpecification::class, 'product_id');
}

public function orderSpecifications()
{
    return $this->hasMany(ProductOrderSpecification::class, 'product_id');
}
public function category()
{
 return $this->belongsTo (Category::class, 'category_id');
}
}