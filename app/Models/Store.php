<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
use App\Models\Product;
use App\Models\Amount;

class Store extends Model
{
    use HasFactory;
       protected $fillable = ['store_name', 'store_type','address'];

       protected $hidden = [
        'created_at',
        'updated_at'
    ];
    
    public function image()
    {
        return $this->hasMany(Image::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'amounts')->withPivot('amount');
    }

    public function amount()
    {
        return $this->belongsTo(Amount::class);
    }

     public function location()
     {
         return $this->hasMany(Location::class);
     }
}
