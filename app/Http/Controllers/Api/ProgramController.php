<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Services\ContentCaps;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        return Program::ordered()->get();
    }

    public function store(Request $request)
    {
        if (! ContentCaps::canAddCard()) {
            return response()->json(['error' => 'Card limit reached (25 across gallery/programs/leaders/blog).'], 422);
        }
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'stat_number' => 'required|string|max:50',
            'stat_label' => 'required|string|max:100',
            'image_path' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        return response()->json(Program::create($data), 201);
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'stat_number' => 'sometimes|string|max:50',
            'stat_label' => 'sometimes|string|max:100',
            'image_path' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        $program->update($data);
        return response()->json($program);
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return response()->json(['ok' => true]);
    }
}
