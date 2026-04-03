<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\UserResource;
use App\Http\Resources\BaseCollection;
class Authcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::paginate(10);
        return (new BaseCollection($user))
            ->setMessage('Users retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password',
        ]);
        if($request->password !== $request->confirm_password){
            return response()->json(['error' => 'Password and confirm password do not match'], 400);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'user' => new UserResource($user)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'User retrieved successfully',
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
        ]);
        $user->update($request->only(['name', 'email']));
        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['status' => true, 'message' => 'User deleted successfully']);
    }
    public function login(Request $request)
    {
        $request->validate([
           'email' => 'required|String|email',
           'password' => 'required|String',
       ]);
       if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
           return response()->json(['error' => 'Unauthorized'], 401);
       }
        $user = JWTAuth::user();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if ($user->status !== 'active') {
            return response()->json(['error' => 'Account disabled'], 403);
        }
        $expiration = JWTAuth::factory()->getTTL();
        $expires_at = now()->addMinutes($expiration)->format('Y-m-d h:i:s A');
        // $user->load('profile');
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            "access_token" => $token,
            'expires_at' => $expires_at,
            'user' => new UserResource($user),
        ]);
    }
    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['status' => true, 'message' => 'Successfully logged out']);
    }
}
