<script setup lang="ts">
import { ref, computed } from 'vue';
import { useSearchStore } from '@/stores/searchStore';
import type { Phone } from '@/types/Phone';
import PhoneCards from '../components/Card.vue';

const props = defineProps<{
    phones: Phone[];
}>();

const phones = ref<Phone[]>(props.phones);
const selectedBrand = ref('All');
const selectedRAM = ref('All');
const selectedStorage = ref('All');
const sortBy = ref('recommended');
const searchStore = useSearchStore();

const brands = computed(() => {
    const unique = [...new Set(phones.value.map((p) => p.brand))];
    return ['All', ...unique];
});

const rams = computed(() => {
    const unique = [...new Set(phones.value.map((p) => p.ram))];
    return ['All', ...unique.sort()];
});

const storages = computed(() => {
    const unique = [...new Set(phones.value.map((p) => p.storage))];
    return ['All', ...unique.sort()];
});

const filteredPhones = computed(() => {
    let result = phones.value;

    if (selectedBrand.value !== 'All') {
        result = result.filter((p) => p.brand === selectedBrand.value);
    }

    if (selectedRAM.value !== 'All') {
        result = result.filter((p) => p.ram === selectedRAM.value);
    }

    if (selectedStorage.value !== 'All') {
        result = result.filter((p) => p.storage === selectedStorage.value);
    }

    if (searchStore.query) {
        result = result.filter(
            (p) =>
                p.brand.toLowerCase().includes(searchStore.query.toLowerCase()) ||
                p.model.toLowerCase().includes(searchStore.query.toLowerCase()),
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
            (a, b) => new Date(b.release_date).getTime() - new Date(a.release_date).getTime(),
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
    <div class="container mx-auto">

        <!-- Brand Filter -->
        <div class="mb-4">
            <p class="text-xs text-gray-500 uppercase mb-2">Brand</p>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="brand in brands"
                    :key="brand"
                    @click="selectedBrand = brand"
                    :class="selectedBrand === brand ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'"
                    class="rounded-full px-4 py-1.5 text-sm transition-colors"
                >
                    {{ brand }}
                </button>
            </div>
        </div>

        <!-- RAM Filter -->
        <div class="mb-4">
            <p class="text-xs text-gray-500 uppercase mb-2">RAM</p>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="ram in rams"
                    :key="ram"
                    @click="selectedRAM = ram"
                    :class="selectedRAM === ram ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'"
                    class="rounded-full px-4 py-1.5 text-sm transition-colors"
                >
                    {{ ram }}
                </button>
            </div>
        </div>

        <!-- Storage Filter -->
        <div class="mb-4">
            <p class="text-xs text-gray-500 uppercase mb-2">Storage</p>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="storage in storages"
                    :key="storage"
                    @click="selectedStorage = storage"
                    :class="selectedStorage === storage ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'"
                    class="rounded-full px-4 py-1.5 text-sm transition-colors"
                >
                    {{ storage }}
                </button>
            </div>
        </div>

        <!-- Sort -->
        <div class="mb-8">
            <p class="text-xs text-gray-500 uppercase mb-2">Sort By</p>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="sort in [
                        { key: 'recommended', label: 'Recommended' },
                        { key: 'latest', label: 'Latest' },
                        { key: 'price_low', label: 'Price: Low to High' },
                        { key: 'price_high', label: 'Price: High to Low' },
                    ]"
                    :key="sort.key"
                    @click="sortBy = sort.key"
                    :class="sortBy === sort.key ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'"
                    class="rounded-full px-4 py-1.5 text-sm transition-colors"
                >
                    {{ sort.label }}
                </button>
            </div>
        </div>

    </div>

    <!-- Pass filtered phones to PhoneCards -->
    <PhoneCards :phones="sortedPhones" />
</template>
