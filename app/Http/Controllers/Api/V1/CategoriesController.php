<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('image')->get();

        // if ($request->wantsJson()) {
        return $this->api()->success(CategoryResource::collection($categories));

        // } else {
        //     return view('admin.category', ['data' => CategoryResource::collection($categories)]);
        // }
    }


    public function store(Request $request)
    {

        $category = Category::create([
            'name' => $request->name,
            'slug' => $request->name,
            'description' => $request->description,
            'visibility' => true,
        ]);

        $getImage = $request->image;
        $category->image()->create(['path' => $getImage]);
        return $this->api()->created(CategoryResource::collection($category), "Category Created Successfully");
    }


    public function show($id)
    {
        $category = Category::with('image')->find($id);
        if (!$category) {
            return $this->api()->notFound();
        }
        return $this->api()->success(CategoryResource::collection($category));
    }

    public function update(Request $request, $id)
    {

        $category = Category::with('image')->find($id);
        if (!$category) {
            return $this->api()->notFound();
        }
        $category->update([
            'name' => $request->name,
            'slug' => $request->name,
            'description' => $request->description,
            'visibility' => true,
        ]);

        $getImage = $request->image;
        $category->image()->updatedAt(['path' => $getImage]);


        return $this->api()->success(CategoryResource::collection($category), "category updated successfully.");
    }

    public function destroy($id)
    {

        $category = Category::with('image')->find($id);

        // dd($service);
        if ($category !== null) {
            $category->delete();
            return $this->api()->success([], 'Category deleted successfully');
        } else {
            return $this->api()->notFound('Category Not Found');
        }

    }
}

