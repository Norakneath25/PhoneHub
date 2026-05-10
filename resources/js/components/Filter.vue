<script setup lang="ts">
import { ref, computed } from 'vue';
import { useSearchStore } from '@/stores/searchStore';
import type { Phone } from '@/types/Phone';
import PhoneCards from '../components/Card.vue';

// ✅ defineProps outside onMounted
const props = defineProps<{
    phones: Phone[];
}>();

const phones = ref<Phone[]>(props.phones); // ← initialize with props
const selectedBrand = ref('All');
const searchStore = useSearchStore();

const brands = computed(() => {
    const unique = [...new Set(phones.value.map((p) => p.brand))];
    return ['All', ...unique];
});

const filteredPhones = computed(() => {
    let result = phones.value;
    if (selectedBrand.value !== 'All') {
        result = result.filter((p) => p.brand === selectedBrand.value);
    }
    if (searchStore.query) {
        result = result.filter(
            (p) =>
                p.brand
                    .toLowerCase()
                    .includes(searchStore.query.toLowerCase()) ||
                p.model.toLowerCase().includes(searchStore.query.toLowerCase()),
        );
    }
    return result;
});

const sortBy = ref('latest');
const sortedPhones = computed(() => {
    const filtered = filteredPhones.value;
    if (sortBy.value === 'latest') {
        return [...filtered].sort(
            (a, b) =>
                new Date(b.release_date).getTime() -
                new Date(a.release_date).getTime(),
        );
    }
    if (sortBy.value === 'price') {
        return [...filtered].sort((a, b) => a.price - b.price);
    }
    if (sortBy.value === 'rating') {
        return [...filtered].sort((a, b) => b.rating - a.rating);
    }
    return filtered;
});
</script>

<template>
    <div class="container mx-auto">
        <!-- Brand Filter -->
        <div class="mb-8 flex flex-wrap gap-2">
            <button
                v-for="brand in brands"
                :key="brand"
                @click="selectedBrand = brand"
                :class="
                    selectedBrand === brand
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
                "
                class="rounded-full px-4 py-1.5 text-sm transition-colors"
            >
                {{ brand }}
            </button>
        </div>

        <div class="mb-4 flex gap-2">
            <button
                v-for="sort in ['latest', 'price', 'rating']"
                :key="sort"
                @click="sortBy = sort"
                :class="
                    sortBy === sort
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-800 text-gray-400'
                "
                class="rounded-full px-4 py-1.5 text-sm capitalize transition-colors"
            >
                {{ sort }}
            </button>
        </div>
    </div>

    <!-- Pass filtered phones to PhoneCards -->
    <PhoneCards :phones="sortedPhones" />
</template>
