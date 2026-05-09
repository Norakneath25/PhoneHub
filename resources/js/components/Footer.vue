<template>
    <footer class="bg-gray-900 border-t border-gray-800 py-10 text-white mt-6">
        <div class="container mx-auto flex flex-col md:flex-row justify-between gap-8 px-6">

            <!-- Left - Brand -->
            <div>
                <h2 class="text-xl font-bold mb-2">PhoneHub</h2>
                <p class="text-gray-400 text-sm max-w-xs">
                    Discover specs, compare models, and find the best prices across stores.
                </p>
            </div>

            <!-- Middle - Quick Links -->
            <div>
                <h3 class="font-semibold mb-3 text-gray-300">Quick Links</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="/" class="hover:text-white transition-colors">Home</a></li>
                    <li><a href="/compare" class="hover:text-white transition-colors">Compare</a></li>
                    <li><a href="/blog" class="hover:text-white transition-colors">Blog</a></li>
                </ul>
            </div>

            <!-- Right - Contact -->
            <div>
                <h3 class="font-semibold mb-3 text-gray-300">Contact</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>phonehub@email.com</li>
                    <li>Twitter: @phonehub</li>
                </ul>
            </div>

        </div>

        <!-- Bottom -->
        <div class="mt-8 border-t border-gray-800 pt-4 text-center text-sm text-gray-500">
            &copy; {{ new Date().getFullYear() }} PhoneHub. All rights reserved.
        </div>
    </footer>
</template>

<script setup lang="ts"></script>
