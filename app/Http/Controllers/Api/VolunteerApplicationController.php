<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VolunteerApplication;
use Illuminate\Http\Request;

class VolunteerApplicationController extends Controller
{
    // POST /api/volunteer-applications — unauthenticated. This is what the
    // volunteer page's form actually submits to now; previously it submitted
    // to nothing at all.
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'area' => 'required|string|max:50',
            'availability' => 'required|string|max:50',
            'message' => 'required|string|max:3000',
        ]);
        VolunteerApplication::create($data);
        return response()->json(['ok' => true], 201);
    }

    // Admin-only from here down.
    public function index()
    {
        return VolunteerApplication::orderByDesc('created_at')->get();
    }

    public function update(Request $request, VolunteerApplication $volunteerApplication)
    {
        $data = $request->validate(['read' => 'required|boolean']);
        $volunteerApplication->update($data);
        return response()->json($volunteerApplication);
    }

    public function destroy(VolunteerApplication $volunteerApplication)
    {
        $volunteerApplication->delete();
        return response()->json(['ok' => true]);
    }
}
