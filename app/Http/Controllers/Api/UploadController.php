<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    // POST /api/uploads — accepts one image file from a browser <input type="file">,
    // compresses it, and returns the path to store on whichever record it belongs
    // to (gallery item, program, leader, blog post, hero background, contact photo).
    public function store(Request $request, ImageService $images)
    {
        $request->validate([
            // 8MB is generous for a phone photo pre-compression; ImageService
            // re-encodes everything to a much smaller JPEG before it's stored.
            'file' => 'required|image|max:8192',
        ]);

        $path = $images->storeCompressed($request->file('file'));

        return response()->json(['path' => $path], 201);
    }
}
