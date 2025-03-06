<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Str;
class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(10);
        return response()->json($blogs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'short_description' => 'required',
            'image' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $blog = Blog::create([
            'title' => $request->title,
            'content' => $request->content,
            'short_description' => $request->short_description,
            'slug' => Str::slug($request->title),
            'image' => $request->image,
            'user_id' => $request->user_id,
        ]);

        return response()->json($blog, 201);
    }

    public function show(Blog $blog)
    {
        return response()->json($blog);
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'short_description' => 'required',
            'image' => 'nullable|string',
        ]);

        $blog->update([
            'title' => $request->title,
            'content' => $request->content,
            'short_description' => $request->short_description,
            'slug' => Str::slug($request->title),
            'image' => $request->image,
        ]);

        return response()->json($blog);
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return response()->json(['message' => 'Blog deleted successfully']);
    }
}
