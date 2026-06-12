<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import Navbar from '@/Components/Site/Navbar.vue';
import BlogPostForm from './Partials/BlogPostForm.vue';

type BlogPost = {
    id: number;
    slug: string;
    category: string;
    title: string;
    excerpt: string;
    content: string;
    author: string;
    read_time: string;
    image: string;
    featured: boolean;
    tags: string[];
};

const props = defineProps<{
    post: BlogPost;
}>();

const deletePost = () => {
    if (confirm(`Delete "${props.post.title}"?`)) {
        router.delete(`/admin/blog/${props.post.id}`);
    }
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />

        <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <section class="mb-8 border-b border-white/10 pb-8">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-sky-300">Admin Blog</p>
                    <Link
                        href="/admin/blog"
                        class="text-sm font-semibold text-slate-400 hover:text-white"
                    >
                        &larr; Back to list
                    </Link>
                </div>
                <div
                    class="mt-3 flex flex-col justify-between gap-4 md:flex-row md:items-end"
                >
                    <div>
                        <h1 class="text-4xl font-bold text-white">
                            Edit blog post.
                        </h1>
                        <p
                            class="mt-3 max-w-2xl text-sm leading-6 text-slate-400"
                        >
                            Update the article details, image, tags, or full
                            HTML body.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="deletePost"
                        class="w-fit rounded-full border border-red-400/30 px-5 py-2.5 text-sm font-semibold text-red-200 transition hover:bg-red-400/10"
                    >
                        Delete post
                    </button>
                </div>
            </section>

            <div class="rounded-lg border border-white/10 bg-white/[0.04] p-6">
                <BlogPostForm
                    submit-label="Save changes"
                    :action="`/admin/blog/${post.id}`"
                    method="put"
                    :initial-values="{
                        ...post,
                        tags: Array.isArray(post.tags) ? post.tags.join(', ') : post.tags ?? '',
                    }"
                />
            </div>
        </main>
    </div>
</template>

