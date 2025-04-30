<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFaqRequest;
use App\Http\Requests\Api\V1\UpdateFaqRequest;
use App\Http\Resources\Api\V1\FaqResource;
use App\Models\Faq;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    public function index()
    {
        try {
            $perPage = 10;
            $faqs = Faq::latest()->paginate($perPage);

            return $this->api()->success([
                'data' => FaqResource::collection($faqs),
                'pagination' => [
                    'current_page' => $faqs->currentPage(),
                    'next_page_url' => $faqs->nextPageUrl(),
                ]
            ]);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function show($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            return $this->api()->success(new FaqResource($faq));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function store(StoreFaqRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['slug'] = Str::slug($validated['title']);

            $faq = Faq::create($validated);

            return $this->api()->created(new FaqResource($faq), "Faq Created Successfully");
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function update(UpdateFaqRequest $request, $id)
    {
        try {

            $faq = Faq::findOrFail($id);
            $validated = $request->validated();

            if (isset($validated['title'])) {
                $validated['slug'] = Str::slug($validated['title']);
            }

            $faq->update($validated);

            return $this->api()->success(new FaqResource($faq), "Faq Updated Successfully");
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }


    public function destroy($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faq->delete();
            return $this->api()->success([], 'Faq deleted successfully');
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }
}
