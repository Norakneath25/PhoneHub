<template>
    <div class="bg-gray-950 py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <h2 class="mb-6 text-2xl font-bold text-white">
                Reviews ({{ reviews.length }})
            </h2>

            <!-- Write a Review -->
            <div class="mb-6 rounded-xl bg-gray-800 p-6">
                <h3 class="mb-4 font-semibold text-white">Write a Review</h3>

                <!-- Rating - everyone can do this -->
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

                <!-- Alert -->
                <div
                    v-if="showAlert"
                    class="mb-4 flex items-center justify-between rounded-lg border border-yellow-500/20 bg-yellow-500/10 px-4 py-3"
                >
                    <p class="text-sm text-yellow-400">
                        Please
                        <a href="/login" class="underline hover:text-yellow-300"
                            >login</a
                        >
                        to rate or review this phone.
                    </p>
                    <button
                        @click="showAlert = false"
                        class="ml-4 text-yellow-400 hover:text-yellow-300"
                    >
                        ✕
                    </button>
                </div>

                <!-- Comment - only logged in users -->
                <div v-if="props.auth">
                    <textarea
                        v-model="comment"
                        rows="3"
                        placeholder="Write your review here..."
                        class="mb-4 w-full resize-none rounded-lg bg-gray-700 px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <button
                        @click="submitReview"
                        class="rounded-lg bg-blue-600 px-6 py-2 font-medium text-white transition-colors hover:bg-blue-500 active:bg-blue-700"
                    >
                        Submit Review
                    </button>
                </div>
                <div v-else>
                    <p class="text-sm text-gray-400">
                        <a
                            href="/login"
                            class="text-blue-400 hover:text-blue-300"
                            >Login</a
                        >
                        to write a comment.
                    </p>
                </div>
            </div>

            <!-- Review List -->
            <div class="space-y-4">
                <div
                    v-for="review in reviews"
                    :key="review.id"
                    class="rounded-xl bg-gray-800 p-5"
                >
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
                    <p class="text-sm text-gray-300">{{ review.comment }}</p>
                </div>

                <div v-if="reviews.length === 0" class="text-sm text-gray-500">
                    No reviews yet. Be the first to review!
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const showAlert = ref(false);

const props = defineProps<{
    reviews: { id: number; rating: number; comment: string }[];
    phoneId: number;
    auth: { id: number; name: string } | null;
}>();

const comment = ref('');
const selectedRating = ref(0);

const emit = defineEmits(['reviewAdded']);

const submitReview = async () => {
    if (!comment.value || selectedRating.value === 0) return;

    // Get CSRF token first
    await fetch('/sanctum/csrf-cookie', {
        credentials: 'include',
    });

    const response = await fetch('/reviews', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-XSRF-TOKEN': decodeURIComponent(
                document.cookie
                    .split('; ')
                    .find((row) => row.startsWith('XSRF-TOKEN='))
                    ?.split('=')[1] ?? '',
            ),
        },
        credentials: 'include',
        body: JSON.stringify({
            phone_id: props.phoneId,
            comment: comment.value,
            rating: selectedRating.value,
        }),
    });

    const data = await response.json();
    console.log('Response data:', data);
    emit('reviewAdded', data.data);

    comment.value = '';
    selectedRating.value = 0;
};
</script>
