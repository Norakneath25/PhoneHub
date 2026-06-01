<script setup lang="ts">
import { useCompareStore } from '@/stores/compareStore';
import type { Phone } from '@/types/Phone';

const compareStore = useCompareStore();

defineProps<{
    phones: Phone[];
}>();

const isInCompare = (phone: Phone) =>
    compareStore.phones.some((item) => item.id === phone.id);
</script>

<template>
    <div
        class="mx-auto grid max-w-7xl grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
    >
        <article
            v-for="phone in phones"
            :key="phone.id"
            class="group overflow-hidden rounded-lg border border-white/10 bg-white/[0.04] transition hover:-translate-y-1 hover:border-sky-300/40 hover:bg-white/[0.06]"
        >
            <a :href="`/phones/${phone.id}`" class="block">
                <div class="relative aspect-[4/3] overflow-hidden bg-slate-900">
                    <img
                        :src="phone.image"
                        :alt="phone.model"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    />
                    <div
                        class="absolute left-3 top-3 rounded-full bg-black/55 px-3 py-1 text-xs font-semibold text-white backdrop-blur"
                    >
                        {{ phone.brand }}
                    </div>
                </div>
            </a>

            <div class="p-4">
                <h3 class="min-h-12 font-semibold leading-6 text-white">
                    {{ phone.model }}
                </h3>

                <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                    <div class="rounded-md bg-white/[0.04] p-3">
                        <p class="text-slate-500">RAM</p>
                        <p class="mt-1 font-semibold text-slate-200">
                            {{ phone.ram }}
                        </p>
                    </div>
                    <div class="rounded-md bg-white/[0.04] p-3">
                        <p class="text-slate-500">Storage</p>
                        <p class="mt-1 font-semibold text-slate-200">
                            {{ phone.storage }}
                        </p>
                    </div>
                    <div class="rounded-md bg-white/[0.04] p-3">
                        <p class="text-slate-500">Battery</p>
                        <p class="mt-1 font-semibold text-slate-200">
                            {{ phone.battery }}
                        </p>
                    </div>
                    <div class="rounded-md bg-white/[0.04] p-3">
                        <p class="text-slate-500">Rating</p>
                        <p class="mt-1 font-semibold text-slate-200">
                            {{ phone.rating || 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between">
                    <p class="text-xl font-bold text-sky-300">
                        ${{ phone.price }}
                    </p>
                    <a
                        :href="`/phones/${phone.id}`"
                        class="rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-400"
                    >
                        View
                    </a>
                </div>

                <button
                    @click="compareStore.addPhone(phone)"
                    :disabled="
                        isInCompare(phone) || compareStore.phones.length >= 3
                    "
                    class="mt-3 w-full rounded-full border px-4 py-2 text-sm font-medium transition"
                    :class="
                        isInCompare(phone)
                            ? 'cursor-default border-emerald-400/30 bg-emerald-400/10 text-emerald-200'
                            : 'border-white/10 text-slate-300 hover:border-white/25 hover:bg-white/10 hover:text-white'
                    "
                >
                    {{ isInCompare(phone) ? 'Added to compare' : 'Compare' }}
                </button>
            </div>
        </article>
    </div>
</template>
