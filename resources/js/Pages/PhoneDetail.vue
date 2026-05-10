vue<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import PhoneCardDetail from '@/components/PhoneCardDetail.vue';
import Review from '@/components/Review.vue';
import type { Phone } from '@/types/Phone';

defineProps<{
    phone: Phone;
    auth: { id: number; name: string } | null;
}>();
</script>

<template>
    <div class="bg-gray-950">
        <Navbar />
        <PhoneCardDetail :phone="phone" />
        <Review :reviews="phone.reviews" :phone-id="phone.id" :auth="auth" />
    </div>
</template>
