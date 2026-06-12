<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_uses_default_posts_when_database_is_empty(): void
    {
        $this->get(route('blog.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Index', false)
                ->has('posts', 6)
                ->where('posts.0.slug', 'samsung-galaxy-s25-ultra-review')
            );
    }

    public function test_default_blog_posts_can_be_opened_directly(): void
    {
        $this->get(route('blog.show', 'samsung-galaxy-s25-ultra-review'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Show', false)
                ->where('post.slug', 'samsung-galaxy-s25-ultra-review')
                ->where('post.title', 'Samsung Galaxy S25 Ultra: The Pinnacle of Android in 2025')
            );
    }
}
