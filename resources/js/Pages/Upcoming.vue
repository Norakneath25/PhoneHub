<script setup lang="ts">
import PhoneGrid from '@/Components/Phones/PhoneGrid.vue';
import Footer from '@/Components/Site/Footer.vue';
import Navbar from '@/Components/Site/Navbar.vue';
import type { Phone } from '@/types/Phone';

defineProps<{
    phones: Phone[];
}>();
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />
        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-sm font-semibold text-sky-300">Coming Soon</p>
                <h1 class="mt-3 text-4xl font-bold text-white">
                    Upcoming Phones
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">
                    Phones that have been announced but not yet released. Stay ahead of the curve.
                </p>
            </div>

            <div v-if="phones.length === 0" class="rounded-lg border border-dashed border-white/10 py-20 text-center">
                <p class="text-4xl">📱</p>
                <p class="mt-4 text-lg font-semibold text-white">No upcoming phones yet</p>
                <p class="mt-2 text-sm text-slate-400">Check back later for newly announced devices.</p>
            </div>

            <PhoneGrid v-else :phones="phones" />
        </main>
        <Footer />
    </div>
</template>
