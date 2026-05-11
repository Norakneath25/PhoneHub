<template>
    <section class="bg-gray-900 text-white py-28">
        <div class="max-w-6xl mx-auto px-6 text-center">

            <!-- Badge -->
            <div class="inline-flex items-center gap-2 bg-gray-800 text-gray-400 text-sm px-4 py-1.5 rounded-full mb-6 border border-gray-700">
                <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                Updated daily with latest prices
            </div>

            <!-- Title -->
            <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                Find Your Perfect
                <span class="text-blue-400"> Phone</span>
            </h1>

            <!-- Subtitle -->
            <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-10">
                Discover specs, compare models, and find the best prices across stores — all in one place.
            </p>

            <!-- Search Bar -->
            <div class="flex items-center max-w-xl mx-auto mb-10">
                <input
                    type="text"
                    :value="searchStore.query"
                    @input="(e) => searchStore.setQuery((e.target as HTMLInputElement).value)"
                    placeholder="Search phones..."
                    class="w-full rounded-l-xl bg-gray-800 border border-gray-700 px-5 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button class="bg-blue-600 hover:bg-blue-500 px-6 py-3 rounded-r-xl text-sm font-medium transition-colors">
                    Search
                </button>
            </div>


            <!-- Stats -->
            <div class="flex justify-center gap-12 mt-16">
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
</script>
