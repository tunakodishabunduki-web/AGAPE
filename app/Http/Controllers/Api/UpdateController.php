<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteUpdate;
use App\Services\ContentCaps;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function index()
    {
        return SiteUpdate::active();
    }

    public function store(Request $request)
    {
        if (SiteUpdate::active()->count() >= ContentCaps::MAX_UPDATES) {
            return response()->json(['error' => 'Announcement limit reached ('.ContentCaps::MAX_UPDATES.' active at once).'], 422);
        }
        $data = $request->validate([
            'message' => 'required|string|max:500',
            'title' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:1000',
            'image_path' => 'nullable|string',
            'expires_at' => 'required|date|after:now',
        ]);
        return response()->json(SiteUpdate::create($data), 201);
    }

    public function destroy(SiteUpdate $siteUpdate)
    {
        $siteUpdate->delete();
        return response()->json(['ok' => true]);
    }
}
