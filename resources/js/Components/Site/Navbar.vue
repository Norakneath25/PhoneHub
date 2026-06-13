<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PhoneIcon from '@/Components/PhoneIcon.vue';

const isOpen = ref(false);
const page = usePage();

const auth = computed(
    () =>
        page.props.auth as {
            user: { name: string; is_admin?: boolean } | null;
        },
);
const currentPath = computed(() => page.url);

const navLinks = computed(() => {
    const links = [
        { label: 'Home', href: '/' },
        { label: 'Products', href: '/products' },
        { label: 'Upcoming', href: '/upcoming' },
        { label: 'Compare', href: '/compare' },
        { label: 'Blog', href: '/blog' },
    ];

    if (auth.value.user) {
        links.push({ label: 'Dashboard', href: '/dashboard' });
    }
    
    // Favorites always visible
    links.push({ label: 'Favorites', href: '/favorites' });

    if (auth.value.user?.is_admin) {
        links.push({ label: 'Admin', href: '/admin' });
    }

    return links;
});

const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

const closeMenu = () => {
    isOpen.value = false;
};

const isActive = (href: string) => {
    if (href === '/') {
        return currentPath.value === '/';
    }

    return currentPath.value.startsWith(href);
};
</script>

<template>
    <nav
        class="sticky top-0 z-50 border-b border-white/10 bg-[#0b0f14]/90 backdrop-blur-xl"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <Link href="/" class="flex items-center gap-3">
                    <PhoneIcon
                        :size="22"
                        icon-class="text-sky-400"
                    />
                    <span class="text-xl font-extrabold text-white">
                        Phone<span class="text-sky-300">Hub</span>
                    </span>
                </Link>

                <div
                    class="hidden items-center rounded-full bg-white/[0.04] p-1 md:flex"
                >
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        :class="[
                            'rounded-full px-4 py-2 text-sm font-medium transition',
                            isActive(link.href)
                                ? 'bg-white text-slate-950'
                                : 'text-slate-300 hover:text-white',
                        ]"
                    >
                        {{ link.label }}
                    </Link>
                </div>

                <div class="flex items-center gap-3">
                    <template v-if="auth.user">
                        <span class="hidden text-sm text-slate-300 md:block">
                            {{ auth.user.name }}
                        </span>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="rounded-full border border-red-400/30 px-4 py-2 text-sm font-medium text-red-200 transition hover:border-red-300/60 hover:bg-red-400/10"
                        >
                            Logout
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="hidden rounded-full px-4 py-2 text-sm font-medium text-slate-300 transition hover:text-white sm:inline-flex"
                        >
                            Login
                        </Link>
                        <Link
                            href="/register"
                            class="rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-400"
                        >
                            Sign up
                        </Link>
                    </template>

                    <button
                        type="button"
                        @click="toggleMenu"
                        class="rounded-full p-2 text-slate-300 transition hover:bg-white/10 hover:text-white md:hidden"
                        aria-label="Toggle navigation"
                    >
                        <svg
                            v-if="!isOpen"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

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
                class="border-t border-white/10 bg-[#0b0f14] px-4 pb-4 pt-2 md:hidden"
            >
                <div class="flex flex-col gap-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        @click="closeMenu"
                        :class="[
                            'rounded-lg px-4 py-3 text-sm font-medium transition',
                            isActive(link.href)
                                ? 'bg-white text-slate-950'
                                : 'text-slate-300 hover:bg-white/10 hover:text-white',
                        ]"
                    >
                        {{ link.label }}
                    </Link>

                    <div class="mt-3 border-t border-white/10 pt-3">
                        <template v-if="auth.user">
                            <p class="mb-2 px-4 text-sm text-slate-400">
                                {{ auth.user.name }}
                            </p>
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="w-full rounded-lg px-4 py-3 text-left text-sm font-medium text-red-200 transition hover:bg-red-400/10"
                            >
                                Logout
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                href="/login"
                                @click="closeMenu"
                                class="block rounded-lg px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                            >
                                Login
                            </Link>
                            <Link
                                href="/register"
                                @click="closeMenu"
                                class="mt-1 block rounded-lg bg-sky-500 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-sky-400"
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
