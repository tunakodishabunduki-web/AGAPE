<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Services\ContentCaps;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        return BlogPost::orderByDesc('published_at')->get();
    }

    public function store(Request $request)
    {
        if (! ContentCaps::canAddCard()) {
            return response()->json(['error' => 'Card limit reached (25 across gallery/programs/leaders/blog).'], 422);
        }
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'body' => 'required|string',
            'image_path' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);
        $data['slug'] = $this->uniqueSlug($data['title']);
        return response()->json(BlogPost::create($data), 201);
    }

    // Clean, readable slugs ("new-scholarship-program") instead of an ugly
    // uniqid() suffix on every single post ("new-scholarship-program-64f8a2b3c4d5e").
    // Only falls back to a numbered suffix on an actual title collision, which
    // is rare — most posts get the plain, readable slug.
    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i = 2;
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'excerpt' => 'sometimes|string|max:500',
            'body' => 'sometimes|string',
            'image_path' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);
        $blogPost->update($data);
        return response()->json($blogPost);
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();
        return response()->json(['ok' => true]);
    }
}
