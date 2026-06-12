<script setup lang="ts">
import Navbar from '@/Components/Site/Navbar.vue';
import BlogPostForm from './Partials/BlogPostForm.vue';
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />

        <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <section class="mb-8 border-b border-white/10 pb-8">
                <p class="text-sm font-semibold text-sky-300">Admin Blog</p>
                <h1 class="mt-3 text-4xl font-bold text-white">
                    Publish a blog post.
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">
                    Posts are saved to the blog_posts table and appear on the
                    public Blog page immediately.
                </p>
            </section>

            <div class="rounded-lg border border-white/10 bg-white/[0.04] p-6">
                <BlogPostForm submit-label="Publish post" action="/admin/blog" />
            </div>
        </main>
    </div>
</template>
