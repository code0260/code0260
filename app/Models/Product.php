<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
   
    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class, 'product_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    
    public function productOrderSpecifications()
    {
        return $this->hasMany(ProductOrderSpecification::class);
    }
}