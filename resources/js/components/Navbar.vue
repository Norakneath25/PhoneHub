<template>
    <nav
        class="flex items-center justify-between bg-gray-900 px-4 py-4 text-white shadow-md"
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
                :value="searchStore.query"
                @input="
                    (e) =>
                        searchStore.setQuery(
                            (e.target as HTMLInputElement).value,
                        )
                "
                placeholder="Search phones..."
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
            />
            <!-- login icon -->
            <!-- Auth buttons -->
            <div v-if="auth?.user" class="flex items-center gap-2">
                <span class="text-sm text-gray-400">{{ auth.user.name }}</span>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="text-sm text-red-400 transition-colors hover:text-red-300"
                >
                    Logout
                </Link>
            </div>
            <div v-else>
                <a
                    href="/login"
                    class="text-sm text-blue-400 transition-colors hover:text-blue-300"
                    >Login</a
                >
            </div>
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
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import { useSearchStore } from '@/stores/searchStore';

const searchStore = useSearchStore();

const isOpen = ref(false);
const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

const page = usePage();
const auth = computed(
    () => page.props.auth as { user: { name: string } | null },
);
</script>

<style scoped></style>
