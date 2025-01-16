<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;

class Image extends Model
{
    use HasFactory;
    protected $fillable = ['image_url'];

    protected $hidden = [
        'user_id',
        'product_id',
        'store_id',
        'created_at',
        'updated_at'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
