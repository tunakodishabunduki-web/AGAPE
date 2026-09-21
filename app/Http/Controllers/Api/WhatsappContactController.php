<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsappContact;
use App\Services\ContentCaps;
use Illuminate\Http\Request;

class WhatsappContactController extends Controller
{
    public function index()
    {
        return WhatsappContact::all();
    }

    public function store(Request $request)
    {
        if (WhatsappContact::count() >= ContentCaps::MAX_WHATSAPP) {
            return response()->json(['error' => 'Limit reached ('.ContentCaps::MAX_WHATSAPP.' contacts).'], 422);
        }
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'phone_number' => 'required|string|max:30',
        ]);
        return response()->json(WhatsappContact::create($data), 201);
    }

    public function destroy(WhatsappContact $whatsappContact)
    {
        $whatsappContact->delete();
        return response()->json(['ok' => true]);
    }
}
