<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PhoneDetailCard from '@/Components/Phones/PhoneDetailCard.vue';
import PhoneReviews from '@/Components/Phones/PhoneReviews.vue';
import Navbar from '@/Components/Site/Navbar.vue';
import type { Phone } from '@/types/Phone';

const props = defineProps<{
    phone: Phone;
}>();

const page = usePage();
const auth = computed(
    () => page.props.auth as { user: { id: number; name: string } } | null,
);

const phone = ref({ ...props.phone });

const onReviewAdded = (newReview: any) => {
    if (newReview) {
        phone.value.reviews.push(newReview);
    }
};
</script>

<template>
    <div class="bg-gray-950">
        <Navbar />
        <PhoneDetailCard :phone="phone" />
        <PhoneReviews
            :reviews="phone.reviews"
            :phone-id="phone.id"
            :auth="auth?.user ?? null"
            @review-added="onReviewAdded"
        />
    </div>
</template>
