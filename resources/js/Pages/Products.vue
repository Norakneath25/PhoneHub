<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Navbar from '@/Components/Site/Navbar.vue';
import Footer from '@/Components/Site/Footer.vue';
import PhoneGrid from '@/Components/Phones/PhoneGrid.vue';
import CompareBar from '@/Components/Phones/CompareBar.vue';
import type { Phone } from '@/types/Phone';

const props = defineProps<{
    phones: Phone[];
    filters?: {
        q?: string;
        brand?: string;
        ram?: string;
        storage?: string;
        sorts?: string;
    };
}>();

const currentFilters = ref({
    q: props.filters?.q ?? '',
    brand: props.filters?.brand ?? 'All',
    ram: props.filters?.ram ?? 'All',
    storage: props.filters?.storage ?? 'All',
});

const activeSorts = ref(new Set<string>(
    props.filters?.sorts ? props.filters.sorts.split(',').filter(Boolean) : ['recommended']
));

const sortOptions = [
    { key: 'recommended', label: 'Recommended' },
    { key: 'latest', label: 'Latest' },
    { key: 'price_low', label: 'Price: Low to High' },
    { key: 'price_high', label: 'Price: High to Low' },
];

const brands = computed(() => ['All', ...new Set(props.phones.map((p) => p.brand))]);
const rams = computed(() => ['All', ...[...new Set(props.phones.map((p) => p.ram))].sort()]);

const toggleSort = (key: string) => {
    // If it's a price sort, remove the opposite price sort
    if (key === 'price_low') activeSorts.value.delete('price_high');
    if (key === 'price_high') activeSorts.value.delete('price_low');

    if (activeSorts.value.has(key)) {
        activeSorts.value.delete(key);
    } else {
        activeSorts.value.add(key);
    }
    if (activeSorts.value.size === 0) {
        activeSorts.value.add('recommended');
    }
    sendFilters();
};

const applyFilter = (key: string, value: string) => {
    currentFilters.value = { ...currentFilters.value, [key]: value };
    sendFilters();
};

const sendFilters = () => {
    const sorts = [...activeSorts.value].join(',');
    router.get('/products', { ...currentFilters.value, sorts }, { preserveState: true, preserveScroll: true });
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />
        
        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col justify-between gap-4 border-b border-white/10 pb-6 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-semibold text-sky-300">All Products</p>
                    <h1 class="mt-2 text-3xl font-bold text-white">
                        Browse All Phones
                    </h1>
                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        Showing {{ phones.length }} phones. Filter and sort to find yours.
                    </p>
                </div>
            </div>

            <div class="mb-8 grid gap-5 lg:grid-cols-4">
                <div>
                    <p class="mb-3 text-xs font-semibold text-slate-500">SORT BY</p>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="s in sortOptions" :key="s.key"
                            @click="toggleSort(s.key)"
                            :class="['rounded-full px-4 py-2 text-sm font-medium transition',
                                activeSorts.has(s.key)
                                    ? 'bg-white text-slate-950'
                                    : 'bg-white/[0.05] text-slate-300 hover:bg-white/10 hover:text-white']"
                        >{{ s.label }}</button>
                    </div>
                </div>

                <div>
                    <p class="mb-3 text-xs font-semibold text-slate-500">BRAND</p>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="brand in brands" :key="brand"
                            @click="applyFilter('brand', brand)"
                            :class="['rounded-full px-4 py-2 text-sm font-medium transition',
                                (currentFilters.brand) === brand
                                    ? 'bg-white text-slate-950'
                                    : 'bg-white/[0.05] text-slate-300 hover:bg-white/10 hover:text-white']"
                        >{{ brand }}</button>
                    </div>
                </div>

                <div>
                    <p class="mb-3 text-xs font-semibold text-slate-500">RAM</p>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="ram in rams" :key="ram"
                            @click="applyFilter('ram', ram)"
                            :class="['rounded-full px-4 py-2 text-sm font-medium transition',
                                (currentFilters.ram) === ram
                                    ? 'bg-white text-slate-950'
                                    : 'bg-white/[0.05] text-slate-300 hover:bg-white/10 hover:text-white']"
                        >{{ ram }}</button>
                    </div>
                </div>

                <div>
                    <p class="mb-3 text-xs font-semibold text-slate-500">SEARCH</p>
                    <input type="text" :value="currentFilters.q"
                        @input="applyFilter('q', ($event.target as HTMLInputElement).value)"
                        placeholder="Search by name..."
                        class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-sky-300/50"
                    />
                </div>
            </div>

            <PhoneGrid :phones="phones" />
        </main>

        <CompareBar />
        <Footer />
    </div>
</template>

