<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\CategoryResource;
// use App\Http\Resources\ResourceClass;
use Illuminate\Http\Request;

class Categorycontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request)
    {
        
        $query = Category::query();

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $pageSize = $request->get('pageSize', 10);
        $categories = $query->paginate($pageSize);
        return (new BaseCollection($categories))->setMessage("Category list");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'status' => 'nullable|in:enable,disable',
            'description' => 'nullable|string',
        ]);
        if(Category::where('category_name', $request->category_name)->exists()){
            return response()->json(['message' => 'Category name already exists'], 400);
        }
        $category = Category::create($request->all());
        return new CategoryResource($category);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'category_name' => 'string|max:255',
            'status' => 'nullable|in:enable,disable',
            'description' => 'nullable|string',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->update($request->all());
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 204);
    }
}
