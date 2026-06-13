<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PhoneGrid from '@/Components/Phones/PhoneGrid.vue';
import Footer from '@/Components/Site/Footer.vue';
import Navbar from '@/Components/Site/Navbar.vue';
import type { Phone } from '@/types/Phone';

defineProps<{
    thisMonth: Phone[];
    nextQuarter: Phone[];
    later: Phone[];
}>();

const formatDate = (date: string | null) => {
    if (!date) return 'TBA';
    const d = new Date(date);
    return d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
};

const daysUntil = (date: string | null) => {
    if (!date) return null;
    const now = new Date();
    const target = new Date(date);
    const diff = Math.ceil((target.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
    return diff > 0 ? diff : null;
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />

        <!-- Hero -->
        <div class="relative overflow-hidden border-b border-white/10 bg-gradient-to-b from-sky-950/20 to-[#0b0f14]">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <p class="text-sm font-semibold text-sky-300">Coming Soon</p>
                <h1 class="mt-3 text-5xl font-bold text-white">
                    Upcoming Phones
                </h1>
                <p class="mt-3 max-w-2xl text-base leading-7 text-slate-400">
                    The future of mobile technology. Browse upcoming releases and stay ahead of the curve.
                </p>
            </div>
        </div>

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- This Month -->
            <section class="mb-14">
                <div class="mb-6 flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-500/20 text-sm">🔥</span>
                    <div>
                        <h2 class="text-xl font-bold text-white">Releasing This Month</h2>
                        <p class="text-sm text-slate-500">Launching within the next 30 days</p>
                    </div>
                </div>
                <PhoneGrid v-if="thisMonth.length" :phones="thisMonth" />
                <div v-else class="rounded-lg border border-dashed border-white/10 py-12 text-center">
                    <p class="text-slate-500">No phones releasing this month</p>
                </div>
            </section>

            <!-- Next Quarter -->
            <section class="mb-14">
                <div class="mb-6 flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/20 text-sm">📅</span>
                    <div>
                        <h2 class="text-xl font-bold text-white">Next Quarter</h2>
                        <p class="text-sm text-slate-500">Expected in the coming months</p>
                    </div>
                </div>
                <PhoneGrid v-if="nextQuarter.length" :phones="nextQuarter" />
                <div v-else class="rounded-lg border border-dashed border-white/10 py-12 text-center">
                    <p class="text-slate-500">No phones scheduled for next quarter</p>
                </div>
            </section>

            <!-- Later / TBA -->
            <section class="mb-14">
                <div class="mb-6 flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500/20 text-sm">🔮</span>
                    <div>
                        <h2 class="text-xl font-bold text-white">Announced / TBA</h2>
                        <p class="text-sm text-slate-500">Confirmed but release date not yet set</p>
                    </div>
                </div>
                <PhoneGrid v-if="later.length" :phones="later" />
                <div v-else class="rounded-lg border border-dashed border-white/10 py-12 text-center">
                    <p class="text-slate-500">No announced phones yet</p>
                </div>
            </section>

            <div class="text-center">
                <Link href="/products" class="rounded-full bg-white/[0.05] px-6 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    Browse all available phones →
                </Link>
            </div>
        </main>

        <Footer />
    </div>
</template>

