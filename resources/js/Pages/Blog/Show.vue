<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Footer from '@/Components/Site/Footer.vue';
import Navbar from '@/Components/Site/Navbar.vue';

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

const props = defineProps<{
    post: Post;
    related: Post[];
}>();

const topicLine = computed(() => props.post.tags.slice(0, 3).join(' / '));

const categoryTone = computed(() => {
    const tones: Record<string, string> = {
        Reviews: 'bg-emerald-500/15 text-emerald-200 ring-emerald-400/20',
        News: 'bg-sky-500/15 text-sky-200 ring-sky-400/20',
        Comparisons: 'bg-amber-500/15 text-amber-200 ring-amber-400/20',
        'Tips & Tricks': 'bg-rose-500/15 text-rose-200 ring-rose-400/20',
    };

    return tones[props.post.category] ?? 'bg-white/10 text-white ring-white/15';
});
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />

        <main>
            <section class="border-b border-white/10 bg-[#101722]">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <Link
                        href="/blog"
                        class="inline-flex text-sm font-semibold text-slate-300 transition hover:text-white"
                    >
                        Back to blog
                    </Link>
                </div>

                <div
                    class="mx-auto grid max-w-7xl gap-8 px-4 pb-12 sm:px-6 lg:grid-cols-[minmax(0,0.94fr)_minmax(360px,0.66fr)] lg:px-8 lg:pb-16"
                >
                    <div class="self-end">
                        <div
                            class="mb-5 flex flex-wrap items-center gap-3 text-sm text-slate-300"
                        >
                            <span
                                :class="[
                                    'rounded-full px-3 py-1 font-semibold ring-1',
                                    categoryTone,
                                ]"
                            >
                                {{ post.category }}
                            </span>
                            <span>{{ post.date }}</span>
                            <span aria-hidden="true">/</span>
                            <span>{{ post.read_time }}</span>
                        </div>

                        <h1
                            class="max-w-4xl text-4xl font-black leading-tight text-white sm:text-6xl"
                        >
                            {{ post.title }}
                        </h1>
                        <p
                            class="mt-5 max-w-3xl text-lg leading-8 text-slate-300"
                        >
                            {{ post.excerpt }}
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-sky-500 text-sm font-bold text-white"
                            >
                                {{ post.author.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-semibold text-white">
                                    {{ post.author }}
                                </p>
                                <p class="text-sm text-slate-400">
                                    PhoneHub Editorial
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden rounded-lg border border-white/10 shadow-2xl shadow-black/25"
                    >
                        <img
                            :src="post.image"
                            :alt="post.title"
                            class="aspect-[4/3] h-full w-full object-cover"
                        />
                    </div>
                </div>
            </section>

            <section
                class="mx-auto grid max-w-7xl gap-10 px-4 py-10 sm:px-6 lg:grid-cols-[220px_minmax(0,760px)_280px] lg:px-8"
            >
                <aside class="hidden lg:block">
                    <div class="sticky top-24 space-y-6">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                            >
                                ARTICLE
                            </p>
                            <p class="mt-2 text-sm leading-6 text-slate-300">
                                {{ post.read_time }} by {{ post.author }}
                            </p>
                        </div>
                        <div v-if="topicLine">
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                            >
                                TOPICS
                            </p>
                            <p class="mt-2 text-sm leading-6 text-slate-300">
                                {{ topicLine }}
                            </p>
                        </div>
                    </div>
                </aside>

                <article>
                    <div class="mb-8 flex flex-wrap gap-2">
                        <span
                            v-for="tag in post.tags"
                            :key="tag"
                            class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1 text-sm text-slate-300"
                        >
                            {{ tag }}
                        </span>
                    </div>

                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.03] px-5 py-7 sm:px-8 sm:py-9"
                    >
                        <div class="article-body" v-html="post.content" />
                    </div>
                </article>

                <aside class="space-y-5">
                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.03] p-5"
                    >
                        <h2
                            class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"
                        >
                            Buying Takeaway
                        </h2>
                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Treat every flagship claim as a tradeoff: camera,
                            battery, software support, repairability, and price
                            all matter more than one headline spec.
                        </p>
                    </div>

                    <div
                        v-if="related.length"
                        class="rounded-lg border border-white/10 bg-white/[0.03] p-5"
                    >
                        <h2
                            class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"
                        >
                            More {{ post.category }}
                        </h2>
                        <div class="mt-4 space-y-4">
                            <Link
                                v-for="rel in related"
                                :key="rel.id"
                                :href="`/blog/${rel.slug}`"
                                class="block border-t border-white/10 pt-4 first:border-t-0 first:pt-0"
                            >
                                <img
                                    :src="rel.image"
                                    :alt="rel.title"
                                    class="mb-3 aspect-[16/9] w-full rounded-md object-cover"
                                />
                                <p
                                    class="mb-1 text-xs font-medium text-sky-300"
                                >
                                    {{ rel.category }} / {{ rel.read_time }}
                                </p>
                                <h3
                                    class="text-sm font-semibold leading-6 text-white transition hover:text-sky-200"
                                >
                                    {{ rel.title }}
                                </h3>
                            </Link>
                        </div>
                    </div>
                </aside>
            </section>
        </main>

        <Footer />
    </div>
</template>

<style scoped>
.article-body {
    color: rgb(203 213 225);
    font-size: 1.0625rem;
    line-height: 1.85;
}

.article-body :deep(p) {
    margin: 0 0 1.45rem;
}

.article-body :deep(h2) {
    color: white;
    font-size: 1.55rem;
    font-weight: 750;
    line-height: 1.25;
    margin: 2.25rem 0 0.85rem;
}

.article-body :deep(p:first-child) {
    color: rgb(226 232 240);
    font-size: 1.18rem;
    line-height: 1.8;
}
</style>
