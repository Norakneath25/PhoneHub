<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Database\Seeders\BlogPostSeeder;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Throwable;

class BlogController extends Controller
{
    public function index()
    {
        $posts = $this->blogPosts()->map(fn ($post) => $this->formatPost($post));

        return Inertia::render('Blog/Index', ['posts' => $posts]);
    }

    public function show(string $slug)
    {
        $posts = $this->blogPosts();
        $post = $posts->first(fn ($post) => $this->postValue($post, 'slug') === $slug);

        abort_if(! $post, 404);

        $related = $posts
            ->filter(fn ($item) => $this->postValue($item, 'category') === $this->postValue($post, 'category'))
            ->reject(fn ($item) => $this->postValue($item, 'slug') === $slug)
            ->take(2)
            ->map(fn ($post) => $this->formatPost($post))
            ->values();

        return Inertia::render('Blog/Show', [
            'post' => $this->formatPost($post),
            'related' => $related,
        ]);
    }

    private function blogPosts(): Collection
    {
        try {
            $posts = BlogPost::latest()->get();
        } catch (Throwable) {
            $posts = collect();
        }

        return $posts->isNotEmpty()
            ? $posts
            : collect(BlogPostSeeder::posts())
                ->map(fn ($post, $index) => array_merge(['id' => $index + 1], $post));
    }

    private function formatPost(BlogPost|array $post): array
    {
        $tags = $this->postValue($post, 'tags') ?? [];

        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?: [];
        }

        return [
            'id' => $this->postValue($post, 'id'),
            'slug' => $this->postValue($post, 'slug'),
            'category' => $this->postValue($post, 'category'),
            'title' => $this->postValue($post, 'title'),
            'content' => $this->postValue($post, 'content'),
            'excerpt' => $this->postValue($post, 'excerpt'),
            'author' => $this->postValue($post, 'author'),
            'date' => $post instanceof BlogPost && $post->created_at
                ? $post->created_at->format('M d, Y')
                : 'May 21, 2026',
            'read_time' => $this->postValue($post, 'read_time'),
            'image' => $this->postValue($post, 'image'),
            'featured' => (bool) $this->postValue($post, 'featured'),
            'tags' => $tags,
        ];
    }

    private function postValue(BlogPost|array $post, string $key): mixed
    {
        return $post instanceof BlogPost ? $post->{$key} : ($post[$key] ?? null);
    }
}
