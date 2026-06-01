<script setup lang="ts">
import Navbar from '@/Components/Site/Navbar.vue';
import { useForm } from '@inertiajs/vue3';
import type { Phone } from '@/types/Phone';

const props = defineProps<{
    phone: Phone;
}>();

const form = useForm({
    brand: props.phone.brand,
    model: props.phone.model,
    price: props.phone.price,
    image: props.phone.image,
    store_url: props.phone.store_url,
    ram: props.phone.ram,
    storage: props.phone.storage,
    camera: props.phone.camera,
    battery: props.phone.battery,
    screen_size: props.phone.screen_size,
    os: props.phone.os,
    release_date: props.phone.release_date,
    rating: props.phone.rating,
});

const submit = () => {
    form.put(`/admin/phones/${props.phone.id}`);
};
</script>

<template>
    <div class="min-h-screen bg-gray-950">
        <Navbar />

        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-3xl font-bold text-white">Edit Phone</h1>
                <a href="/admin" class="text-gray-400 hover:text-white text-sm transition-colors">← Back</a>
            </div>

            <!-- Form -->
            <div class="bg-gray-800 rounded-2xl p-8 space-y-5">

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Brand</label>
                        <input v-model="form.brand" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <p v-if="form.errors.brand" class="text-red-400 text-xs mt-1">{{ form.errors.brand }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Model</label>
                        <input v-model="form.model" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <p v-if="form.errors.model" class="text-red-400 text-xs mt-1">{{ form.errors.model }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Price</label>
                        <input v-model="form.price" type="number" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <p v-if="form.errors.price" class="text-red-400 text-xs mt-1">{{ form.errors.price }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Rating</label>
                        <input v-model="form.rating" type="number" step="0.1" min="0" max="5" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <p v-if="form.errors.rating" class="text-red-400 text-xs mt-1">{{ form.errors.rating }}</p>
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-400 mb-1 block">Image URL</label>
                    <input v-model="form.image" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="text-sm text-gray-400 mb-1 block">Store URL</label>
                    <input v-model="form.store_url" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">RAM</label>
                        <input v-model="form.ram" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Storage</label>
                        <input v-model="form.storage" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Camera</label>
                        <input v-model="form.camera" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Battery</label>
                        <input v-model="form.battery" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">Screen Size</label>
                        <input v-model="form.screen_size" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="text-sm text-gray-400 mb-1 block">OS</label>
                        <input v-model="form.os" type="text" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-400 mb-1 block">Release Date</label>
                    <input v-model="form.release_date" type="date" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <!-- Submit -->
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2.5 rounded-lg font-medium transition-colors"
                >
                    Update Phone
                </button>
            </div>
        </div>
    </div>
</template>
