<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AdminBlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::latest()->get()->map(fn (BlogPost $post) => [
            'id' => $post->id,
            'slug' => $post->slug,
            'category' => $post->category,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'author' => $post->author,
            'read_time' => $post->read_time,
            'image' => $post->image,
            'featured' => $post->featured,
            'tags' => $post->tags ?? [],
            'created_at' => $post->created_at?->format('M d, Y'),
        ]);

        return Inertia::render('Admin/Blog/Index', ['posts' => $posts]);
    }

    public function create()
    {
        return Inertia::render('Admin/Blog/Create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $post = BlogPost::create($data);

        $this->syncFeaturedPost($data, $post);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post published.');
    }

    public function edit(BlogPost $post)
    {
        return Inertia::render('Admin/Blog/Edit', [
            'post' => [
                'id' => $post->id,
                'slug' => $post->slug,
                'category' => $post->category,
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'author' => $post->author,
                'read_time' => $post->read_time,
                'image' => $post->image,
                'featured' => $post->featured,
                'tags' => $post->tags ?? [],
            ],
        ]);
    }

    public function update(Request $request, BlogPost $post)
    {
        $data = $this->validatedData($request, $post);

        $post->update($data);

        $this->syncFeaturedPost($data, $post);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post updated.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post deleted.');
    }

    private function validatedData(Request $request, ?BlogPost $post = null): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug'.($post ? ','.$post->id : ''),
            'category' => 'required|string|max:255',
            'excerpt' => 'required|string|max:1000',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'read_time' => 'required|string|max:50',
            'image' => 'required|url|max:2048',
            'featured' => 'boolean',
            'tags' => 'nullable|string|max:1000',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['featured'] = (bool) ($validated['featured'] ?? false);
        $validated['tags'] = collect(explode(',', $validated['tags'] ?? ''))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->values()
            ->all();

        // Re-check uniqueness with the auto-generated slug
        $slugExists = BlogPost::where('slug', $validated['slug'])
            ->when($post, fn ($query) => $query->where('id', '!=', $post->id))
            ->exists();

        if ($slugExists) {
            throw ValidationException::withMessages([
                'slug' => 'This slug is already used by another blog post.',
            ]);
        }

        return $validated;
    }

    private function syncFeaturedPost(array $data, ?BlogPost $post = null): void
    {
        if (! $data['featured']) {
            return;
        }

        BlogPost::where('id', '!=', $post?->id ?? 0)->update(['featured' => false]);
    }
}
