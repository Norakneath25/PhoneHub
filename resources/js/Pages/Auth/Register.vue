<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <div class="flex min-h-screen items-center justify-center bg-gray-950 px-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-white">PhoneHub</h1>
                <p class="mt-1 text-sm text-gray-400">Create your account</p>
            </div>

            <!-- Card -->
            <div class="rounded-2xl bg-gray-800 p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Name -->
                    <div>
                        <label class="mb-1 block text-sm text-gray-400"
                            >Name</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            autofocus
                            class="w-full rounded-lg bg-gray-700 px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Your name"
                        />
                        <InputError
                            :message="form.errors.name"
                            class="mt-1 text-xs text-red-400"
                        />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="mb-1 block text-sm text-gray-400"
                            >Email</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full rounded-lg bg-gray-700 px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="you@example.com"
                        />
                        <InputError
                            :message="form.errors.email"
                            class="mt-1 text-xs text-red-400"
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="mb-1 block text-sm text-gray-400"
                            >Password</label
                        >
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            class="w-full rounded-lg bg-gray-700 px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="••••••••"
                        />
                        <InputError
                            :message="form.errors.password"
                            class="mt-1 text-xs text-red-400"
                        />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="mb-1 block text-sm text-gray-400"
                            >Confirm Password</label
                        >
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="w-full rounded-lg bg-gray-700 px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="••••••••"
                        />
                        <InputError
                            :message="form.errors.password_confirmation"
                            class="mt-1 text-xs text-red-400"
                        />
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-lg bg-blue-600 py-2.5 font-medium text-white transition-colors hover:bg-blue-500"
                        :class="{
                            'cursor-not-allowed opacity-50': form.processing,
                        }"
                    >
                        Create Account
                    </button>

                    <!-- Login link -->
                    <p class="text-center text-sm text-gray-400">
                        Already have an account?
                        <Link
                            :href="route('login')"
                            class="text-blue-400 hover:text-blue-300"
                        >
                            Sign in
                        </Link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>
