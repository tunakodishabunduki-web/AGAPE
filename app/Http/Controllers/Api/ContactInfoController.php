<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\Request;

class ContactInfoController extends Controller
{
    public function show()
    {
        return ContactInfo::current();
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'office' => 'nullable|string|max:255',
            'regional_offices' => 'nullable|string|max:100',
            'availability' => 'nullable|string|max:100',
            'intro' => 'nullable|string|max:1000',
        ]);
        $info = ContactInfo::current();
        $info->update($data);
        return response()->json($info);
    }
}
