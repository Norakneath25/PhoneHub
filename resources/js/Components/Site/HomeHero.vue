<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const searchQuery = ref('');

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get('/', { q: searchQuery.value }, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <section
        class="border-b border-white/10 bg-[#0b0f14] px-4 py-16 text-white sm:px-6 lg:px-8"
    >
        <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[1fr_440px]">
            <div class="flex flex-col justify-center">
                <div
                    class="mb-6 inline-flex w-fit items-center gap-2 rounded-full border border-sky-300/20 bg-sky-400/10 px-4 py-2 text-sm font-medium text-sky-200"
                >
                    <span class="h-2 w-2 rounded-full bg-sky-300"></span>
                    Updated phone prices and specs
                </div>

                <h1
                    class="max-w-3xl text-5xl font-bold leading-tight text-white sm:text-6xl"
                >
                    Compare phones with specs that actually matter.
                </h1>

                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-300">
                    Search models, compare battery and storage, and open clear
                    phone detail pages before you decide what to buy.
                </p>

                <div
                    class="mt-8 flex max-w-2xl flex-col gap-3 rounded-lg border border-white/10 bg-white/[0.04] p-2 sm:flex-row"
                >
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search iPhone, Galaxy, Pixel..."
                        @keyup.enter="handleSearch"
                        class="min-h-12 flex-1 rounded-md border border-transparent bg-transparent px-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-sky-300/40"
                    />
                    <button
                        @click="handleSearch"
                        class="min-h-12 rounded-md bg-sky-500 px-6 text-sm font-semibold text-white transition hover:bg-sky-400"
                    >
                        Search Phones
                    </button>
                </div>
            </div>

            <div
                class="grid content-end gap-4 border-l border-white/10 pl-0 lg:pl-8"
            >
                <div
                    class="rounded-lg border border-white/10 bg-white/[0.04] p-5"
                >
                    <p class="text-sm text-slate-400">Best for buyers</p>
                    <p class="mt-2 text-3xl font-bold text-white">
                        Filter fast
                    </p>
                    <p class="mt-3 text-sm leading-6 text-slate-400">
                        Narrow options by brand, RAM, storage, price, and
                        release date without leaving the page.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.04] p-4"
                    >
                        <p class="text-2xl font-bold text-white">500+</p>
                        <p class="mt-1 text-sm text-slate-400">Phones</p>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.04] p-4"
                    >
                        <p class="text-2xl font-bold text-white">50+</p>
                        <p class="mt-1 text-sm text-slate-400">Brands</p>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.04] p-4"
                    >
                        <p class="text-2xl font-bold text-white">3</p>
                        <p class="mt-1 text-sm text-slate-400">Compare</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
