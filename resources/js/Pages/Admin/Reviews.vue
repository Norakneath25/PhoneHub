<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps<{
    reviews: {
        id: number;
        comment: string;
        rating: number;
        phone: { brand: string; model: string };
        user: { name: string };
    }[];
}>();

const confirmDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this review?')) {
        router.delete(`/admin/reviews/${id}`);
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-950">
        <Navbar />

        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-3xl font-bold text-white">Manage Reviews</h1>
                <a
                    href="/admin"
                    class="text-sm text-gray-400 transition-colors hover:text-white"
                    >← Back to Admin</a
                >
            </div>

            <!-- Reviews Table -->
            <div class="overflow-hidden rounded-xl border border-gray-800">
                <table class="w-full text-sm text-white">
                    <thead class="bg-gray-800 text-xs uppercase text-gray-400">
                        <tr>
                            <th class="px-6 py-4 text-left">Phone</th>
                            <th class="px-6 py-4 text-left">User</th>
                            <th class="px-6 py-4 text-left">Rating</th>
                            <th class="px-6 py-4 text-left">Comment</th>
                            <th class="px-6 py-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="review in reviews"
                            :key="review.id"
                            class="border-t border-gray-800 bg-gray-900 transition-colors hover:bg-gray-800"
                        >
                            <td class="px-6 py-4">
                                {{ review.phone.brand }}
                                {{ review.phone.model }}
                            </td>
                            <td class="px-6 py-4">{{ review.user.name }}</td>
                            <td class="px-6 py-4">⭐ {{ review.rating }}/5</td>
                            <td class="max-w-xs truncate px-6 py-4">
                                {{ review.comment }}
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    @click="confirmDelete(review.id)"
                                    class="text-red-400 transition-colors hover:text-red-300"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty state -->
                <div
                    v-if="reviews.length === 0"
                    class="py-12 text-center text-gray-500"
                >
                    No reviews yet.
                </div>
            </div>
        </div>
    </div>
</template>
