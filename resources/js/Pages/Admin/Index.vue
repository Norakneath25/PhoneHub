<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import type { Phone } from '@/types/Phone';

defineProps<{
    phones: Phone[];
}>();

const confirmDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this phone?')) {
        router.delete(`/admin/phones/${id}`);
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-950">
        <Navbar />

        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-3xl font-bold text-white">Admin Panel</h1>
                <Link
                    href="/admin/phones/create"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-500"
                >
                    + Add Phone
                </Link>
            </div>

            <!-- Phones Table -->
            <div class="overflow-hidden rounded-xl border border-gray-800">
                <table class="w-full text-sm text-white">
                    <thead class="bg-gray-800 text-xs uppercase text-gray-400">
                        <tr>
                            <th class="px-6 py-4 text-left">Brand</th>
                            <th class="px-6 py-4 text-left">Model</th>
                            <th class="px-6 py-4 text-left">Price</th>
                            <th class="px-6 py-4 text-left">RAM</th>
                            <th class="px-6 py-4 text-left">Storage</th>
                            <th class="px-6 py-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="phone in phones"
                            :key="phone.id"
                            class="border-t border-gray-800 bg-gray-900 transition-colors hover:bg-gray-800"
                        >
                            <td class="px-6 py-4">{{ phone.brand }}</td>
                            <td class="px-6 py-4">{{ phone.model }}</td>
                            <td class="px-6 py-4">${{ phone.price }}</td>
                            <td class="px-6 py-4">{{ phone.ram }}</td>
                            <td class="px-6 py-4">{{ phone.storage }}</td>
                            <td class="flex gap-3 px-6 py-4">
                                <Link
                                    :href="`/admin/phones/${phone.id}/edit`"
                                    class="text-blue-400 transition-colors hover:text-blue-300"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="confirmDelete(phone.id)"
                                    class="text-red-400 transition-colors hover:text-red-300"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
