<template>
    <nav
        class="flex items-center justify-between  bg-gray-900 px-4 py-4 text-white shadow-md"
    >
        <div class="text-xl font-bold">PhoneHub</div>
        <div class="hidden space-x-6 md:flex">
            <a href="/" class="transition-colors hover:text-blue-400">Home</a>
            <a href="" class="transition-colors hover:text-blue-400"
                >Compares</a
            >
            <a href="" class="transition-colors hover:text-blue-400">Blog</a>
        </div>
        <div class="flex items-center justify-center space-x-2.5">
            <input
                type="text"
                class="block w-full rounded border border-none bg-white px-2.5 py-2 text-sm text-gray-900 shadow-xs focus:outline-none"
                placeholder="Search"
            />
            <!-- login icon -->
            <button>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                    />
                </svg>
            </button>
            <!-- bar -->
            <button @click="toggleMenu" class="md:hidden">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                    />
                </svg>
            </button>
        </div>
    </nav>

    <!-- mobile menu -->
    <div
        v-if="isOpen"
        class="space-y-2 bg-gray-900 px-4 pb-4 text-white shadow-md md:hidden"
    >
        <a href="" class="block">Home</a>
        <a href="" class="block">Compares</a>
        <a href="" class="block">Blog</a>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const isOpen = ref(false);
const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};
</script>

<style scoped></style>
