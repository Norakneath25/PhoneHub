<template>
    <div class=" pt-20 pb-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <!-- Back button -->
            <a
                href="/"
                class="mb-8 inline-flex items-center gap-2 text-sm text-gray-400 transition-colors hover:text-white"
            >
                ← Back to phones
            </a>

            <div class="mt-6 flex flex-col gap-10 md:flex-row">
                <!-- Left - Image -->
                <div class="md:w-1/2">
                    <div class="h-[500px] overflow-hidden rounded-2xl">
                        <img
                            class="h-full w-full object-cover"
                            :src="phone.image"
                            :alt="phone.model"
                        />
                    </div>
                    <button
                        class="mt-4 w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 font-medium text-white transition-colors hover:bg-gray-700"
                    >
                        + Add to Compare List
                    </button>
                </div>

                <!-- Right - Details -->
                <div class="flex flex-col justify-center md:w-1/2">
                    <p
                        class="mb-2 text-xs tracking-widest text-blue-400 uppercase"
                    >
                        {{ phone.brand }}
                    </p>
                    <h1 class="mb-3 text-4xl font-bold text-white">
                        {{ phone.model }}
                    </h1>
                    <p class="mb-8 text-3xl font-bold text-blue-400">
                        ${{ phone.price }}
                    </p>

                    <!-- Specs Grid -->
                    <div class="mb-8 grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">RAM</p>
                            <p class="font-medium text-white">
                                {{ phone.ram }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">Storage</p>
                            <p class="font-medium text-white">
                                {{ phone.storage }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">Camera</p>
                            <p class="font-medium text-white">
                                {{ phone.camera }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">Battery</p>
                            <p class="font-medium text-white">
                                {{ phone.battery }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">Screen</p>
                            <p class="font-medium text-white">
                                {{ phone.screen_size }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-gray-800 p-4">
                            <p class="mb-1 text-xs text-gray-500">OS</p>
                            <p class="font-medium text-white">{{ phone.os }}</p>
                        </div>
                    </div>

                    <!-- Store Button -->
                    <a
                        :href="phone.store_url"
                        target="_blank"
                        class="block rounded-xl bg-blue-600 py-3 text-center font-medium text-white transition-colors hover:bg-blue-500"
                    >
                        View Store →
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Phone } from '@/types/Phone';

defineProps<{
    phone: Phone;
}>();
</script>
