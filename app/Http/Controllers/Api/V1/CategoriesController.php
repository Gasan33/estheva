<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    /**
     * Get all categories.
     */
    public function index(Request $request)
    {
        try {
            $categories = Category::with('image')->get();
            return $this->api()->success(CategoryResource::collection($categories));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        try {
            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'visibility' => true,
            ]);

            if ($request->has('image')) {
                $category->image()->create([
                    'path' => $request->image,
                ]);
            }

            return $this->api()->created(new CategoryResource($category), "Category created successfully.");
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Show a category by ID.
     */
    public function show($id)
    {
        try {
            $category = Category::with('image')->find($id);
            if (!$category) {
                return $this->api()->notFound("Category not found.");
            }

            return $this->api()->success(new CategoryResource($category));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Get a category by slug.
     */
    public function getCategoryBySlug($slug)
    {
        try {
            $category = Category::with('image')->where('slug', $slug)->first();

            if (!$category) {
                return $this->api()->notFound("Category not found.");
            }

            return response()->json(new CategoryResource($category));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Update a category.
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::with('image')->find($id);
            if (!$category) {
                return $this->api()->notFound("Category not found.");
            }

            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'visibility' => true,
            ]);

            if ($request->has('image')) {
                if ($category->image) {
                    $category->image->update(['path' => $request->image]);
                } else {
                    $category->image()->create(['path' => $request->image]);
                }
            }

            return $this->api()->success(new CategoryResource($category), "Category updated successfully.");
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Delete a category.
     */
    public function destroy($id)
    {
        try {
            $category = Category::with('image')->find($id);

            if (!$category) {
                return $this->api()->notFound("Category not found.");
            }

            $category->delete();
            return $this->api()->success([], 'Category deleted successfully.');
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
