<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::latest()->get()->map(fn($p) => [
            'id'        => $p->id,
            'slug'      => $p->slug,
            'category'  => $p->category,
            'title'     => $p->title,
            'excerpt'   => $p->excerpt,
            'author'    => $p->author,
            'date'      => $p->created_at->format('M d, Y'),
            'read_time' => $p->read_time,
            'image'     => $p->image,
            'featured'  => (bool) $p->featured,
            'tags'      => $p->tags ?? [],
        ]);

        return Inertia::render('Blog/Index', ['posts' => $posts]);
    }

    public function show(string $slug)
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();

        $related = BlogPost::where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(2)
            ->get()
            ->map(fn($p) => [
                'id'        => $p->id,
                'slug'      => $p->slug,
                'category'  => $p->category,
                'title'     => $p->title,
                'read_time' => $p->read_time,
                'image'     => $p->image,
            ]);

        return Inertia::render('Blog/Show', [
            'post' => [
                'id'        => $post->id,
                'slug'      => $post->slug,
                'category'  => $post->category,
                'title'     => $post->title,
                'content'   => $post->content,
                'excerpt'   => $post->excerpt,
                'author'    => $post->author,
                'date'      => $post->created_at->format('M d, Y'),
                'read_time' => $post->read_time,
                'image'     => $post->image,
                'tags'      => $post->tags ?? [],
            ],
            'related' => $related,
        ]);
    }
}