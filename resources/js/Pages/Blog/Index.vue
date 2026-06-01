<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Footer from '@/Components/Site/Footer.vue';
import Navbar from '@/Components/Site/Navbar.vue';

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
        : props.posts.filter((post) => post.category === activeCategory.value),
);

const featured = computed(() => props.posts.find((post) => post.featured));
const leadPost = computed(() => {
    if (activeCategory.value === 'All') {
        return featured.value ?? props.posts[0];
    }

    return filteredPosts.value[0];
});

const storyPosts = computed(() =>
    filteredPosts.value.filter((post) => post.id !== leadPost.value?.id),
);

const latestPosts = computed(() => props.posts.slice(0, 4));

const categoryCounts = computed(() =>
    categories.map((category) => ({
        name: category,
        count:
            category === 'All'
                ? props.posts.length
                : props.posts.filter((post) => post.category === category)
                      .length,
    })),
);

const categoryTone = (category: string) => {
    const tones: Record<string, string> = {
        Reviews: 'bg-emerald-500/15 text-emerald-200 ring-emerald-400/20',
        News: 'bg-sky-500/15 text-sky-200 ring-sky-400/20',
        Comparisons: 'bg-amber-500/15 text-amber-200 ring-amber-400/20',
        'Tips & Tricks': 'bg-rose-500/15 text-rose-200 ring-rose-400/20',
    };

    return tones[category] ?? 'bg-white/10 text-white ring-white/15';
};

const tagList = computed(() =>
    [...new Set(props.posts.flatMap((post) => post.tags ?? []))].slice(0, 10),
);
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />

        <main>
            <section class="border-b border-white/10 bg-[#101722]">
                <div
                    class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:px-8 lg:py-16"
                >
                    <div class="self-end">
                        <p
                            class="mb-4 text-sm font-semibold uppercase tracking-[0.22em] text-sky-300"
                        >
                            PhoneHub Journal
                        </p>
                        <h1
                            class="max-w-4xl text-4xl font-black leading-tight text-white sm:text-6xl"
                        >
                            Phone coverage for sharper buying decisions.
                        </h1>
                        <p
                            class="mt-5 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg"
                        >
                            Reviews, comparisons, software notes, and phone news
                            written around the details that matter after the
                            launch hype fades.
                        </p>
                    </div>

                    <aside
                        class="self-end rounded-lg border border-white/10 bg-white/[0.04] p-5 text-sm text-slate-300"
                    >
                        <div class="grid grid-cols-3 gap-4 lg:grid-cols-1">
                            <div>
                                <p class="text-2xl font-bold text-white">
                                    {{ posts.length }}
                                </p>
                                <p>published stories</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-white">
                                    {{ categories.length - 1 }}
                                </p>
                                <p>phone topics</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-white">
                                    Daily
                                </p>
                                <p>buyer focus</p>
                            </div>
                        </div>

                        <div
                            v-if="tagList.length"
                            class="mt-5 border-t border-white/10 pt-5"
                        >
                            <p
                                class="mb-3 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500"
                            >
                                Topics
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="tag in tagList"
                                    :key="tag"
                                    class="rounded-full bg-white/[0.06] px-3 py-1 text-xs text-slate-300"
                                >
                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div
                    class="flex gap-2 overflow-x-auto border-b border-white/10 pb-5"
                >
                    <button
                        v-for="category in categoryCounts"
                        :key="category.name"
                        @click="activeCategory = category.name"
                        :class="[
                            'shrink-0 rounded-full px-4 py-2 text-sm font-medium transition',
                            activeCategory === category.name
                                ? 'bg-white text-slate-950'
                                : 'bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white',
                        ]"
                    >
                        {{ category.name }}
                        <span class="ml-1 text-xs opacity-70">
                            {{ category.count }}
                        </span>
                    </button>
                </div>

                <div
                    v-if="leadPost"
                    class="grid gap-8 py-8 lg:grid-cols-[minmax(0,1fr)_340px]"
                >
                    <Link
                        :href="`/blog/${leadPost.slug}`"
                        class="group grid overflow-hidden rounded-lg border border-white/10 bg-white/[0.04] shadow-2xl shadow-black/20 transition hover:border-sky-300/50 md:grid-cols-[1.12fr_0.88fr]"
                    >
                        <div class="relative min-h-[320px] overflow-hidden">
                            <img
                                :src="leadPost.image"
                                :alt="leadPost.title"
                                class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent"
                            />
                            <span
                                :class="[
                                    'absolute left-4 top-4 rounded-full px-3 py-1 text-xs font-semibold ring-1',
                                    categoryTone(leadPost.category),
                                ]"
                            >
                                {{ leadPost.category }}
                            </span>
                        </div>

                        <div class="flex flex-col justify-center p-6 sm:p-8">
                            <p
                                class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-sky-300"
                            >
                                Lead Story
                            </p>
                            <h2
                                class="text-3xl font-bold leading-tight text-white"
                            >
                                {{ leadPost.title }}
                            </h2>
                            <p class="mt-4 text-base leading-7 text-slate-300">
                                {{ leadPost.excerpt }}
                            </p>
                            <div
                                class="mt-6 flex flex-wrap items-center gap-3 text-sm text-slate-400"
                            >
                                <span>{{ leadPost.author }}</span>
                                <span aria-hidden="true">/</span>
                                <span>{{ leadPost.date }}</span>
                                <span aria-hidden="true">/</span>
                                <span>{{ leadPost.read_time }}</span>
                            </div>
                            <div class="mt-7">
                                <span
                                    class="inline-flex rounded-full bg-sky-500 px-5 py-2 text-sm font-semibold text-white transition group-hover:bg-sky-400"
                                >
                                    Read full story
                                </span>
                            </div>
                        </div>
                    </Link>

                    <aside class="space-y-5">
                        <div
                            class="rounded-lg border border-white/10 bg-white/[0.03] p-5"
                        >
                            <h2
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"
                            >
                                Latest Briefing
                            </h2>
                            <div class="mt-4 space-y-4">
                                <Link
                                    v-for="post in latestPosts"
                                    :key="post.id"
                                    :href="`/blog/${post.slug}`"
                                    class="block border-t border-white/10 pt-4 first:border-t-0 first:pt-0"
                                >
                                    <p
                                        class="mb-1 text-xs font-medium text-sky-300"
                                    >
                                        {{ post.category }} /
                                        {{ post.read_time }}
                                    </p>
                                    <h3
                                        class="text-sm font-semibold leading-6 text-white transition hover:text-sky-200"
                                    >
                                        {{ post.title }}
                                    </h3>
                                </Link>
                            </div>
                        </div>

                        <div
                            class="rounded-lg border border-sky-300/20 bg-[#0f2230] p-5"
                        >
                            <h2
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-200"
                            >
                                Editorial Focus
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-slate-300">
                                Camera samples, battery claims, display quality,
                                software updates, and honest value checks before
                                you buy.
                            </p>
                        </div>
                    </aside>
                </div>

                <div
                    v-if="storyPosts.length"
                    class="grid gap-5 pb-12 md:grid-cols-2 xl:grid-cols-3"
                >
                    <Link
                        v-for="post in storyPosts"
                        :key="post.id"
                        :href="`/blog/${post.slug}`"
                        class="group overflow-hidden rounded-lg border border-white/10 bg-white/[0.035] transition hover:-translate-y-1 hover:border-white/25 hover:bg-white/[0.06]"
                    >
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img
                                :src="post.image"
                                :alt="post.title"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            />
                            <span
                                :class="[
                                    'absolute left-3 top-3 rounded-full px-3 py-1 text-xs font-semibold ring-1',
                                    categoryTone(post.category),
                                ]"
                            >
                                {{ post.category }}
                            </span>
                        </div>

                        <article class="p-5">
                            <div
                                class="mb-3 flex flex-wrap items-center gap-2 text-xs text-slate-400"
                            >
                                <span>{{ post.date }}</span>
                                <span aria-hidden="true">/</span>
                                <span>{{ post.read_time }}</span>
                            </div>
                            <h2
                                class="text-xl font-bold leading-snug text-white"
                            >
                                {{ post.title }}
                            </h2>
                            <p
                                class="mt-3 line-clamp-3 text-sm leading-6 text-slate-400"
                            >
                                {{ post.excerpt }}
                            </p>
                            <div
                                class="mt-5 flex items-center justify-between border-t border-white/10 pt-4"
                            >
                                <span class="text-sm text-slate-400">
                                    {{ post.author }}
                                </span>
                                <span
                                    class="text-sm font-semibold text-sky-300 transition group-hover:text-sky-200"
                                >
                                    Read story
                                </span>
                            </div>
                        </article>
                    </Link>
                </div>

                <div
                    v-else
                    class="mb-12 rounded-lg border border-white/10 bg-white/[0.03] py-20 text-center"
                >
                    <p class="text-sm text-slate-400">
                        No posts in this category yet.
                    </p>
                </div>
            </section>
        </main>

        <Footer />
    </div>
</template>
