<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Http\Resources\BaseCollection;
class Productcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('product_name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $pageSize = $request->get('pageSize', 10);
        $products = $query->paginate($pageSize);
        return (new BaseCollection($products))->setMessage("Product list");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'qty' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'status' => 'nullable|in:enable,disable',
        ]);
        if(Product::where('product_name', $request->product_name)->exists()){
            return response()->json(['error' => 'Product name already exists'], 400);
        }
        $category = Category::find($request->category_id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $request->file('image')->store('products', 'public');
        }
        $product = Product::create([
            'category_id' => $request->category_id,
            'product_name' => $request->product_name,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'qty' => $request->qty,
            'image' => $imageUrl,
            'description' => $request->description,
            'status' => $request->status ?? 'enable',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => new ProductResource($product->load('category'))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => new ProductResource($product->load('category'))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'qty' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'status' => 'nullable|in:enable,disable',
        ]);
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $category = Category::find($request->category_id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        if(Product::where('product_name', $request->product_name)->where('id', '!=', $id)->exists()){
            return response()->json(['error' => 'Product name already exists'], 400);
        }
        if ($request->hasFile('image')) {
            $product = Product::where('id', $product->id)->first();
            if ($product && $product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $imageUrl = $request->file('image')->store('products', 'public');
            if ($product) {
                $product->update(['image' => $imageUrl]);
            }
        }
        $product->update([
            'category_id' => $request->category_id,
            'product_name' => $request->product_name,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'qty' => $request->qty,
            'description' => $request->description,
            'status' => $request->status ?? $product->status,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product->load('category'))
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
