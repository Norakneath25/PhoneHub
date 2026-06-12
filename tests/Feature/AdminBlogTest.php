<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_blog_post(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('admin.blog.store'), [
                'title' => 'Galaxy Z Fold Buying Guide',
                'slug' => '',
                'category' => 'Tips & Tricks',
                'excerpt' => 'How to decide if a foldable phone is worth buying.',
                'content' => '<p>Foldables are useful when the big screen fits your workflow.</p>',
                'author' => 'PhoneHub Admin',
                'read_time' => '4 min read',
                'image' => 'https://example.com/foldable.jpg',
                'featured' => true,
                'tags' => 'Samsung, Foldable, Guide',
            ])
            ->assertRedirect(route('admin.blog.index'));

        $this->assertDatabaseHas('blog_posts', [
            'slug' => 'galaxy-z-fold-buying-guide',
            'title' => 'Galaxy Z Fold Buying Guide',
            'featured' => true,
        ]);

        $this->assertSame(
            ['Samsung', 'Foldable', 'Guide'],
            BlogPost::where('slug', 'galaxy-z-fold-buying-guide')->first()?->tags,
        );
    }

    public function test_admin_can_update_blog_post(): void
    {
        $post = BlogPost::create([
            'slug' => 'old-title',
            'category' => 'News',
            'title' => 'Old Title',
            'excerpt' => 'Old excerpt',
            'content' => '<p>Old content.</p>',
            'author' => 'Old Author',
            'read_time' => '3 min read',
            'image' => 'https://example.com/old.jpg',
            'featured' => false,
            'tags' => ['Old'],
        ]);

        $this->actingAs($this->adminUser())
            ->put(route('admin.blog.update', $post), [
                'title' => 'Updated Blog Title',
                'slug' => 'updated-blog-title',
                'category' => 'Reviews',
                'excerpt' => 'Updated excerpt',
                'content' => '<p>Updated content.</p>',
                'author' => 'PhoneHub Admin',
                'read_time' => '7 min read',
                'image' => 'https://example.com/updated.jpg',
                'featured' => false,
                'tags' => 'Updated, Review',
            ])
            ->assertRedirect(route('admin.blog.index'));

        $this->assertDatabaseHas('blog_posts', [
            'id' => $post->id,
            'slug' => 'updated-blog-title',
            'title' => 'Updated Blog Title',
        ]);
    }

    public function test_admin_can_delete_blog_post(): void
    {
        $post = BlogPost::create([
            'slug' => 'delete-me',
            'category' => 'News',
            'title' => 'Delete Me',
            'excerpt' => 'Delete this post.',
            'content' => '<p>Delete me.</p>',
            'author' => 'PhoneHub Admin',
            'read_time' => '2 min read',
            'image' => 'https://example.com/delete.jpg',
            'featured' => false,
            'tags' => ['Delete'],
        ]);

        $this->actingAs($this->adminUser())
            ->delete(route('admin.blog.destroy', $post))
            ->assertRedirect(route('admin.blog.index'));

        $this->assertDatabaseMissing('blog_posts', [
            'id' => $post->id,
        ]);
    }

    private function adminUser(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['is_admin' => true])->save();

        return $user;
    }
}
