<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',        // إضافة هنا
        'order_id',       // إذا كان لديك عمود آخر
        'mode',
        'status',
    ];
    use HasFactory;
            public function order()
            {
                return $this->belongsTo(Order::class);  
            }
}
