<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'body', 'image_path', 'alt_text', 'published_at'];

    protected $casts = ['published_at' => 'datetime'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->orderByDesc('published_at');
    }
}
