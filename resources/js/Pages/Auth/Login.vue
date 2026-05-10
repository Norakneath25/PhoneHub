<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
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

    <div class="min-h-screen bg-gray-950 flex items-center justify-center px-4">
        <div class="w-full max-w-md">

            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-white">PhoneHub</h1>
                <p class="text-gray-400 text-sm mt-1">Sign in to your account</p>
            </div>

            <!-- Card -->
            <div class="bg-gray-800 rounded-2xl p-8">

                <!-- Status -->
                <div v-if="status" class="mb-4 text-sm text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Email -->
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="you@example.com"
                        />
                        <InputError :message="form.errors.email" class="mt-1 text-red-400 text-xs" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="••••••••"
                        />
                        <InputError :message="form.errors.password" class="mt-1 text-red-400 text-xs" />
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-400">
                            <input type="checkbox" v-model="form.remember" class="rounded" />
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
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2.5 rounded-lg font-medium transition-colors"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    >
                        Sign in
                    </button>

                    <!-- Register link -->
                    <p class="text-center text-sm text-gray-400">
                        Don't have an account?
                        <Link :href="route('register')" class="text-blue-400 hover:text-blue-300">
                            Register
                        </Link>
                    </p>

                </form>
            </div>
        </div>
    </div>
</template>
