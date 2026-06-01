<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import InputError from '@/Components/InputError.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="flex min-h-screen items-center justify-center bg-gray-950 px-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-white">PhoneHub</h1>
                <p class="mt-1 text-sm text-gray-400">
                    Sign in to your account
                </p>
            </div>

            <!-- Card -->
            <div class="rounded-2xl bg-gray-800 p-8">
                <!-- Status -->
                <div v-if="status" class="mb-4 text-sm text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label class="mb-1 block text-sm text-gray-400"
                            >Email</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
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

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between">
                        <label
                            class="flex items-center gap-2 text-sm text-gray-400"
                        >
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="rounded"
                            />
                            Remember me
                        </label>
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-blue-400 hover:text-blue-300"
                        >
                            Forgot password?
                        </Link>
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
                        Sign in
                    </button>

                    <!-- Register link -->
                    <p class="text-center text-sm text-gray-400">
                        Don't have an account?
                        <Link
                            :href="route('register')"
                            class="text-blue-400 hover:text-blue-300"
                        >
                            Register
                        </Link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>
