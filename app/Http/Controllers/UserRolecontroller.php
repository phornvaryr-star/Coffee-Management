<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserRolecontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|array'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->roles()->sync($request->role_id);

        return response()->json([
            'message' => 'Roles assigned successfully'
        ]);
    }
    public function getUserRoles($userId)
    {
        $user = User::with('roles')->findOrFail($userId);

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function removeRole(string $id)
    {
        $userRole = UserRole::find($id);
        if (!$userRole) {
            return response()->json([
                'status' => false,
                'message' => 'User role not found',
            ], 404);
        }
        $userRole->delete();

        return response()->json([
            'status' => true,
            'message' => 'User role deleted successfully',
        ]);
    }
}
