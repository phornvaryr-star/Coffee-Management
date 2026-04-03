<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use App\Http\Resources\BaseCollection;
class Authcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('profile');
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $pageSize = $request->get('pageSize', 10);
        $user = $query->paginate($pageSize);
        return (new BaseCollection($user))->setMessage("User list");
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
            'phone' => 'nullable',
            'address' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|in:active,inactive'
        ]);
        if($request->password !== $request->confirm_password){
            return response()->json(['error' => 'Password and confirm password do not match'], 400);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status ?? 'active',
        ]);
        $token = JWTAuth::fromUser($user);
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $request->file('image')->store('profiles', 'public');
        }
        Profile::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $imageUrl
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'user' => new UserResource($user->load('profile')),
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
            'user' => new UserResource($user->load('profile'))
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password',
            'phone' => 'nullable',
            'address' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|in:active,inactive'
        ]);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status ?? 'active'
        ]);
        if ($request->hasFile('image')) {
            $profile = Profile::where('user_id', $user->id)->first();
            if ($profile && $profile->image) {
                \Storage::disk('public')->delete($profile->image);
            }
            $imageUrl = $request->file('image')->store('profiles', 'public');
            if ($profile) {
                $profile->update(['image' => $imageUrl]);
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'user' => new UserResource($user->load('profile'))
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
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            "access_token" => $token,
            'expires_at' => $expires_at,
            'user' => new UserResource($user->load('profile')),
        ]);
    }
    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['status' => true, 'message' => 'Successfully logged out']);
    }
}
