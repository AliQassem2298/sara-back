<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Product;

class OrderDetails extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'order_id', 'quantity'];
    protected $hidden = [
        'updated_at',
        'created_at',
    ];
    
    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
