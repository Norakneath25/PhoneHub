<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Navbar from '@/Components/Site/Navbar.vue';

const props = defineProps<{
    reviews: {
        id: number;
        comment: string;
        rating: number;
        phone: { brand: string; model: string };
        user: { name: string };
    }[];
}>();

const averageRating = computed(() => {
    if (!props.reviews.length) {
        return '0.0';
    }

    const total = props.reviews.reduce(
        (sum, review) => sum + Number(review.rating),
        0,
    );

    return (total / props.reviews.length).toFixed(1);
});

const confirmDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this review?')) {
        router.delete(`/admin/reviews/${id}`);
    }
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <section
                class="mb-8 grid gap-6 border-b border-white/10 pb-8 lg:grid-cols-[1fr_320px]"
            >
                <div>
                    <p class="text-sm font-semibold text-sky-300">Reviews</p>
                    <h1 class="mt-3 text-4xl font-bold text-white">
                        Moderate customer feedback.
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">
                        Review comments, ratings, and remove entries that do not
                        belong on the public phone detail pages.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link
                            href="/admin"
                            class="rounded-full bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400"
                        >
                            Back to admin
                        </Link>
                        <Link
                            href="/"
                            class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                        >
                            View site
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.04] p-4"
                    >
                        <p class="text-2xl font-bold text-white">
                            {{ reviews.length }}
                        </p>
                        <p class="mt-1 text-sm text-slate-400">Reviews</p>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.04] p-4"
                    >
                        <p class="text-2xl font-bold text-white">
                            {{ averageRating }}
                        </p>
                        <p class="mt-1 text-sm text-slate-400">Avg rating</p>
                    </div>
                </div>
            </section>

            <div class="overflow-x-auto rounded-lg border border-white/10">
                <table class="w-full min-w-[820px] text-sm text-white">
                    <thead
                        class="bg-white/[0.03] text-xs uppercase text-slate-500"
                    >
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
                            class="border-t border-white/10 bg-[#0f1620] transition hover:bg-white/[0.05]"
                        >
                            <td class="px-6 py-4 font-semibold text-white">
                                {{ review.phone.brand }}
                                {{ review.phone.model }}
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ review.user.name }}
                            </td>
                            <td class="px-6 py-4 text-amber-200">
                                {{ review.rating }}/5
                            </td>
                            <td
                                class="max-w-md truncate px-6 py-4 text-slate-300"
                            >
                                {{ review.comment }}
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    @click="confirmDelete(review.id)"
                                    class="font-medium text-red-300 transition hover:text-red-200"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div
                    v-if="reviews.length === 0"
                    class="border-t border-white/10 bg-white/[0.03] py-16 text-center text-sm text-slate-400"
                >
                    No reviews yet.
                </div>
            </div>
        </main>
    </div>
</template>
