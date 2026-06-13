<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import CompareBar from '@/Components/Phones/CompareBar.vue';
import PhoneGrid from '@/Components/Phones/PhoneGrid.vue';
import Footer from '@/Components/Site/Footer.vue';
import HomeHero from '@/Components/Site/HomeHero.vue';
import Navbar from '@/Components/Site/Navbar.vue';
import type { Phone } from '@/types/Phone';

const props = defineProps<{
    recommended: Phone[];
    latest: Phone[];
    budget: Phone[];
    filters?: {
        q?: string;
        brand?: string;
        ram?: string;
        storage?: string;
        sort?: string;
    };
}>();
</script>

<template>
    <div class="bg-gray-900">
        <Navbar />
        <HomeHero />

        <!-- Recommended -->
        <section class="bg-[#0b0f14] px-4 py-10 text-white sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 flex flex-col justify-between gap-4 border-b border-white/10 pb-6 md:flex-row md:items-end">
                    <div>
                        <p class="text-sm font-semibold text-sky-300">Recommended</p>
                        <h2 class="mt-2 text-3xl font-bold text-white">
                            Top Rated Phones
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-400">
                            Our highest-rated picks for every budget.
                        </p>
                    </div>
                    <Link
                        href="/products?sort=recommended"
                        class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                    >
                        View All →
                    </Link>
                </div>

                <PhoneGrid :phones="recommended" />
            </div>
        </section>

        <!-- Latest -->
        <section class="bg-[#12171e] px-4 py-10 text-white sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 flex flex-col justify-between gap-4 border-b border-white/10 pb-6 md:flex-row md:items-end">
                    <div>
                        <p class="text-sm font-semibold text-emerald-300">Latest</p>
                        <h2 class="mt-2 text-3xl font-bold text-white">
                            Newest Arrivals
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-400">
                            Recently added phones you haven't seen yet.
                        </p>
                    </div>
                    <Link
                        href="/products?sort=latest"
                        class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                    >
                        View All →
                    </Link>
                </div>

                <PhoneGrid :phones="latest" />
            </div>
        </section>

        <!-- Budget -->
        <section class="bg-[#0b0f14] px-4 py-10 text-white sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 flex flex-col justify-between gap-4 border-b border-white/10 pb-6 md:flex-row md:items-end">
                    <div>
                        <p class="text-sm font-semibold text-amber-300">Budget</p>
                        <h2 class="mt-2 text-3xl font-bold text-white">
                            Affordable Phones
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-400">
                            Great phones under $500. Best value for your money.
                        </p>
                    </div>
                    <Link
                        href="/products?sort=price_low"
                        class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                    >
                        View All →
                    </Link>
                </div>

                <PhoneGrid :phones="budget" />
            </div>
        </section>

        <CompareBar />
        <Footer />
    </div>
</template>

<style scoped></style>

