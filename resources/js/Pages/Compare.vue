<script setup lang="ts">
import { computed } from 'vue';
import Navbar from '@/Components/Site/Navbar.vue';
import { useCompareStore } from '@/stores/compareStore';
import type { Phone } from '@/types/Phone';

const props = defineProps<{
    phones: Phone[];
}>();

const compareStore = useCompareStore();

const specs = [
    { label: 'Brand', key: 'brand' },
    { label: 'Model', key: 'model' },
    { label: 'Price', key: 'price' },
    { label: 'RAM', key: 'ram' },
    { label: 'Storage', key: 'storage' },
    { label: 'Camera', key: 'camera' },
    { label: 'Battery', key: 'battery' },
    { label: 'Screen Size', key: 'screen_size' },
    { label: 'OS', key: 'os' },
    { label: 'Rating', key: 'rating' },
];

const availablePhones = computed(() => {
    return props.phones.filter(
        (p) => !compareStore.phones.find((c) => c.id === p.id),
    );
});
</script>

<template>
    <div class="min-h-screen bg-gray-950">
        <Navbar />

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Title -->
            <div class="mb-10 text-center">
                <h1 class="text-4xl font-bold text-white">
                    Compare Phones
                </h1>

                <p class="mt-2 text-sm text-gray-400">
                    Select up to 3 phones to compare
                </p>
            </div>

            <!-- Compare Slots -->
            <div class="mb-10 flex justify-center gap-6">
                <div
                    v-for="index in 3"
                    :key="index"
                    class="w-full max-w-xs"
                >
                    <!-- Phone Card -->
                    <div
                        v-if="compareStore.phones[index - 1]"
                        class="overflow-hidden rounded-2xl bg-gray-800 shadow-lg"
                    >
                        <img
                            :src="compareStore.phones[index - 1].image"
                            :alt="compareStore.phones[index - 1].model"
                            class="h-52 w-full object-cover"
                        />

                        <div class="p-5 text-center">
                            <p
                                class="text-xs tracking-wide text-gray-400 uppercase"
                            >
                                {{ compareStore.phones[index - 1].brand }}
                            </p>

                            <h3 class="mt-1 text-lg font-semibold text-white">
                                {{ compareStore.phones[index - 1].model }}
                            </h3>

                            <button
                                @click="
                                    compareStore.removePhone(
                                        compareStore.phones[index - 1].id,
                                    )
                                "
                                class="mt-3 text-sm text-red-400 transition-colors hover:text-red-300"
                            >
                                Remove
                            </button>
                        </div>
                    </div>

                    <!-- Empty Slot -->
                    <div
                        v-else
                        class="flex h-full min-h-[320px] flex-col items-center justify-center rounded-2xl border border-dashed border-gray-600 bg-gray-800 p-6"
                    >
                        <p class="mb-4 text-sm text-gray-500">
                            Add a phone
                        </p>

                        <select
                            class="w-full rounded-xl bg-gray-700 px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            @change="
                                (e) => {
                                    const phone = availablePhones.find(
                                        (p) =>
                                            p.id ===
                                            Number(
                                                (
                                                    e.target as HTMLSelectElement
                                                ).value,
                                            ),
                                    );

                                    if (phone) {
                                        compareStore.addPhone(phone);

                                        (
                                            e.target as HTMLSelectElement
                                        ).value = '';
                                    }
                                }
                            "
                        >
                            <option value="">
                                Select a phone...
                            </option>

                            <option
                                v-for="phone in availablePhones"
                                :key="phone.id"
                                :value="phone.id"
                            >
                                {{ phone.brand }} {{ phone.model }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Specs Table -->
            <div
                v-if="compareStore.phones.length > 0"
                class="overflow-hidden rounded-2xl border border-gray-800"
            >
                <div
                    v-for="(spec, index) in specs"
                    :key="spec.key"
                    class="flex"
                    :class="
                        index % 2 === 0
                            ? 'bg-gray-900'
                            : 'bg-gray-800'
                    "
                >
                    <!-- Label -->
                    <div
                        class="w-40 shrink-0 border-r border-gray-700 px-6 py-4 text-sm font-medium text-gray-400"
                    >
                        {{ spec.label }}
                    </div>

                    <!-- Values -->
                    <div
                        v-for="phone in compareStore.phones"
                        :key="phone.id"
                        class="flex-1 border-r border-gray-700 px-6 py-4 text-sm text-white last:border-0"
                    >
                        {{
                            spec.key === 'price'
                                ? '$' +
                                  phone[
                                      spec.key as keyof typeof phone
                                  ]
                                : phone[
                                      spec.key as keyof typeof phone
                                  ]
                        }}
                    </div>
                </div>
            </div>

            <!-- Empty Table State -->
            <div
                v-else
                class="rounded-2xl border border-dashed border-gray-700 py-20 text-center"
            >
                <p class="text-gray-500">
                    Add phones to start comparing
                </p>
            </div>

            <!-- Clear -->
            <div
                v-if="compareStore.phones.length > 0"
                class="mt-6 text-center"
            >
                <button
                    @click="compareStore.clearAll()"
                    class="text-sm text-gray-400 transition-colors hover:text-white"
                >
                    Clear all
                </button>
            </div>
        </div>
    </div>
</template>
