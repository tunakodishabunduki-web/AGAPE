<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use App\Services\ContentCaps;
use Illuminate\Http\Request;

class LeaderController extends Controller
{
    public function index()
    {
        return Leader::ordered()->get();
    }

    public function store(Request $request)
    {
        if (! ContentCaps::canAddCard()) {
            return response()->json(['error' => 'Card limit reached (25 across gallery/programs/leaders/blog).'], 422);
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'bio' => 'required|string',
            'photo_path' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        return response()->json(Leader::create($data), 201);
    }

    public function update(Request $request, Leader $leader)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string',
            'photo_path' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        $leader->update($data);
        return response()->json($leader);
    }

    public function destroy(Leader $leader)
    {
        $leader->delete();
        return response()->json(['ok' => true]);
    }
}
