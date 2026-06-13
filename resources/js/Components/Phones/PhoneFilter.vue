<script setup lang="ts">
import { computed } from 'vue';
import type { Phone } from '@/types/Phone';
import PhoneGrid from './PhoneGrid.vue';

const props = defineProps<{
    phones: Phone[];
    filters?: {
        q?: string;
        brand?: string;
        ram?: string;
        storage?: string;
        sort?: string;
    };
}>();

const emit = defineEmits<{
    applyFilters: [filters: { q?: string; brand?: string; ram?: string; storage?: string; sort?: string }];
}>();

const sortOptions = [
    { key: 'recommended', label: 'Recommended' },
    { key: 'latest', label: 'Latest' },
    { key: 'price_low', label: 'Price: Low to High' },
    { key: 'price_high', label: 'Price: High to Low' },
];

const brands = computed(() => {
    // Unique brands from ALL available phones (not filtered ones)
    // This is fine to keep on frontend since it's just for UI display
    const unique = [...new Set(props.phones.map((phone) => phone.brand))];
    return ['All', ...unique];
});

const rams = computed(() => {
    const unique = [...new Set(props.phones.map((phone) => phone.ram))];
    return ['All', ...unique.sort()];
});

const applyFilter = (key: string, value: string) => {
    emit('applyFilters', {
        ...props.filters,
        [key]: value,
    });
};
</script>

<template>
    <section class="bg-[#0b0f14] px-4 py-10 text-white sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div
                class="mb-6 flex flex-col justify-between gap-4 border-b border-white/10 pb-6 md:flex-row md:items-end"
            >
                <div>
                    <p class="text-sm font-semibold text-sky-300">
                        Phone Finder
                    </p>
                    <h2 class="mt-2 text-3xl font-bold text-white">
                        Browse the current lineup
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">
                        Showing {{ phones.length }} phones.
                    </p>
                </div>

                <select
                    :value="filters?.sort ?? 'recommended'"
                    @change="applyFilter('sort', ($event.target as HTMLSelectElement).value)"
                    class="h-11 rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50"
                >
                    <option
                        v-for="sort in sortOptions"
                        :key="sort.key"
                        :value="sort.key"
                        class="bg-slate-950"
                    >
                        {{ sort.label }}
                    </option>
                </select>
            </div>

            <div class="mb-8 grid gap-5 lg:grid-cols-3">
                <div>
                    <p class="mb-3 text-xs font-semibold text-slate-500">
                        BRAND
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="brand in brands"
                            :key="brand"
                            @click="applyFilter('brand', brand)"
                            :class="[
                                'rounded-full px-4 py-2 text-sm font-medium transition',
                                (filters?.brand ?? 'All') === brand
                                    ? 'bg-white text-slate-950'
                                    : 'bg-white/[0.05] text-slate-300 hover:bg-white/10 hover:text-white',
                            ]"
                        >
                            {{ brand }}
                        </button>
                    </div>
                </div>

                <div>
                    <p class="mb-3 text-xs font-semibold text-slate-500">RAM</p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="ram in rams"
                            :key="ram"
                            @click="applyFilter('ram', ram)"
                            :class="[
                                'rounded-full px-4 py-2 text-sm font-medium transition',
                                (filters?.ram ?? 'All') === ram
                                    ? 'bg-white text-slate-950'
                                    : 'bg-white/[0.05] text-slate-300 hover:bg-white/10 hover:text-white',
                            ]"
                        >
                            {{ ram }}
                        </button>
                    </div>
                </div>

                <div>
                    <p class="mb-3 text-xs font-semibold text-slate-500">
                        STORAGE
                    </p>
                    <div class="max-w-xs">
                        <select
                            :value="filters?.storage ?? 'All'"
                            @change="applyFilter('storage', ($event.target as HTMLSelectElement).value)"
                            class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50"
                        >
                            <option value="All" class="bg-slate-950">
                                All storage options
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <PhoneGrid :phones="phones" />
    </section>
</template>
