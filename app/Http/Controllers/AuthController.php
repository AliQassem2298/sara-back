<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Traits\JsonResponse;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use JsonResponse;
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login','register']]);
    // }
    public function login(Request $request)
    {
        try {     
            $validate = Validator::make($request->only(['email', 'password']), [
                'email' => 'required|string|email|ends_with:gmail.com,yahoo.com',
                'password' => 'required|string',
            ]);
            if ($validate->fails()) {
                return response()->json(['data' => $validate->errors(), 'message' => 'incorrect info was entered or missing info', 'status' => 400], 400);
            }
            $credentials = $request->only('email', 'password');

            $token = Auth::attempt($credentials);
            if (!$token) {

                return response()->json([
                    'status' => 400,
                    'message' => 'Unauthorized',
                ],400);
            }
            $location = User::with('location')->find(auth()->user()->id);

            $user = Auth::user();
            return response()->json([
                'message' => 'success',
                'user' => $user,
                'location' => $location['location'][0]['address'],
                'token' => $token,
            ]);
        } catch (Exception $exception) {
            return response()->json(['data' => $exception->getMessage(), 'message' => 'an exception occured', 'status' => 400],400);
        }
    }

    public function register(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'user_name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|digits:10|unique:users',
                'location' => 'required|string|max:255',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required|string|min:6',
            ]);

            if ($validate->fails()) {
                return response()->json(['data' => $validate->errors(), 'message' => 'Incorrect info was entered or missing info', 'status' => 400],400);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user_name' => $request->user_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $location = Location::create([
                'user_id' => $user->id,
                'address' => $request->location,
            ]);

            Auth::login($user);
            $token = Auth::attempt($request->only('email', 'password'));
            $user['token'] = $token;

            $user['location'] = $location ? $location->address : null;

            return response()->json(['user' => $user, 'message' => 'User created successfully', 'status' => 200],200);
        } catch (Exception $exception) {
            return response()->json(['data' => $exception->getMessage(), 'message' => 'An exception occurred', 'status' => 400],400);
        }
    }



    public function logout()
    {
        try {
            Auth::logout();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        } catch (Exception $exception) {
            return response()->json(['data' => $exception->getMessage(), 'message' => 'an exception occured', 'status' => 400],400);
        }
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function Profile()
    {
        try {
            $user = Auth::user();
            $addresses = $user->location->pluck('address')->implode(', ');
            $user->address = $addresses;
            
            // الحصول على آخر صورة مرفوعة من قبل المستخدم
            $latestImage = $user->image()->latest()->first();
            $user->image_url = $latestImage ? $latestImage->image_url : null;
    
            unset($user->location);
            return $this->jsonResponse($user, 'Profile retrieved successfully', 200);
        } catch (Exception $exception) {
            return $this->jsonResponse('An exception occurred', $exception->getMessage(), 400);
        }
    }
        



    public function update_profile(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return $this->jsonResponse('User not authenticated', 'User not found', 400);
            }
            $update = [];
            if ($request->has('email') && $request->input('email') !== $user->email) {
                if (User::where('email', $request->input('email'))->exists()) {
                    return $this->jsonResponse('Email already exists', 400);
                }
                $update['email'] = $request->input('email');
            }
            if ($request->has('phone') && $request->input('phone') !== $user->phone) {
                if (User::where('phone', $request->input('phone'))->exists()) {
                    return $this->jsonResponse('Phone number already exists', 400);
                }
                $update['phone'] = $request->input('phone');
            }
            if ($request->has('user_name') && $request->input('user_name') !== $user->user_name) {
                if (User::where('user_name', $request->input('user_name'))->exists()) {
                    return $this->jsonResponse('Username already exists', 400);
                }
                $update['user_name'] = $request->input('user_name');
            }
            if ($request->has('first_name')) {
                $update['first_name'] = $request->input('first_name');
            }
            if ($request->has('last_name')) {
                $update['last_name'] = $request->input('last_name');
            }

            if (count($update) > 0) {
                $user->update($update); // <--- Here's the fix! Pass the $update array
                return $this->jsonResponse($user, 'Profile updated successfully', 200);
            } else {
                return $this->jsonResponse('Nothing to update', 400); //Slight wording change
            }
        } catch (Exception $exception) {
            return $this->jsonResponse('An exception occurred', $exception->getMessage(), 500); //Use 500 for server errors
        }
    }
}

 