<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Phone } from '@/types/Phone';

defineProps<{
    phones: Phone[];
}>();

// Use localStorage for favorites display
const stored = JSON.parse(localStorage.getItem('favorites') || '[]');
const favoriteIds = ref<number[]>(stored);

const addToCompare = (phone: Phone) => {
    const url = new URL(window.location.href);
    const existing = url.searchParams.get('compare') || '';
    const ids = existing ? existing.split(',').filter(Boolean).map(Number) : [];
    if (ids.includes(phone.id) || ids.length >= 3) return;
    ids.push(phone.id);
    router.get('/compare', { ids: ids.join(',') });
};

const toggleFavorite = (phoneId: number) => {
    const index = favoriteIds.value.indexOf(phoneId);
    if (index === -1) {
        favoriteIds.value.push(phoneId);
    } else {
        favoriteIds.value.splice(index, 1);
    }
    localStorage.setItem('favorites', JSON.stringify(favoriteIds.value));
};

const isFav = (phoneId: number) => favoriteIds.value.includes(phoneId);
</script>

<template>
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <article v-for="phone in phones" :key="phone.id"
            class="group overflow-hidden rounded-lg border border-white/10 bg-white/[0.04] transition hover:-translate-y-1 hover:border-sky-300/40 hover:bg-white/[0.06]">
            <div class="relative">
                <a :href="`/phones/${phone.id}`">
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-900">
                        <img :src="phone.image" :alt="phone.model"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                        <div class="absolute left-3 top-3 rounded-full bg-black/55 px-3 py-1 text-xs font-semibold text-white backdrop-blur">
                            {{ phone.brand }}
                        </div>
                    </div>
                </a>
                <button @click="toggleFavorite(phone.id)"
                    class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full text-lg backdrop-blur transition hover:scale-110"
                    :class="isFav(phone.id)
                        ? 'bg-red-600/40 text-red-400 shadow-lg shadow-red-500/30'
                        : 'bg-black/50 text-white'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                        <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                    </svg>
                </button>
            </div>

            <div class="p-4">
                <h3 class="min-h-12 font-semibold leading-6 text-white">{{ phone.model }}</h3>

                <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-md bg-white/[0.04] p-3">
                        <p class="text-slate-500">RAM</p>
                        <p class="mt-1 font-semibold text-slate-200">{{ phone.ram }}</p>
                    </div>
                    <div class="rounded-md bg-white/[0.04] p-3">
                        <p class="text-slate-500">Storage</p>
                        <p class="mt-1 font-semibold text-slate-200">{{ phone.storage }}</p>
                    </div>
                    <div class="rounded-md bg-white/[0.04] p-3">
                        <p class="text-slate-500">Battery</p>
                        <p class="mt-1 font-semibold text-slate-200">{{ phone.battery }}</p>
                    </div>
                    <div class="rounded-md bg-white/[0.04] p-3">
                        <p class="text-slate-500">Rating</p>
                        <p class="mt-1 font-semibold text-slate-200">{{ phone.rating || 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between">
                    <p class="text-xl font-bold text-sky-300">${{ phone.price }}</p>
                    <a :href="`/phones/${phone.id}`"
                        class="rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-400">View</a>
                </div>

                <button @click="addToCompare(phone)"
                    class="mt-3 w-full rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-white/25 hover:bg-white/10 hover:text-white">
                    Compare
                </button>
            </div>
        </article>
    </div>
</template>
