<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroContent;
use Illuminate\Http\Request;

class HeroController extends Controller
{
    public function show()
    {
        return HeroContent::current();
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'headline' => 'required|string|max:255',
            'stat_value' => 'nullable|string|max:20',
            'subheadline' => 'nullable|string|max:500',
            'background_image' => 'nullable|string',
            'background_image_alt' => 'nullable|string|max:255',
        ]);
        $hero = HeroContent::current();
        $hero->update($data);
        return response()->json($hero);
    }
}
