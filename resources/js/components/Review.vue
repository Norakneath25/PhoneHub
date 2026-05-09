<template>
    <div class="mx-auto max-w-6xl px-4  sm:px-6 lg:px-8 bg-gray-950 py-8">
        <h2 class="mb-6 text-2xl font-bold text-white">
            Reviews ({{ reviews.length }})
        </h2>

        <!-- Write a Review -->
        <div class="rounded-xl bg-gray-800 p-6">
            <h3 class="mb-4 font-semibold text-white">Write a Review</h3>

            <!-- Rating Input -->
            <div class="mb-4 flex items-center gap-2">
                <span class="text-sm text-gray-400">Rating:</span>
                <div class="flex gap-1">
                    <button
                        v-for="star in 5"
                        :key="star"
                        @click="selectedRating = star"
                        class="text-2xl transition-colors"
                        :class="
                            star <= selectedRating
                                ? 'text-yellow-400'
                                : 'text-gray-600'
                        "
                    >
                        ★
                    </button>
                </div>
            </div>

            <!-- Comment Input -->
            <textarea
                v-model="comment"
                rows="3"
                placeholder="Write your review here..."
                class="mb-4 w-full resize-none rounded-lg bg-gray-700 px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />

            <!-- Send Button -->
            <button
                @click="submitReview"
                class="rounded-lg bg-blue-600 px-6 py-2 font-medium text-white transition-colors hover:bg-blue-500"
            >
                Submit Review
            </button>
        </div>
        <!-- Review List -->
        <div class="mb-10 space-y-4 mt-4">
            <div
                v-for="review in reviews"
                :key="review.id"
                class="rounded-xl bg-gray-800 p-5"
            >
                <!-- Rating Stars -->
                <div class="mb-2 flex items-center gap-1">
                    <span
                        v-for="star in 5"
                        :key="star"
                        class="text-lg"
                        :class="
                            star <= review.rating
                                ? 'text-yellow-400'
                                : 'text-gray-600'
                        "
                        >★</span
                    >
                    <span class="ml-2 text-sm text-gray-400"
                        >{{ review.rating }}/5</span
                    >
                </div>

                <!-- Comment -->
                <p class="text-sm text-gray-300">{{ review.comment }}</p>
            </div>

            <!-- No reviews yet -->
            <div v-if="reviews.length === 0" class="text-sm text-gray-500">
                No reviews yet. Be the first to review!
            </div>
        </div>


    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
    reviews: { id: number; rating: number; comment: string }[];
    phoneId: number;
}>();

const comment = ref('');
const selectedRating = ref(0);

const submitReview = async () => {
    if (!comment.value || selectedRating.value === 0) {
        return;
    }

    await fetch('/api/reviews', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify({
            phone_id: props.phoneId,
            user_id: 1, // temporary until auth is set up
            comment: comment.value,
            rating: selectedRating.value,
        }),
    });

    comment.value = '';
    selectedRating.value = 0;
};
</script>
