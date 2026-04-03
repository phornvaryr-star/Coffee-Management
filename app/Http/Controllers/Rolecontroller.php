<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Resources\BaseCollection;
use Illuminate\Http\Request;

class Rolecontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::query();
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        $roles = $query->get();
        return (new BaseCollection($roles, RoleResource::class))->setMessage("Roles retrieved successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
        ]);

        if (Role::where('name', $request->name)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Role already exists',
            ], 400);
        }
        $role = Role::create([
            'name' => $request->name,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Role created successfully',
            'data' => new RoleResource($role),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Role retrieved successfully',
            'data' => new RoleResource($role),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,id,' . $role->id,
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Role updated successfully',
            'data' => new RoleResource($role),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found',
            ], 404);
        }
        $role->delete();

        return response()->json([
            'status' => true,
            'message' => 'Role deleted successfully',
        ]);
    }
}
