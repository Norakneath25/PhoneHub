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
            class="group overflow-hidden rounded-2xl bg-gray-800 transition-all hover:ring-1 hover:ring-blue-500"
        >
            <!-- Image -->
            <div class="bg-gray-750 relative h-64 overflow-hidden">
                <img
                    :src="phone.image"
                    :alt="phone.model"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
            </div>

            <!-- Content -->
            <div class="p-4">
                <p class="text-xs uppercase tracking-widest text-gray-500">
                    {{ phone.brand }}
                </p>
                <h3 class="mb-3 mt-1 font-semibold leading-tight text-white">
                    {{ phone.model }}
                </h3>

                <!-- Specs -->
                <div class="mb-4 space-y-1.5 text-xs text-gray-400">
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-600">RAM</span>
                        <span>{{ phone.ram }}</span>
                        <span class="text-gray-600">·</span>
                        <span class="text-gray-600">Storage</span>
                        <span>{{ phone.storage }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-600">Battery</span>
                        <span>{{ phone.battery }}</span>
                    </div>
                </div>

                <!-- Price + View -->
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-lg font-bold text-blue-400">
                        ${{ phone.price }}
                    </p>
                    <a
                        :href="`/phones/${phone.id}`"
                        class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-medium text-white transition-colors hover:bg-blue-500"
                    >
                        View
                    </a>
                </div>

                <!-- Compare -->
                <button
                    @click="compareStore.addPhone(phone)"
                    :disabled="
                        isInCompare(phone) || compareStore.phones.length >= 3
                    "
                    class="w-full rounded-lg border py-1.5 text-xs transition-colors"
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
