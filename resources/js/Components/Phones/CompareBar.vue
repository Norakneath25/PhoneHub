<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const comparePhones = computed(() => {
    // Read from URL params
    const params = new URL(window.location.href).searchParams;
    const ids = params.get('ids') || '';
    return ids ? ids.split(',').filter(Boolean).map(Number) : [];
});

const count = computed(() => comparePhones.value.length);
</script>

<template>
    <div
        v-if="count > 0"
        class="fixed right-0 bottom-0 left-0 z-50 border-t border-gray-700 bg-gray-900 px-6 py-4"
    >
        <div class="mx-auto flex max-w-6xl items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    v-for="n in count"
                    :key="n"
                    class="flex h-10 w-32 items-center justify-center rounded-lg bg-gray-800 px-3 text-sm text-white"
                >
                    Phone {{ n }}
                </div>

                <div
                    v-for="n in 3 - count"
                    :key="'empty-' + n"
                    class="flex h-10 w-32 items-center justify-center rounded-lg border border-dashed border-gray-600 text-xs text-gray-600"
                >
                    + Add phone
                </div>
            </div>

            <div class="flex items-center gap-3">
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
