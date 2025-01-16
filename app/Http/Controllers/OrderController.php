<?php

namespace App\Http\Controllers;

use App\Traits\JsonResponse;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Events\OrderCancelled;
use App\Events\OrderConfirmed;

class OrderController extends Controller
{
    use JsonResponse;

    // إضافة منتج إلى السلة
    public function add_to_cart(Request $request, $product_id)
    {
        try {
            $validat = Validator::make($request->all(), [
                'quantity_want' => 'required|integer',
            ]);
            if ($validat->fails()) {
                return $this->jsonResponse('Validation errors', $validat->errors(), 400);
            }
            $product = Product::find($product_id);
            if (!$product || $product->quantity == 0) {
                return $this->jsonResponse(null, 'Product not found or out of stock', 400);
            }

            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized user',
                ]);
            }

            $user = User::find(auth()->user()->id);
            $order = Order::where('user_id', $user->id)->where('status', 0)->first();
            if (!$order) {
                $order = $user->order()->create([
                    'user_id' => $user->id,
                    'total_price' => 0,
                    'status' => 0,
                    'order_date' => now()
                ]);
            }
            if ($request->quantity_want > $product->quantity) {
                return $this->jsonResponse(null, "Quantity more than available by: " . ($request->quantity_want - $product->quantity), 400);
            }
            $order->total_price += ($product->price * $request->quantity_want);
            $order->status = 0;
            $order->save();
            DB::table('order_details')->insert([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity_want' => $request->quantity_want,
                'created_at' => now(),
            ]);
            return $this->jsonResponse(null, 'Product added to cart successfully', 200);
        } catch (Exception $exception) {
            return $this->jsonResponse('An exception occurred', $exception->getMessage(), 400);
        }
    }

    // عرض محتويات السلة
    public function show_cart()
    {
        try {
            $user = User::find(auth()->user()->id);
            $order = Order::where('user_id', $user->id)->where('status', 0)->first();
    
            if (!$order) {
                return $this->jsonResponse([], 'There is no product in cart', 200);
            }
    
            $products = DB::table('order_details')->where('order_id', $order->id)->get();
            $productDetails = [];
    
            foreach ($products as $p) {
                $pp = Product::with('image')->find($p->product_id);
                if ($p->quantity_want > $pp->quantity) {
                    DB::table('order_details')->where('id', $p->id)->delete();
                    $order->total_price -= $pp->price * $p->quantity_want;
                    $order->save();
                } else {
                    $productDetails[] = [
                        'order_detail_id' => $p->id,
                        'product_id' => $pp->id,
                        'name' => $pp->product_name,
                        'description' => $pp->description,
                        'price' => $pp->price,
                        'quantity_want' => $p->quantity_want,
                        'image' => $pp->image->pluck('image_url')->first(),
                        'total_quantity' => $pp->quantity,
                    ];
                }
            }
    
            if (empty($productDetails)) {
                return $this->jsonResponse([], 'There is no product in cart', 200);
            }
    
            return $this->jsonResponse($productDetails, 'ok', 200);
        } catch (Exception $exception) {
            return $this->jsonResponse('An exception occurred', $exception->getMessage(), 400);
        }
    }
        // حذف منتج من السلة
    public function delete_from_cart($order_detail_id)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized user',
                ]);
            }

            $order = Order::where('user_id', $user->id)->where('status', 0)->first();
            if (!$order) {
                return $this->jsonResponse(null, 'No active order found', 400);
            }

            $orderDetail = DB::table('order_details')->where('id', $order_detail_id)->first();

            if (!$orderDetail) {
                return $this->jsonResponse(null, 'Product not found in cart', 400);
            }

            DB::table('order_details')->where('id', $order_detail_id)->delete();

            $product = Product::find($orderDetail->product_id);
            $order->total_price -= $orderDetail->quantity_want * $product->price;
            $order->save();

            return $this->jsonResponse(null, 'Product deleted successfully', 200);
        } catch (Exception $exception) {
            return $this->jsonResponse('An exception occurred', $exception->getMessage(), 400);
        }
    }

    // تحديث كمية المنتج في السلة
    public function update_cart(Request $request, $order_detail_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'new_quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return $this->jsonResponse('Validation errors', $validator->errors(), 400);
            }

            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized user',
                ]);
            }

            $order = Order::where('user_id', $user->id)->where('status', 0)->first();
            if (!$order) {
                return $this->jsonResponse(null, 'No active order found', 400);
            }

            $orderDetail = DB::table('order_details')->where('id', $order_detail_id)->first();

            if (!$orderDetail) {
                return $this->jsonResponse(null, 'Product not found in cart', 400);
            }

            $product = Product::find($orderDetail->product_id);
            if (!$product) {
                return $this->jsonResponse(null, 'Product not found', 400);
            }

            if ($request->new_quantity > $product->quantity) {
                return $this->jsonResponse(null, "Requested quantity exceeds available stock by: " . ($request->new_quantity - $product->quantity), 400);
            }

            $difference = $request->new_quantity - $orderDetail->quantity_want;
            $order->total_price += ($difference * $product->price);
            $order->save();

            DB::table('order_details')
                ->where('id', $order_detail_id)
                ->update(['quantity_want' => $request->new_quantity]);

            return $this->jsonResponse(null, 'Product quantity updated successfully', 200);
        } catch (Exception $exception) {
            return $this->jsonResponse('An exception occurred', $exception->getMessage(), 400);
        }
    }

  // تأكيد الطلب (تثبيت الطلب)
  public function confirm_order()
  {
      try {
          $user = auth()->user();
          if (!$user) {
              return response()->json([
                  'status' => 401,
                  'message' => 'Unauthorized user',
              ]);
          }
  
          $order = Order::where('user_id', $user->id)->where('status', 0)->first();
          if (!$order) {
              return $this->jsonResponse(null, 'No active order found in cart', 400);
          }
  
          $order->status = 1;
          $order->save();
  
          // تشغيل الحدث OrderConfirmed
          event(new OrderConfirmed($order));
  
          return $this->jsonResponse($order->status, 'Order confirmed successfully', 200);
      } catch (Exception $exception) {
          return $this->jsonResponse('An exception occurred', $exception->getMessage(), 400);
      }
  }    // عرض جميع الطلبات للمستخدم
    public function get_orders()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized user',
                ]);
            }

            $orders = Order::where('user_id', $user->id)->where('status', '!=', 0)->get();

            return $this->jsonResponse($orders, 'Orders retrieved successfully', 200);
        } catch (Exception $exception) {
            return $this->jsonResponse('An exception occurred', $exception->getMessage(), 400);
        }
    }

    // إلغاء الطلب
    public function cancel_order($order_id)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized user',
                ]);
            }
    
            $order = Order::where('id', $order_id)->where('user_id', $user->id)->first();
            if (!$order) {
                return $this->jsonResponse(null, 'Order not found', 400);
            }
    
            if ($order->status == 2) {
                return $this->jsonResponse(null, 'Order is already cancelled', 400);
            }
    
            $order->status = 2;
            $order->save();
    
            // تشغيل الحدث OrderCancelled
            event(new OrderCancelled($order));
    
            return $this->jsonResponse(null, 'Order cancelled successfully', 200);
        } catch (Exception $exception) {
            return $this->jsonResponse('An exception occurred', $exception->getMessage(), 400);
        }
    }
}