<template>
    <section class="bg-gray-900 py-28 text-white">
        <div class="mx-auto max-w-6xl px-6 text-center">
            <!-- Badge -->
            <div
                class="mb-6 inline-flex items-center gap-2 rounded-full border border-gray-700 bg-gray-800 px-4 py-1.5 text-sm text-gray-400"
            >
                <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                Updated daily with latest prices
            </div>

            <!-- Title -->
            <h1 class="mb-6 text-5xl font-bold leading-tight md:text-6xl">
                Find Your Perfect
                <span class="text-blue-400"> Phone</span>
            </h1>

            <!-- Subtitle -->
            <p class="mx-auto mb-10 max-w-2xl text-lg text-gray-400">
                Discover specs, compare models, and find the best prices across
                stores — all in one place.
            </p>

            <!-- Search Bar -->
            <div class="mx-auto mb-10 flex max-w-xl items-center">
                <input
                    type="text"
                    :value="searchStore.query"
                    @input="
                        (e) =>
                            searchStore.setQuery(
                                (e.target as HTMLInputElement).value,
                            )
                    "
                    placeholder="Search phones..."
                    class="w-full rounded-l-xl border border-gray-700 bg-gray-800 px-5 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button
                    @click="handleSearch"
                    class="rounded-r-xl bg-blue-600 px-6 py-3 text-sm font-medium transition-colors hover:bg-blue-500"
                >
                    Search
                </button>
            </div>

            <!-- Stats -->
            <div class="mt-16 flex justify-center gap-12">
                <div>
                    <p class="text-2xl font-bold">500+</p>
                    <p class="text-sm text-gray-500">Phones</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">50+</p>
                    <p class="text-sm text-gray-500">Brands</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">100+</p>
                    <p class="text-sm text-gray-500">Stores</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useSearchStore } from '@/stores/searchStore';

const searchStore = useSearchStore();

const handleSearch = () => {
    const section = document.getElementById('phones-section');
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
};
</script>
