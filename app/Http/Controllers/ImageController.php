<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Image;

class ImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        try{
        $request->validate([
            'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $user=Auth::user()->id;
        $users = User::find($user);
        
        $image = $users->image()->create([
            'image_url' =>  $request->file('image_url')->store('/images', 'public')
        ]);

        return response()->json([
            'status'=> true,
            'message'=> 'image upload seccessfuly',
            'user'=>  $user=Auth::user(),
            'image'=>  $image
        ],200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => 'error',
            'message' => $th->getMessage(),
        ], 500);
    }
    }
    
    public function getuserimage()
    {
        $user=Auth::user()->id;
        $image = Image::findOrFail($user)->latest()->first();

        return response()->json([
            'image'=> $image
        ]);
    }
   
}
