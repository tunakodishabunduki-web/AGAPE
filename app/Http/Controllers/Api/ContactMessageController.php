<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    // POST /api/messages — unauthenticated. This is the exact path
    // AFF.sendMessage() in site-data.js already posts to; it existed on the
    // frontend with nothing behind it on the backend until now.
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:3000',
        ]);
        ContactMessage::create($data);
        return response()->json(['ok' => true], 201);
    }

    public function index()
    {
        return ContactMessage::orderByDesc('created_at')->get();
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate(['read' => 'required|boolean']);
        $contactMessage->update($data);
        return response()->json($contactMessage);
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return response()->json(['ok' => true]);
    }
}
