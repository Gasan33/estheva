<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BlogRequest;
use App\Http\Resources\Api\V1\BlogResource;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Str;
class BlogController extends Controller
{
    public function index()
    {
        return $this->api()->success(BlogResource::collection(Blog::latest()->paginate(10)));
    }

    public function store(BlogRequest $request)
    {
        $blog = Blog::create([
            'title' => $request->title,
            'content' => $request->content,
            'short_description' => $request->short_description,
            'slug' => Str::slug($request->title),
            'image' => $request->image,
            'user_id' => $request->user_id,
        ]);

        return $this->api()->created(new BlogResource($blog), "Blog created successfully");
    }

    public function show(Blog $blog)
    {
        return $this->api()->success(new BlogResource($blog));
    }

    public function update(BlogRequest $request, Blog $blog)
    {
        $blog->update([
            'title' => $request->title,
            'content' => $request->content,
            'short_description' => $request->short_description,
            'slug' => Str::slug($request->title),
            'image' => $request->image,
        ]);

        return $this->api()->success(new BlogResource($blog), "Blog Updated Successfully");
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return $this->api()->success([], 'Blog deleted successfully');
    }
}
