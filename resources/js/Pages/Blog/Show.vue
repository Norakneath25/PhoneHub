<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';

interface Post {
    id: number;
    slug: string;
    category: string;
    title: string;
    content: string;
    excerpt: string;
    author: string;
    date: string;
    read_time: string;
    image: string;
    tags: string[];
}

defineProps<{
    post: Post;
    related: Post[];
}>();
</script>

<template>
    <div class="min-h-screen bg-gray-950">
        <Navbar />

        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">

            <!-- Back -->
            <div class="mb-6">
                <Link
                    href="/blog"
                    class="text-sm text-gray-500 transition-colors hover:text-gray-300"
                >
                    ← Back to Blog
                </Link>
            </div>

            <!-- Hero Image -->
            <div class="mb-8 overflow-hidden rounded-xl">
                <img
                    :src="post.image"
                    :alt="post.title"
                    class="h-72 w-full object-cover brightness-75"
                />
            </div>

            <!-- Meta -->
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <span
                    class="rounded bg-blue-600/20 px-2 py-1 text-xs font-semibold uppercase tracking-wider text-blue-400"
                >
                    {{ post.category }}
                </span>
                <span class="text-xs text-gray-600">{{ post.date }}</span>
                <span class="text-xs text-gray-600">·</span>
                <span class="text-xs text-gray-600">{{ post.read_time }}</span>
                <span class="text-xs text-gray-600">·</span>
                <span class="text-xs text-gray-600">By {{ post.author }}</span>
            </div>

            <!-- Title -->
            <h1 class="mb-6 text-3xl font-bold leading-tight text-white">
                {{ post.title }}
            </h1>

            <!-- Tags -->
            <div class="mb-8 flex flex-wrap gap-2">
                <span
                    v-for="tag in post.tags"
                    :key="tag"
                    class="rounded border border-gray-800 px-2 py-0.5 text-xs text-gray-500"
                >
                    {{ tag }}
                </span>
            </div>

            <!-- Content -->
            <div
                class="prose prose-invert prose-sm max-w-none text-gray-300"
                v-html="post.content"
            />

            <!-- Related Posts -->
            <div v-if="related.length" class="mt-16">
                <h2 class="mb-6 text-lg font-semibold text-white">Related Articles</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <Link
                        v-for="rel in related"
                        :key="rel.id"
                        :href="`/blog/${rel.slug}`"
                        class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 transition-colors hover:border-gray-600"
                    >
                        <img
                            :src="rel.image"
                            :alt="rel.title"
                            class="h-32 w-full object-cover brightness-75"
                        />
                        <div class="p-4">
                            <p class="mb-1 text-xs text-gray-600">{{ rel.category }} · {{ rel.read_time }}</p>
                            <p class="text-sm font-medium leading-snug text-white line-clamp-2">{{ rel.title }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>