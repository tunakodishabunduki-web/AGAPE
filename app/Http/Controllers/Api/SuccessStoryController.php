<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\Request;

class SuccessStoryController extends Controller
{
    // Not part of the shared 25-card pool (it wasn't in the old cap either) —
    // stories are capped separately, small, since only 3 show on the homepage at once.
    private const MAX = 12;

    public function index()
    {
        return SuccessStory::ordered()->get();
    }

    public function store(Request $request)
    {
        if (SuccessStory::count() >= self::MAX) {
            return response()->json(['error' => 'Story limit reached ('.self::MAX.').'], 422);
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'age_label' => 'required|string|max:100',
            'quote' => 'required|string',
            'image_path' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        return response()->json(SuccessStory::create($data), 201);
    }

    public function update(Request $request, SuccessStory $successStory)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'age_label' => 'sometimes|string|max:100',
            'quote' => 'sometimes|string',
            'image_path' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        $successStory->update($data);
        return response()->json($successStory);
    }

    public function destroy(SuccessStory $successStory)
    {
        $successStory->delete();
        return response()->json(['ok' => true]);
    }
}
