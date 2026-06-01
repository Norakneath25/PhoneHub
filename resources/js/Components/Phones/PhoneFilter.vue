<script setup lang="ts">
import { computed, ref } from 'vue';
import { useSearchStore } from '@/stores/searchStore';
import type { Phone } from '@/types/Phone';
import PhoneGrid from './PhoneGrid.vue';

const props = defineProps<{
    phones: Phone[];
}>();

const phones = ref<Phone[]>(props.phones);
const selectedBrand = ref('All');
const selectedRAM = ref('All');
const selectedStorage = ref('All');
const sortBy = ref('recommended');
const searchStore = useSearchStore();

const sortOptions = [
    { key: 'recommended', label: 'Recommended' },
    { key: 'latest', label: 'Latest' },
    { key: 'price_low', label: 'Price: Low to High' },
    { key: 'price_high', label: 'Price: High to Low' },
];

const storageRank = (storage: string) => {
    const match = storage.match(/^(\d+(?:\.\d+)?)(GB|TB)$/i);

    if (!match) {
        return Number.MAX_SAFE_INTEGER;
    }

    const value = Number(match[1]);
    const unit = match[2].toUpperCase();

    return unit === 'TB' ? value * 1024 : value;
};

const storageOptionsForPhone = (storage: string) => {
    const matches = storage
        .toUpperCase()
        .matchAll(/(\d+(?:\.\d+)?)\s*(GB|TB)(?!\s*RAM)/g);

    return [...matches]
        .map((match) => {
            const value = Number(match[1]);
            const unit = match[2];

            if (unit === 'GB' && value < 32) {
                return null;
            }

            return `${match[1]}${unit}`;
        })
        .filter((option): option is string => option !== null);
};

const brands = computed(() => {
    const unique = [...new Set(phones.value.map((phone) => phone.brand))];

    return ['All', ...unique];
});

const rams = computed(() => {
    const unique = [...new Set(phones.value.map((phone) => phone.ram))];

    return ['All', ...unique.sort()];
});

const storages = computed(() => {
    const unique = [
        ...new Set(
            phones.value.flatMap((phone) =>
                storageOptionsForPhone(phone.storage),
            ),
        ),
    ];

    return ['All', ...unique.sort((a, b) => storageRank(a) - storageRank(b))];
});

const filteredPhones = computed(() => {
    let result = phones.value;

    if (selectedBrand.value !== 'All') {
        result = result.filter((phone) => phone.brand === selectedBrand.value);
    }

    if (selectedRAM.value !== 'All') {
        result = result.filter((phone) => phone.ram === selectedRAM.value);
    }

    if (selectedStorage.value !== 'All') {
        result = result.filter((phone) =>
            storageOptionsForPhone(phone.storage).includes(
                selectedStorage.value,
            ),
        );
    }

    if (searchStore.query) {
        const query = searchStore.query.toLowerCase();

        result = result.filter(
            (phone) =>
                phone.brand.toLowerCase().includes(query) ||
                phone.model.toLowerCase().includes(query),
        );
    }

    return result;
});

const sortedPhones = computed(() => {
    const filtered = filteredPhones.value;

    if (sortBy.value === 'recommended') {
        return [...filtered].sort((a, b) => b.rating - a.rating);
    }

    if (sortBy.value === 'latest') {
        return [...filtered].sort(
            (a, b) =>
                new Date(b.release_date).getTime() -
                new Date(a.release_date).getTime(),
        );
    }

    if (sortBy.value === 'price_low') {
        return [...filtered].sort((a, b) => a.price - b.price);
    }

    if (sortBy.value === 'price_high') {
        return [...filtered].sort((a, b) => b.price - a.price);
    }

    return filtered;
});
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
                        Showing {{ sortedPhones.length }} of
                        {{ phones.length }} phones.
                    </p>
                </div>

                <select
                    v-model="sortBy"
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
                            @click="selectedBrand = brand"
                            :class="[
                                'rounded-full px-4 py-2 text-sm font-medium transition',
                                selectedBrand === brand
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
                            @click="selectedRAM = ram"
                            :class="[
                                'rounded-full px-4 py-2 text-sm font-medium transition',
                                selectedRAM === ram
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
                            v-model="selectedStorage"
                            class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50"
                        >
                            <option
                                v-for="storage in storages"
                                :key="storage"
                                :value="storage"
                                class="bg-slate-950"
                            >
                                {{
                                    storage === 'All'
                                        ? 'All storage options'
                                        : storage
                                }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <PhoneGrid :phones="sortedPhones" />
    </section>
</template>
