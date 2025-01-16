<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Seeder\StoreSedeer;

class LocationController extends Controller
{
    use JsonResponse;
    public function add_Location(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'address' => 'required|string|max:255'
            ]);

            if ($validate->fails()) {
                return response()->json(['data' => $validate->errors(), 'message' => 'Incorrect information', 'status' => 400], 400);
            }

            $user = Auth::user();

            $location = Location::updateOrCreate(
                ['user_id' => $user->id],
                ['address' => $request->address]
            );
            $address = $location['address'];
            return response()->json(['data' => $address, 'message' => 'Location updated successfully', 'status' => 200]);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'status' => 400], 400);
        }
    }
}
