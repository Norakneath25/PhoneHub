<!-- PhoneCards.vue -->
<script setup lang="ts">
import { useCompareStore } from '@/stores/compareStore';
import type { Phone } from '@/types/Phone';

const compareStore = useCompareStore();

defineProps<{
    phones: Phone[];
}>();

const isInCompare = (phone: Phone) => {
    return compareStore.phones.some((p) => p.id === phone.id);
};
</script>

<template>
    <div
        class="container mx-auto grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
    >
        <div
            v-for="phone in phones"
            :key="phone.id"
            class="overflow-hidden rounded-xl bg-gray-800 transition-all hover:ring-1 hover:ring-blue-500"
        >
            <img
                :src="phone.image"
                :alt="phone.model"
                class="h-48 w-full object-cover"
            />
            <div class="p-4">
                <p class="text-xs tracking-wide text-gray-500 uppercase">
                    {{ phone.brand }}
                </p>
                <h3 class="mt-0.5 mb-3 font-semibold text-white">
                    {{ phone.model }}
                </h3>
                <div class="mb-4 space-y-1 text-xs text-gray-400">
                    <p>{{ phone.ram }} RAM · {{ phone.storage }}</p>
                    <p>{{ phone.battery }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="font-bold text-blue-400">${{ phone.price }}</p>
                    <button>
                        <a
                            :href="`/phones/${phone.id}`"
                            class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs text-white transition-colors hover:bg-blue-700"
                            >View</a
                        >
                    </button>
                </div>
                <button
                    @click="compareStore.addPhone(phone)"
                    :disabled="
                        isInCompare(phone) || compareStore.phones.length >= 3
                    "
                    class="mt-2 w-full rounded-lg border py-1.5 text-xs transition-colors"
                    :class="
                        isInCompare(phone)
                            ? 'cursor-default border-green-600 text-green-400'
                            : 'border-gray-600 text-gray-300 hover:bg-gray-700'
                    "
                >
                    {{ isInCompare(phone) ? '✓ Added' : '+ Compare' }}
                </button>
            </div>
        </div>
    </div>
</template>
