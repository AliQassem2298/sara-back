<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\Store;


class ProductController extends Controller
{
    

    public function getAllProduct(){
        return response()->json( Product::with(['image','stores:id'])->get(),200);
    }

    public function sershProduct(Request $request)
    {
        try {
            $query = Product::query();
            if ($request->has('name')) {
                $query->where('product_name', 'like', '%' . $request->query('name') . '%');
            }
    
            // جلب البيانات مع العلاقات
            $products = $query->with(['image', 'stores:id'])->get();
    
            // تعديل شكل الـ response
            $modifiedProducts = $products->map(function ($product) {
                // الحصول على الـ amount من أول store (إذا كان موجودًا)
                $amount = $product->stores->isNotEmpty() ? $product->stores->first()->pivot->amount : 0;
    
                return [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'amount' => $amount, // إضافة الـ amount هنا
                    'image_url' => $product->image->isNotEmpty() ? $product->image->first()->image_url : null,
                ];
            });
    
            return response()->json($modifiedProducts, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function createProduct(Request $request)
    {
        try {
            $product = Product::create($request->all());
            $image = $product->image()->create([
                'image_url' =>  $request->file('image_url')->store('/images', 'public')
            ]);
            $image_url=$image->image_url;

            return response()->json([
                'maessage' => 'producte created successfully',
                'product' => $product,
                'image' => $image_url
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function showProduct(Request $request, $id)
    {
        try {
            $product = Product::with(['stores.image', 'image'])->findOrFail($id);

            $responseData = [
                'product_name' => $product->product_name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $product->image,
            ];
           // اذا دخلنا للمنتج عن طريق متجر
         
                $responseData['stores'] = $product->stores->map(function ($store) {
                    return [
                        'id'=> $store->id,
                        'store_name' => $store->store_name,
                        'image' => $store->image()->latest()->first(),
                        'amount' => $store->pivot->amount
                    ];
                });
            

            return response()->json($responseData,200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(null, 201);
    }
}
