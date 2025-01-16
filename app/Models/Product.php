<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Image;
use App\Models\OrderDetaile;
use App\Models\Store;
use App\Models\Amount;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['product_name', 'price', 'description','quantity'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ]; 

    public function orderDetails()
    {
        return $this->belongsTo(OrderDetails::class);
    }
    public function order()
    {
        return $this->belongsToMany(Order::class, 'orderDetails');
    }

    public function image()
    {
        return $this->hasMany(Image::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'amounts')->withPivot('amount');
    }

    public function amount()
    {
        return $this->belongsTo(Amount::class);
    }

    /// now
    public function orders()
{
    return $this->belongsToMany(Order::class, 'order_details')->withPivot('quantity_want');
}

}
