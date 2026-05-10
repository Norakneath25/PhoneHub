<template>
    <div class="bg-gray-900">
        <Navbar />
        <Hero />
        <Filter :phones="phones" />
        <CompareBar />
        <Footer />
    </div>
</template>

<script setup lang="ts">
import CompareBar from '@/components/compareBar.vue';
import Filter from '@/components/Filter.vue';
import Footer from '@/components/Footer.vue';
import Hero from '@/components/Hero.vue';
import Navbar from '@/components/Navbar.vue';
import { Phone } from '@/types/Phone';

defineProps<{
    phones: Phone[];
}>();
</script>

<style scoped></style>
