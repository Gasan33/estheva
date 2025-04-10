<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BlogRequest;
use App\Http\Resources\Api\V1\BlogResource;
use Illuminate\Http\Request;
use App\Models\Blog;
use Exception;
use Illuminate\Support\Str;
class BlogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = 10; // Or set this to a dynamic value
            $blogs = Blog::latest()->paginate($perPage);

            return $this->api()->success([
                'data' => BlogResource::collection($blogs),
                'pagination' => [
                    'current_page' => $blogs->currentPage(),
                    'next_page_url' => $blogs->nextPageUrl(),
                ]
            ]);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }


    public function store(BlogRequest $request)
    {
        try {
            $blog = Blog::create([
                'title' => $request->title,
                'content' => $request->content,
                'short_description' => $request->short_description,
                'slug' => Str::slug($request->title),
                'image' => $request->image,
                'user_id' => $request->user_id,
            ]);

            return $this->api()->created(new BlogResource($blog), "Blog created successfully");
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function show($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            return $this->api()->success(new BlogResource($blog));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function getBlogBySlug($slug)
    {
        try {
            $blog = Blog::where('slug', $slug)->first();

            if (!$blog) {
                return $this->api()->notFound("Blog not found.");
            }

            return response()->json(new BlogResource($blog));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function update(BlogRequest $request, Blog $blog)
    {
        try {
            $blog->update([
                'title' => $request->title,
                'content' => $request->content,
                'short_description' => $request->short_description,
                'slug' => Str::slug($request->title),
                'image' => $request->image,
            ]);

            return $this->api()->success(new BlogResource($blog), "Blog Updated Successfully");
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function destroy(Blog $blog)
    {
        try {
            $blog->delete();
            return $this->api()->success([], 'Blog deleted successfully');
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }
}
