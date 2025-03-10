<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    public function index()
    {
        return $this->api()->success(FaqResource::collection(Faq::where('is_active', true)->orderBy('order')->get()));
    }

    public function show(Faq $faq)
    {
        return $this->api()->success(new FaqResource($faq));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:faqs,title',
            'answer' => 'required|string',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer|min:0'
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        $faq = Faq::create($validated);

        return $this->api()->created(new FaqResource($faq), "Faq Created Successfully");
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255|unique:faqs,title,' . $faq->id,
            'answer' => 'sometimes|string',
            'content' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0'
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $faq->update($validated);

        return $this->api()->success(new FaqResource($faq), "Faq Updated Successfully");
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return $this->api()->success(['message' => 'FAQ deleted successfully'], 200);
    }
}
