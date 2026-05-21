<template>
    <nav class="bg-gray-900 px-6 py-5 text-white shadow-md">
        <div class="container py-4 mx-auto flex items-center justify-between">

            <!-- Logo -->
            <div class="text-4xl font-extrabold tracking-tight text-white">
                Phone<span class="text-blue-400">Hub</span>
            </div>

            <!-- Nav Links -->
            <div class="hidden space-x-8 md:flex">
                <a href="/" class="text-2xl font-medium transition-colors hover:text-blue-400">Home</a>
                <a href="/compare" class="text-2xl font-medium transition-colors hover:text-blue-400">Compare</a>
                <a href="" class="text-2xl font-medium transition-colors hover:text-blue-400">Blog</a>
            </div>

            <!-- Auth + Hamburger -->
            <div class="flex items-center gap-4">
                <div v-if="auth?.user" class="flex items-center gap-3">
                    <span class="text-xl text-gray-400">{{ auth.user.name }}</span>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="text-xl text-red-400 transition-colors hover:text-red-300"
                    >
                        Logout
                    </Link>
                </div>
                <a v-else href="/login" class="text-sm font-medium text-blue-400 transition-colors hover:text-blue-300">
                    Login
                </a>

                <button @click="toggleMenu" class="md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>

        </div>
    </nav>

    <!-- Mobile menu -->
    <div v-if="isOpen" class="space-y-3 bg-gray-900 px-6 pb-4 text-white shadow-md md:hidden">
        <a href="/" class="block text-base font-medium hover:text-blue-400">Home</a>
        <a href="/compare" class="block text-base font-medium hover:text-blue-400">Compare</a>
        <a href="" class="block text-base font-medium hover:text-blue-400">Blog</a>
    </div>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const isOpen = ref(false);
const toggleMenu = () => { isOpen.value = !isOpen.value; };

const page = usePage();
const auth = computed(() => page.props.auth as { user: { name: string } | null });
</script>
