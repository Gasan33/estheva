<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\categories;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $categories = categories::with('image')->get();

        if ($request->wantsJson()) {
            return api()->success(CategoryResource::collection($categories));

        } else {
            return view('admin.category', ['data' => CategoryResource::collection($categories)]);
        }
    }


    public function store(Request $request)
    {

        $category = categories::create([
            'name' => $request->name,
            'slug' => $request->name,
            'description' => $request->description,
            'visibility' => true,
        ]);

        $getImage = $request->image;
        $category->image()->create(['path' => $getImage]);
        return api()->created($category, "Category Created Successfully");
    }


    public function show($id)
    {
        $category = categories::with('image')->find($id);
        if (!$category) {
            return api()->notFound();
        }
        return api()->success($category);
    }

    public function update(Request $request, $id)
    {

        $category = categories::with('image')->find($id);
        if (!$category) {
            return api()->notFound();
        }
        $category->update([
            'name' => $request->name,
            'slug' => $request->name,
            'description' => $request->description,
            'visibility' => true,
        ]);

        $getImage = $request->image;
        $category->image()->updatedAt(['path' => $getImage]);


        return api()->success($category, "category updated successfully.");
    }

    public function destroy($id)
    {

        $category = categories::with('image')->find($id);

        // dd($service);
        if ($category !== null) {
            $category->delete();
            return api()->success(new CategoryResource($category), 'Category deleted successfully');
        } else {
            return api()->notFound('Category Not Found');
        }

    }
}


if (!function_exists('api')) {

    function api()
    {
        return new ApiResponse();
    }
}
