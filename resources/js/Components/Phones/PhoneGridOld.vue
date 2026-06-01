<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { Phone } from '@/types/Phone';

const phones = ref<Phone[]>([]);

onMounted(async () => {
    const response = await fetch('/api/phones');
    const data = await response.json();
    phones.value = data.data;
});
</script>

<template>
    <section class="min-h-screen bg-gray-950 px-6 py-10">
        <div class="mx-auto max-w-6xl">
            <!-- Grid wrapper -->
            <div
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <div
                    v-for="phone in phones"
                    :key="phone.id"
                    class="overflow-hidden rounded-xl bg-gray-800 transition-all hover:ring-1 hover:ring-blue-500"
                >
                    <!-- Image -->
                    <img
                        :src="phone.image"
                        :alt="phone.model"
                        class="h-48 w-full object-cover"
                    />

                    <!-- Content -->
                    <div class="p-4">
                        <p
                            class="text-xs tracking-wide text-gray-500 uppercase"
                        >
                            {{ phone.brand }}
                        </p>
                        <h3 class="mt-0.5 mb-3 font-semibold text-white">
                            {{ phone.model }}
                        </h3>

                        <!-- Only show key specs -->
                        <div class="mb-4 space-y-1 text-xs text-gray-400">
                            <p>{{ phone.ram }} RAM · {{ phone.storage }}</p>
                            <p>{{ phone.battery }}</p>
                        </div>

                        <!-- Price + View Button -->
                        <div class="flex items-center justify-between">
                            <p class="font-bold text-blue-400">
                                ${{ phone.price }}
                            </p>
                            <button
                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs text-white transition-colors hover:bg-blue-700"
                            >
                                View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
