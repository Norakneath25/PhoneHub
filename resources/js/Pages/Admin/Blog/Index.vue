<script setup lang="ts">
import type { PageProps } from '@inertiajs/core';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Navbar from '@/Components/Site/Navbar.vue';

type BlogPost = {
    id: number;
    slug: string;
    category: string;
    title: string;
    excerpt: string;
    author: string;
    read_time: string;
    image: string;
    featured: boolean;
    tags: string[];
    created_at: string;
};

type AdminBlogPageProps = PageProps & {
    flash?: {
        success?: string | null;
        error?: string | null;
    };
};

defineProps<{
    posts: BlogPost[];
}>();

const page = usePage<AdminBlogPageProps>();
const flash = computed(() => page.props.flash ?? {});

const deletePost = (post: BlogPost) => {
    if (confirm(`Delete "${post.title}"?`)) {
        router.delete(`/admin/blog/${post.id}`);
    }
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <section
                class="mb-8 flex flex-col justify-between gap-5 border-b border-white/10 pb-8 md:flex-row md:items-end"
            >
                <div>
                    <p class="text-sm font-semibold text-sky-300">
                        Admin Blog
                    </p>
                    <h1 class="mt-3 text-4xl font-bold text-white">
                        Manage blog posts.
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">
                        Publish buying guides, reviews, news, and comparison
                        stories that appear on the public blog.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Link
                        href="/admin"
                        class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10"
                    >
                        Admin home
                    </Link>
                    <Link
                        href="/admin/blog/create"
                        class="rounded-full bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400"
                    >
                        New post
                    </Link>
                </div>
            </section>

            <div
                v-if="flash.success"
                class="mb-6 rounded-lg border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash.error"
                class="mb-6 rounded-lg border border-red-400/20 bg-red-400/10 px-4 py-3 text-sm text-red-200"
            >
                {{ flash.error }}
            </div>

            <div
                v-if="posts.length"
                class="overflow-hidden rounded-lg border border-white/10"
            >
                <table class="w-full table-fixed divide-y divide-white/10">
                    <thead class="bg-white/[0.04]">
                        <tr class="text-left text-xs uppercase text-slate-500">
                            <th class="w-[42%] px-4 py-3">Post</th>
                            <th class="w-[16%] px-4 py-3">Category</th>
                            <th class="w-[16%] px-4 py-3">Author</th>
                            <th class="w-[12%] px-4 py-3">Date</th>
                            <th class="w-[14%] px-4 py-3 text-right">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 bg-white/[0.02]">
                        <tr v-for="post in posts" :key="post.id">
                            <td class="px-4 py-4">
                                <div class="flex min-w-0 items-center gap-4">
                                    <img
                                        :src="post.image"
                                        :alt="post.title"
                                        class="h-14 w-20 shrink-0 rounded-md object-cover"
                                    />
                                    <div class="min-w-0">
                                        <div
                                            class="flex items-center gap-2 text-sm font-semibold text-white"
                                        >
                                            <span class="truncate">
                                                {{ post.title }}
                                            </span>
                                            <span
                                                v-if="post.featured"
                                                class="shrink-0 rounded-full bg-sky-500/15 px-2 py-0.5 text-xs text-sky-200"
                                            >
                                                Featured
                                            </span>
                                        </div>
                                        <p
                                            class="mt-1 truncate text-xs text-slate-500"
                                        >
                                            /blog/{{ post.slug }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-300">
                                {{ post.category }}
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-300">
                                {{ post.author }}
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-400">
                                {{ post.created_at }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-3 text-sm">
                                    <Link
                                        :href="`/blog/${post.slug}`"
                                        class="font-semibold text-sky-300 hover:text-sky-200"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        :href="`/admin/blog/${post.id}/edit`"
                                        class="font-semibold text-slate-300 hover:text-white"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        @click="deletePost(post)"
                                        class="font-semibold text-red-300 hover:text-red-200"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-else
                class="rounded-lg border border-white/10 bg-white/[0.04] px-6 py-14 text-center"
            >
                <h2 class="text-xl font-bold text-white">No blog posts yet</h2>
                <p class="mt-2 text-sm text-slate-400">
                    Create the first post or seed the default blog data.
                </p>
                <Link
                    href="/admin/blog/create"
                    class="mt-6 inline-flex rounded-full bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400"
                >
                    New post
                </Link>
            </div>
        </main>
    </div>
</template>
