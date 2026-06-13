<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import Navbar from '@/Components/Site/Navbar.vue';
import Footer from '@/Components/Site/Footer.vue';
import PhoneGrid from '@/Components/Phones/PhoneGrid.vue';
import type { Phone } from '@/types/Phone';

const props = defineProps<{
    phones: Phone[];
}>();

const localPhones = ref<Phone[]>([]);
const loading = ref(true);

onMounted(async () => {
    const favIds: number[] = JSON.parse(localStorage.getItem('favorites') || '[]');

    if (props.phones.length > 0) {
        // Backend already gave us phones (logged in user)
        localPhones.value = props.phones;
    } else if (favIds.length > 0) {
        // Guest: fetch phones by IDs from JSON API
        try {
            const res = await fetch('/api/phones-by-ids', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids: favIds }),
            });
            const data = await res.json();
            localPhones.value = data.phones || [];
        } catch (e) {
            localPhones.value = [];
        }
    }
    loading.value = false;
});

const clearFavorites = () => {
    if (confirm('Remove all favorites?')) {
        localStorage.setItem('favorites', '[]');
        localPhones.value = [];
    }
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />
        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-semibold text-sky-300">My Favorites</p>
                    <h1 class="mt-3 text-4xl font-bold text-white">Saved Phones</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">
                        Phones you've saved to compare or buy later.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button v-if="localPhones.length > 0" @click="clearFavorites"
                        class="rounded-full border border-white/10 px-4 py-2.5 text-sm text-slate-400 transition hover:text-red-400">
                        Clear all
                    </button>
                    <Link href="/"
                        class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white">
                        Browse all phones
                    </Link>
                </div>
            </div>

            <div v-if="loading" class="rounded-lg border border-dashed border-white/10 py-20 text-center">
                <p class="text-slate-400">Loading...</p>
            </div>

            <div v-else-if="localPhones.length === 0" class="rounded-lg border border-dashed border-white/10 py-20 text-center">
                <p class="text-4xl">❤️</p>
                <p class="mt-4 text-lg font-semibold text-white">No favorites yet</p>
                <p class="mt-2 text-sm text-slate-400">
                    Browse phones and click the heart icon to save them here.
                </p>
                <Link href="/"
                    class="mt-6 inline-block rounded-full bg-sky-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-sky-400">
                    Browse phones
                </Link>
            </div>

            <PhoneGrid v-else :phones="localPhones" />
        </main>
        <Footer />
    </div>
</template>
