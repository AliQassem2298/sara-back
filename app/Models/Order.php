<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderDetaile;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['order_date', 'total_price', 'status'];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->belongsTo(OrderDetails::class);
    }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class, 'orderDetails');
    // }
    public function products()
{
    return $this->belongsToMany(Product::class, 'order_details')->withPivot('quantity_want');
}
}
