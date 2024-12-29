<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    protected $fillable = [
        'user_id', // <-- Add user_id here
        'subtotal',
        'total',
        'discount',
        'tax',
        'name',
        'phone',
        'address',
        'locality',
        'city',
        'state',
        'country',
        'landmark',
        'zip',
        // Add any other fields you want to be mass assignable
    ];
    use HasFactory;
                public function user()
                {
                return $this->belongsTo (User::class);
                }
                public function address() {
                    return $this->belongsTo(Address::class);
                }
                
                public function orderItems()
                {
                return $this->hasMany (OrderItem::class);
                }
                public function transaction()
                {
                return $this->hasone (Transaction::class);
                }
                public function productSpecifications()
                {
                    return $this->hasMany(ProductOrderSpecification::class, 'order_id');  // ربط المواصفات بالطلب
                }
}
