<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Navbar from '@/components/Navbar.vue';

interface Post {
    id: number;
    slug: string;
    category: string;
    title: string;
    excerpt: string;
    author: string;
    date: string;
    read_time: string;
    image: string;
    featured: boolean;
    tags: string[];
}

const props = defineProps<{
    posts: Post[];
}>();

const categories = ['All', 'Reviews', 'News', 'Comparisons', 'Tips & Tricks'];
const activeCategory = ref('All');

const filteredPosts = computed(() =>
    activeCategory.value === 'All'
        ? props.posts
        : props.posts.filter((p) => p.category === activeCategory.value),
);

const featured = computed(() => props.posts.find((p) => p.featured));
const gridPosts = computed(() =>
    activeCategory.value === 'All'
        ? filteredPosts.value.filter((p) => !p.featured)
        : filteredPosts.value,
);
</script>

<template>
    <div class="min-h-screen bg-gray-950">
        <Navbar />

        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white">Phone Blog</h1>
                <p class="mt-1 text-sm text-gray-400">
                    Reviews, news, and guides from the PhoneHub team.
                </p>
            </div>

            <!-- Category Tabs -->
            <div class="mb-8 flex flex-wrap gap-2">
                <button
                    v-for="cat in categories"
                    :key="cat"
                    @click="activeCategory = cat"
                    :class="[
                        'rounded-lg px-4 py-2 text-sm font-medium transition-colors',
                        activeCategory === cat
                            ? 'bg-blue-600 text-white'
                            : 'border border-gray-700 text-gray-400 hover:border-gray-500 hover:text-white',
                    ]"
                >
                    {{ cat }}
                </button>
            </div>

            <!-- Featured Post -->
            <div
                v-if="featured && activeCategory === 'All'"
                class="mb-10 overflow-hidden rounded-xl border border-gray-800"
            >
                <div class="relative">
                    <img
                        :src="featured.image"
                        :alt="featured.title"
                        class="h-80 w-full object-cover brightness-50"
                    />
                    <span
                        class="absolute left-4 top-4 rounded bg-blue-600 px-2 py-1 text-xs font-semibold uppercase tracking-wider text-white"
                    >
                        Featured
                    </span>
                    <span
                        class="absolute right-4 top-4 rounded bg-gray-900/80 px-2 py-1 text-xs font-medium text-gray-300 backdrop-blur-sm"
                    >
                        {{ featured.category }}
                    </span>
                </div>
                <div class="bg-gray-800 p-6">
                    <div class="mb-2 flex items-center gap-3 text-xs text-gray-500">
                        <span>{{ featured.date }}</span>
                        <span>·</span>
                        <span>{{ featured.read_time }}</span>
                        <span>·</span>
                        <span>{{ featured.author }}</span>
                    </div>
                    <h2 class="mb-3 text-2xl font-bold text-white">
                        {{ featured.title }}
                    </h2>
                    <p class="mb-5 text-sm leading-relaxed text-gray-400">
                        {{ featured.excerpt }}
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="tag in featured.tags"
                                :key="tag"
                                class="rounded border border-gray-700 px-2 py-0.5 text-xs text-gray-500"
                            >
                                {{ tag }}
                            </span>
                        </div>
                        <Link
                            :href="`/blog/${featured.slug}`"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-500"
                        >
                            Read Article →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Grid -->
            <div
                v-if="gridPosts.length"
                class="grid gap-px sm:grid-cols-2 lg:grid-cols-3"
            >
                <article
                    v-for="post in gridPosts"
                    :key="post.id"
                    class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 transition-colors hover:border-gray-600"
                >
                    <div class="relative">
                        <img
                            :src="post.image"
                            :alt="post.title"
                            class="h-44 w-full object-cover brightness-75 transition-all duration-300 hover:brightness-90"
                        />
                        <span
                            class="absolute left-3 top-3 rounded bg-gray-900/80 px-2 py-0.5 text-xs font-medium text-gray-300 backdrop-blur-sm"
                        >
                            {{ post.category }}
                        </span>
                    </div>

                    <div class="p-5">
                        <div class="mb-2 flex items-center gap-2 text-xs text-gray-600">
                            <span>{{ post.date }}</span>
                            <span>·</span>
                            <span>{{ post.read_time }}</span>
                        </div>

                        <h3 class="mb-2 font-semibold leading-snug text-white line-clamp-2">
                            {{ post.title }}
                        </h3>

                        <p class="mb-4 text-sm leading-relaxed text-gray-500 line-clamp-3">
                            {{ post.excerpt }}
                        </p>

                        <div class="flex items-center justify-between border-t border-gray-800 pt-4">
                            <span class="text-xs text-gray-600">{{ post.author }}</span>
                            <Link
                                :href="`/blog/${post.slug}`"
                                class="text-xs font-medium text-blue-400 transition-colors hover:text-blue-300"
                            >
                                Read →
                            </Link>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Empty state -->
            <div
                v-else
                class="rounded-xl border border-gray-800 bg-gray-900 py-20 text-center"
            >
                <p class="text-sm text-gray-500">No posts in this category yet.</p>
            </div>
        </div>
    </div>
</template>