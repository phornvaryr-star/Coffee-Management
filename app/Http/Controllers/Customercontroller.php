<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\BaseCollection;
class Customercontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        $pageSize = $request->get('pageSize', 10);
        $customers = $query->paginate($pageSize);
        return (new BaseCollection($customers))->setMessage("Customer list");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);
        $customer = Customer::create([
            'name' => $request->name ?? 'Customer',
            'phone' => $request->phone ?? null,
            'description' => $request->description,
        ]);
        return response()->json([
            'data' => new CustomerResource($customer),
            'message' => 'Customer created successfully',
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'description' => $request->description,
        ]);
        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }
        $customer->delete();
        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }
}
