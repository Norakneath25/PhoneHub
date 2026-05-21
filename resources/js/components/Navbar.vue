<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const isOpen = ref(false);
const toggleMenu = () => { isOpen.value = !isOpen.value; };

const page = usePage();
const auth = computed(() => page.props.auth as { user: { name: string } | null });

const currentPath = computed(() => page.url);

const navLinks = [
    { label: 'Home',    href: '/' },
    { label: 'Compare', href: '/compare' },
    { label: 'Blog',    href: '/blog' },
];

const isActive = (href: string) => {
    
    if (href === '/') return currentPath.value === '/';

    return currentPath.value.startsWith(href);
};
</script>

<template>
    <!-- Desktop Nav -->
    <nav class="sticky top-0 z-50 border-b border-gray-800 bg-gray-900/95 backdrop-blur-md">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">

                <!-- Logo -->
                <Link href="/" class="flex items-center gap-1">
                    <span class="text-2xl font-extrabold tracking-tight text-white">
                        Phone<span class="text-blue-400">Hub</span>
                    </span>
                </Link>

                <!-- Desktop Links -->
                <div class="hidden items-center gap-1 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        :class="[
                            'rounded-lg px-4 py-2 text-sm font-medium transition-colors',
                            isActive(link.href)
                                ? 'bg-gray-800 text-white'
                                : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                        ]"
                    >
                        {{ link.label }}
                    </Link>
                </div>

                <!-- Right side -->
                <div class="flex items-center gap-3">
                    <template v-if="auth?.user">
                        <span class="hidden text-sm text-gray-400 md:block">
                            {{ auth.user.name }}
                        </span>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="rounded-lg border border-red-500/30 px-3 py-1.5 text-sm font-medium text-red-400 transition-colors hover:border-red-400 hover:text-red-300"
                        >
                            Logout
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-400 transition-colors hover:text-white"
                        >
                            Login
                        </Link>
                        <Link
                            href="/register"
                            class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-blue-500"
                        >
                            Sign up
                        </Link>
                    </template>

                    <!-- Hamburger -->
                    <button
                        @click="toggleMenu"
                        class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-800 hover:text-white md:hidden"
                    >
                        <svg v-if="!isOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="-translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-2 opacity-0"
        >
            <div
                v-if="isOpen"
                class="border-t border-gray-800 bg-gray-900 px-4 pb-4 pt-2 md:hidden"
            >
                <div class="flex flex-col gap-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        @click="isOpen = false"
                        :class="[
                            'rounded-lg px-4 py-2.5 text-sm font-medium transition-colors',
                            isActive(link.href)
                                ? 'bg-gray-800 text-white'
                                : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                        ]"
                    >
                        {{ link.label }}
                    </Link>

                    <div class="mt-2 border-t border-gray-800 pt-3">
                        <template v-if="auth?.user">
                            <p class="mb-2 px-4 text-xs text-gray-600">{{ auth.user.name }}</p>
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="w-full rounded-lg px-4 py-2.5 text-left text-sm font-medium text-red-400 transition-colors hover:bg-gray-800"
                            >
                                Logout
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                href="/login"
                                @click="isOpen = false"
                                class="block rounded-lg px-4 py-2.5 text-sm font-medium text-gray-400 transition-colors hover:bg-gray-800 hover:text-white"
                            >
                                Login
                            </Link>
                            <Link
                                href="/register"
                                @click="isOpen = false"
                                class="mt-1 block rounded-lg bg-blue-600 px-4 py-2.5 text-center text-sm font-medium text-white transition-colors hover:bg-blue-500"
                            >
                                Sign up
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </transition>
    </nav>
</template>