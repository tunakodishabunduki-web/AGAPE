<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Services\ContentCaps;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        return GalleryItem::ordered()->get();
    }

    public function store(Request $request)
    {
        if (! ContentCaps::canAddCard()) {
            return response()->json(['error' => 'Card limit reached (25 across gallery/programs/leaders/blog). Remove one before adding another.'], 422);
        }
        $data = $request->validate([
            'image_path' => 'required|string',
            'alt_text' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'caption' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        return response()->json(GalleryItem::create($data), 201);
    }

    public function update(Request $request, GalleryItem $galleryItem)
    {
        $data = $request->validate([
            'image_path' => 'sometimes|string',
            'alt_text' => 'nullable|string|max:255',
            'title' => 'sometimes|string|max:255',
            'caption' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        $galleryItem->update($data);
        return response()->json($galleryItem);
    }

    public function destroy(GalleryItem $galleryItem)
    {
        $galleryItem->delete();
        return response()->json(['ok' => true]);
    }
}
