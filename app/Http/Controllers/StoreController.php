<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Http;
use App\Models\Location;
use App\Models\Image;


class StoreController extends Controller
{
    public function getAllStore()
    {
        $stores = Store::with(['image' => function ($query) {
            $query->latest();
        }])->select('id', 'store_name')
            ->get()
            ->map(function ($store) {
                // Directly assign the image URL without creating a new object.
                $store->image_url = $store->image->first()->image_url ?? null; //Handle cases where image might be null
    
                // Remove the now-unnecessary image relationship
                unset($store->image); 
                return $store;
            });
    
        return response()->json($stores, 200);
    }

    // Sersh Store
    public function sershStore(Request $request)
    {
        try {
            $query = Store::query();
            if ($request->has('name')) {
                $query->where('store_name', 'like', '%' .  $request->query('name') . '%');
            }
    
            // جلب البيانات مع العلاقة
            $stores = $query->with(['image'])->get();
    
            // تعديل شكل الـ response
            $modifiedStores = $stores->map(function ($store) {
                return [
                    'id' => $store->id,
                    'store_name' => $store->store_name,
                    'store_type' => $store->store_type,
                    'address' => $store->location->first()->address,
                    'image_url' => $store->image->isNotEmpty() ? $store->image->first()->image_url : null,
                ];
            });
    
            return response()->json($modifiedStores, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }
    // Create a new Store
    public function createStore(Request $request)
    {
        try {


            $store = Store::create([
                'store_name' => $request->store_name,
                'store_type' => $request->store_type
            ]);
            
            $image = $store->image()->create([
                'image_url' => $request->file('image_url')-> store('/images','public')
            ]);
         $image_url=   $image->first()->image_url;
            $location = $store->location()->create([
                'address' => $request->address
            ]);
            $address = $location->address;
            
            return response()->json([
                'message' => 'store created successfully',
                'store' => $store,
                'image' => $image_url,
                'location' => $address
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }
    public function showStore($id)
{
    try {
        $store = Store::with(['products.image', 'image', 'location'])->findOrFail($id);

        return response()->json([
            'store_name' => $store->store_name,
            'store_type' => $store->store_type,
            'image_url' => $store->image->first()->image_url ?? null, // الحصول على أول صورة
            'address' => $store->location->first()->address ?? null, // الحصول على أول عنوان
            'products' => $store->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'amount' => $product->pivot->amount,
                    'image_url' => $product->image->first()->image_url ?? null, // الحصول على أول صورة للمنتج
                ];
            }),
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => 'error',
            'message' => $th->getMessage(),
        ]);
    }
}



    public function destroy($id)
    {
        Store::destroy($id);
        return response()->json(null, 201);
    }

    public function addProductToStore(Request $request, $id)
    {
        try {
            $store = Store::findOrFail($id);

            $product = $store->products()->where('product_id', $request->product_id)->first();

            if ($product) {
                // إذا كان المنتج موجودًا، قم بتحديث الكمية
                $newAmount = $product->pivot->amount + $request->amount;
                $store->products()->updateExistingPivot($request->product_id, ['amount' => $newAmount]);
            } else {
                // إذا لم يكن المنتج موجودًا ضع المنتج بالمتجر
                $store->products()->attach($request->product_id, ['amount' => $request->amount]);
            }
            $products = $store->products;
            return response()->json(['product' => $products], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
