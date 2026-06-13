<script setup lang="ts">
import type { PageProps } from '@inertiajs/core';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PhoneIcon from '@/Components/PhoneIcon.vue';
import Navbar from '@/Components/Site/Navbar.vue';

type DashboardUser = {
    name: string;
    email: string;
    is_admin?: boolean;
};

type SavedComparison = {
    id: number;
    name: string;
    phone_ids: number[];
    created_at: string;
};

type DashboardPageProps = PageProps & {
    auth: { user: DashboardUser };
    savedComparisons: SavedComparison[];
    favorites: any[];
};

const page = usePage<DashboardPageProps>();
const user = computed(() => page.props.auth.user);
const savedComparisons = computed(() => page.props.savedComparisons ?? []);

// Also load guest saved comparisons from localStorage
const guestComparisons = computed(() => {
    if (typeof window === 'undefined') return [];
    const raw = localStorage.getItem('savedComparisons');
    return raw ? JSON.parse(raw) : [];
});

const allComparisons = computed(() => {
    const db = savedComparisons.value.map((c: SavedComparison) => ({
        ...c,
        source: 'database',
    }));
    const guest = guestComparisons.value.map((c: any) => ({
        ...c,
        source: 'local',
    }));
    return [...db, ...guest];
});

const deleteComparison = (id: number, source: string) => {
    if (source === 'local') {
        const stored = JSON.parse(localStorage.getItem('savedComparisons') || '[]');
        const updated = stored.filter((c: any) => c.id !== id);
        localStorage.setItem('savedComparisons', JSON.stringify(updated));
        window.location.reload();
    } else {
        router.delete('/saved-comparisons/' + id, {
            preserveScroll: true,
        });
    }
};

const loadComparison = (comparison: any) => {
    const ids = comparison.phone_ids || [];
    router.get('/compare', { ids: ids.join(',') });
};

const primaryLinks = [
    {
        title: 'Browse Phones',
        description: 'Return to the homepage and continue searching models.',
        href: '/',
        label: 'Open homepage',
        stat: 'Phones',
    },
    {
        title: 'Compare Devices',
        description: 'Review selected phones side by side before buying.',
        href: '/compare',
        label: 'Compare phones',
        stat: 'Specs',
    },
    {
        title: 'Read the Blog',
        description: 'Catch up on reviews, buying guides, and phone news.',
        href: '/blog',
        label: 'Read articles',
        stat: 'Guides',
    },
];
</script>

<template>
    <Head title="Dashboard" />
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />
        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <section class="grid gap-8 border-b border-white/10 pb-10 lg:grid-cols-[1fr_360px]">
                <div>
                    <p class="flex items-center gap-2 text-sm font-semibold text-sky-300">
                        <PhoneIcon :size="16" icon-class="text-sky-400" /> Dashboard
                    </p>
                    <h1 class="mt-3 max-w-3xl text-4xl font-bold leading-tight sm:text-5xl">Welcome back, {{ user.name }}.</h1>
                    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">Use this page as your shortcut back into PhoneHub.</p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <Link href="/" class="rounded-full bg-sky-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-400">Go to homepage</Link>
                        <Link v-if="user.is_admin" href="/admin" class="rounded-full border border-white/15 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white">Open admin panel</Link>
                    </div>
                </div>
                <aside class="rounded-lg border border-white/10 bg-white/[0.04] p-5">
                    <p class="text-sm text-slate-400">Signed in as</p>
                    <p class="mt-2 text-xl font-semibold text-white">{{ user.name }}</p>
                    <p class="mt-1 text-sm text-slate-400">{{ user.email }}</p>
                    <div class="mt-5 border-t border-white/10 pt-5">
                        <p class="text-sm text-slate-400">Account role</p>
                        <p class="mt-1 font-semibold text-sky-200">{{ user.is_admin ? 'Administrator' : 'Member' }}</p>
                    </div>
                </aside>
            </section>

            <!-- Quick Links -->
            <section class="grid gap-5 py-10 md:grid-cols-3">
                <Link v-for="item in primaryLinks" :key="item.href" :href="item.href"
                    class="group rounded-lg border border-white/10 bg-white/[0.04] p-5 transition hover:-translate-y-1 hover:border-sky-300/40 hover:bg-white/[0.06]">
                    <p class="text-sm font-semibold text-sky-300">{{ item.stat }}</p>
                    <h2 class="mt-4 text-xl font-bold text-white">{{ item.title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ item.description }}</p>
                    <p class="mt-5 text-sm font-semibold text-sky-300 transition group-hover:text-sky-200">{{ item.label }}</p>
                </Link>
            </section>

            <!-- Saved Comparisons -->
            <section v-if="allComparisons.length > 0" class="border-t border-white/10 pt-10">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white">Saved Comparisons</h2>
                        <p class="mt-1 text-sm text-slate-400">{{ allComparisons.length }} saved comparison{{ allComparisons.length !== 1 ? 's' : '' }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="comp in allComparisons" :key="comp.id"
                        class="rounded-lg border border-white/10 bg-white/[0.04] p-5 transition hover:border-sky-300/40 hover:bg-white/[0.06]">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-semibold text-white">{{ comp.name }}</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ comp.phone_ids?.length || 0 }} phones &middot; {{ new Date(comp.created_at).toLocaleDateString() }}</p>
                            </div>
                            <span v-if="comp.source === 'local'" class="rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] text-amber-300">Local</span>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <span v-for="pid in (comp.phone_ids || []).slice(0, 3)" :key="pid"
                                class="rounded-full bg-white/[0.06] px-2.5 py-1 text-xs text-slate-300">#{{ pid }}</span>
                        </div>
                        <div class="mt-4 flex items-center gap-3">
                            <button @click="loadComparison(comp)" class="text-sm font-medium text-sky-300 transition hover:text-sky-200">Load &rarr;</button>
                            <button @click="deleteComparison(comp.id, comp.source)" class="text-sm text-slate-500 transition hover:text-red-400">Delete</button>
                        </div>
                    </div>
                </div>
            </section>

            <section v-else class="border-t border-white/10 pt-10">
                <div class="rounded-lg border border-dashed border-white/10 py-14 text-center">
                    <p class="text-3xl">📊</p>
                    <p class="mt-4 text-lg font-semibold text-white">No saved comparisons yet</p>
                    <p class="mt-2 text-sm text-slate-400">Go to the compare page, select phones, and click &quot;Save Comparison&quot;.</p>
                    <Link href="/compare" class="mt-5 inline-block rounded-full bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400">Compare phones</Link>
                </div>
            </section>
        </main>
    </div>
</template>
