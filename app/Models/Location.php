<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Location extends Model
{
    use HasFactory;
    protected $fillable = ['store_id','user_id', 'address'];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function store()
     {
         return $this->belongsTo(Store::class);
     }
}
