<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
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

    <div class="min-h-screen bg-gray-950 flex items-center justify-center px-4">
        <div class="w-full max-w-md">

            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-white">PhoneHub</h1>
                <p class="text-gray-400 text-sm mt-1">Create your account</p>
            </div>

            <!-- Card -->
            <div class="bg-gray-800 rounded-2xl p-8">

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Name -->
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            autofocus
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Your name"
                        />
                        <InputError :message="form.errors.name" class="mt-1 text-red-400 text-xs" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
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

                    <!-- Confirm Password -->
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Confirm Password</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="••••••••"
                        />
                        <InputError :message="form.errors.password_confirmation" class="mt-1 text-red-400 text-xs" />
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2.5 rounded-lg font-medium transition-colors"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    >
                        Create Account
                    </button>

                    <!-- Login link -->
                    <p class="text-center text-sm text-gray-400">
                        Already have an account?
                        <Link :href="route('login')" class="text-blue-400 hover:text-blue-300">
                            Sign in
                        </Link>
                    </p>

                </form>
            </div>
        </div>
    </div>
</template>
