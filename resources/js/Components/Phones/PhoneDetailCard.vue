<template>
    <div class="pt-20 pb-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <a href="/" class="mb-8 inline-flex items-center gap-2 text-sm text-gray-400 transition-colors hover:text-white">
                ← Back to phones
            </a>

            <div class="mt-6 flex flex-col gap-10 md:flex-row">
                <div class="md:w-1/2">
                    <div class="relative h-[500px] overflow-hidden rounded-2xl">
                        <img class="h-full w-full object-cover" :src="phone.image" :alt="phone.model" />
                        <button @click="toggleFavorite(phone.id)"
                            class="absolute right-4 top-4 flex h-12 w-12 items-center justify-center rounded-full text-xl backdrop-blur transition hover:scale-110"
                            :class="isFav(phone.id)
                                ? 'bg-red-600/40 text-red-400 shadow-lg shadow-red-500/30'
                                : 'bg-black/50 text-white'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                            </svg>
                        </button>
                    </div>
                    <a :href="'/compare?ids=' + phone.id"
                        class="mt-4 flex w-full items-center justify-center rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 font-medium text-white transition-colors hover:bg-gray-700">
                        + Add to Compare List
                    </a>
                </div>

                <div class="flex flex-col justify-center md:w-1/2">
                    <p class="mb-2 text-xs tracking-widest text-blue-400 uppercase">{{ phone.brand }}</p>
                    <h1 class="mb-3 text-4xl font-bold text-white">{{ phone.model }}</h1>
                    <p class="mb-8 text-3xl font-bold text-blue-400">${{ phone.price }}</p>

                    <div class="mb-8 grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">RAM</p>
                            <p class="font-medium text-white">{{ phone.ram }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">Storage</p>
                            <p class="font-medium text-white">{{ phone.storage }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">Camera</p>
                            <p class="font-medium text-white">{{ phone.camera }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">Battery</p>
                            <p class="font-medium text-white">{{ phone.battery }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">Screen</p>
                            <p class="font-medium text-white">{{ phone.screen_size }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">OS</p>
                            <p class="font-medium text-white">{{ phone.os }}</p>
                        </div>
                    </div>

                    <a :href="phone.store_url" target="_blank"
                        class="block rounded-xl bg-blue-600 py-3 text-center font-medium text-white transition-colors hover:bg-blue-500">
                        View Store →
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { Phone } from '@/types/Phone';

defineProps<{
    phone: Phone;
}>();

const stored = JSON.parse(localStorage.getItem('favorites') || '[]');
const favoriteIds = ref<number[]>(stored);

const isFav = (id: number) => favoriteIds.value.includes(id);

const toggleFavorite = (phoneId: number) => {
    const index = favoriteIds.value.indexOf(phoneId);
    if (index === -1) {
        favoriteIds.value.push(phoneId);
    } else {
        favoriteIds.value.splice(index, 1);
    }
    localStorage.setItem('favorites', JSON.stringify(favoriteIds.value));
};
</script>
