<script setup lang="ts">
import type { PageProps } from '@inertiajs/core';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Navbar from '@/Components/Site/Navbar.vue';

type DashboardUser = {
    name: string;
    email: string;
    is_admin?: boolean;
};

type DashboardPageProps = PageProps & {
    auth: {
        user: DashboardUser;
    };
};

const page = usePage<DashboardPageProps>();
const user = computed(() => page.props.auth.user);

const primaryLinks = computed(() => [
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
]);
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <section
                class="grid gap-8 border-b border-white/10 pb-10 lg:grid-cols-[1fr_360px]"
            >
                <div>
                    <p class="text-sm font-semibold text-sky-300">Dashboard</p>
                    <h1
                        class="mt-3 max-w-3xl text-4xl font-bold leading-tight sm:text-5xl"
                    >
                        Welcome back, {{ user.name }}.
                    </h1>
                    <p
                        class="mt-4 max-w-2xl text-base leading-7 text-slate-300"
                    >
                        Use this page as your shortcut back into PhoneHub. No
                        need to edit the URL after logging in.
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <Link
                            href="/"
                            class="rounded-full bg-sky-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-400"
                        >
                            Go to homepage
                        </Link>
                        <Link
                            v-if="user.is_admin"
                            href="/admin"
                            class="rounded-full border border-white/15 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                        >
                            Open admin panel
                        </Link>
                    </div>
                </div>

                <aside
                    class="rounded-lg border border-white/10 bg-white/[0.04] p-5"
                >
                    <p class="text-sm text-slate-400">Signed in as</p>
                    <p class="mt-2 text-xl font-semibold text-white">
                        {{ user.name }}
                    </p>
                    <p class="mt-1 text-sm text-slate-400">{{ user.email }}</p>
                    <div class="mt-5 border-t border-white/10 pt-5">
                        <p class="text-sm text-slate-400">Account role</p>
                        <p class="mt-1 font-semibold text-sky-200">
                            {{ user.is_admin ? 'Administrator' : 'Member' }}
                        </p>
                    </div>
                </aside>
            </section>

            <section class="grid gap-5 py-10 md:grid-cols-3">
                <Link
                    v-for="item in primaryLinks"
                    :key="item.href"
                    :href="item.href"
                    class="group rounded-lg border border-white/10 bg-white/[0.04] p-5 transition hover:-translate-y-1 hover:border-sky-300/40 hover:bg-white/[0.06]"
                >
                    <p class="text-sm font-semibold text-sky-300">
                        {{ item.stat }}
                    </p>
                    <h2 class="mt-4 text-xl font-bold text-white">
                        {{ item.title }}
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">
                        {{ item.description }}
                    </p>
                    <p
                        class="mt-5 text-sm font-semibold text-sky-300 transition group-hover:text-sky-200"
                    >
                        {{ item.label }}
                    </p>
                </Link>
            </section>
        </main>
    </div>
</template>
