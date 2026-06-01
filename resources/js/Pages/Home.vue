<template>
    <div class="bg-gray-900">
        <Navbar />
        <HomeHero />
        <div id="phones-section">
            <!-- ← add this wrapper -->
            <PhoneFilter :phones="phones" />
        </div>
        <CompareBar />
        <Footer />
    </div>
</template>

<script setup lang="ts">
import CompareBar from '@/Components/Phones/CompareBar.vue';
import PhoneFilter from '@/Components/Phones/PhoneFilter.vue';
import Footer from '@/Components/Site/Footer.vue';
import HomeHero from '@/Components/Site/HomeHero.vue';
import Navbar from '@/Components/Site/Navbar.vue';
import type { Phone } from '@/types/Phone';

defineProps<{
    phones: Phone[];
}>();
</script>

<style scoped></style>
