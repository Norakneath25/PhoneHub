<script setup lang="ts">
import { useCompareStore } from '@/stores/compareStore';

const compareStore = useCompareStore();
</script>

<template>
    <div
        v-if="compareStore.phones.length > 0"
        class="fixed right-0 bottom-0 left-0 z-50 border-t border-gray-700 bg-gray-900 px-6 py-4"
    >
        <div class="mx-auto flex max-w-6xl items-center justify-between">
            <!-- Selected Phones -->
            <div class="flex items-center gap-4">
                <div
                    v-for="phone in compareStore.phones"
                    :key="phone.id"
                    class="flex items-center gap-2 rounded-lg bg-gray-800 px-3 py-2"
                >
                    <img
                        :src="phone.image"
                        class="h-8 w-8 rounded object-cover"
                    />
                    <span class="text-sm text-white"
                        >{{ phone.brand }} {{ phone.model }}</span
                    >
                    <button
                        @click="compareStore.removePhone(phone.id)"
                        class="ml-1 text-gray-400 hover:text-red-400"
                    >
                        ✕
                    </button>
                </div>

                <!-- Empty slots -->
                <div
                    v-for="n in 3 - compareStore.phones.length"
                    :key="n"
                    class="flex h-10 w-32 items-center justify-center rounded-lg border border-dashed border-gray-600 text-xs text-gray-600"
                >
                    + Add phone
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3">
                <button
                    @click="compareStore.clearAll()"
                    class="text-sm text-gray-400 transition-colors hover:text-white"
                >
                    Clear
                </button>

                <a
                    href="/compare"
                    class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-500"
                >
                    Compare →
                </a>
            </div>
        </div>
    </div>
</template>
