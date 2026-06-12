<script setup lang="ts">
import PhoneIcon from '@/Components/PhoneIcon.vue';

const year = new Date().getFullYear();
</script>

<template>
    <footer class="border-t border-white/10 bg-[#0b0f14] px-4 py-10 text-white">
        <div
            class="mx-auto grid max-w-7xl gap-8 sm:px-6 md:grid-cols-[1fr_180px_220px] lg:px-8"
        >
            <div>
                <h2 class="flex items-center gap-2 text-xl font-extrabold">
                    <PhoneIcon :size="20" icon-class="text-sky-400" />
                    Phone<span class="text-sky-300">Hub</span>
                </h2>
                <p class="mt-3 max-w-md text-sm leading-6 text-slate-400">
                    Discover phone specs, compare models, and read practical
                    buying coverage before your next upgrade.
                </p>
            </div>

            <div>
                <h3 class="font-semibold text-white">Explore</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-400">
                    <li>
                        <a href="/" class="transition hover:text-white">Home</a>
                    </li>
                    <li>
                        <a href="/compare" class="transition hover:text-white">
                            Compare
                        </a>
                    </li>
                    <li>
                        <a href="/blog" class="transition hover:text-white">
                            Blog
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold text-white">Editorial</h3>
                <p class="mt-3 text-sm leading-6 text-slate-400">
                    Reviews, comparisons, battery notes, camera coverage, and
                    phone buying guides.
                </p>
            </div>
        </div>

        <div
            class="mx-auto mt-8 max-w-7xl border-t border-white/10 pt-5 text-sm text-slate-500 sm:px-6 lg:px-8"
        >
            &copy; {{ year }} PhoneHub. All rights reserved.
        </div>
    </footer>
</template>
