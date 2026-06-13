<script setup lang="ts">
import { router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '@/Components/Site/Navbar.vue';

const props = defineProps<{
    stores: Array<{
        id: number;
        name: string;
        logo_url: string | null;
        website_url: string;
        phone_prices_count: number;
    }>;
    flash?: { success?: string };
}>();

const showForm = ref(false);
const name = ref('');
const website_url = ref('');
const logo_url = ref('');

const addStore = () => {
    router.post('/admin/stores', {
        name: name.value,
        website_url: website_url.value,
        logo_url: logo_url.value || null,
    }, {
        onSuccess: () => {
            name.value = '';
            website_url.value = '';
            logo_url.value = '';
            showForm.value = false;
        }
    });
};

const confirmDelete = (id: number) => {
    if (confirm('Delete this store?')) {
        router.delete(`/admin/stores/${id}`);
    }
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />
        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div v-if="flash?.success" class="mb-6 rounded-lg border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
                {{ flash.success }}
            </div>

            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="text-sm font-semibold text-sky-300">Store Management</p>
                    <h1 class="mt-2 text-3xl font-bold text-white">Manage Stores</h1>
                    <p class="mt-2 text-sm text-slate-400">Add retailers and manage their phone prices.</p>
                </div>
                <div class="flex gap-3">
                    <Link href="/admin" class="rounded-full border border-white/15 px-4 py-2 text-sm text-slate-200 hover:bg-white/10">
                        ← Back to Admin
                    </Link>
                    <button @click="showForm = !showForm" class="rounded-full bg-sky-500 px-5 py-2 text-sm font-semibold hover:bg-sky-400">
                        {{ showForm ? 'Cancel' : 'Add Store' }}
                    </button>
                </div>
            </div>

            <div v-if="showForm" class="mb-8 rounded-lg border border-white/10 bg-white/[0.04] p-5">
                <h2 class="mb-4 text-lg font-semibold">New Store</h2>
                <form @submit.prevent="addStore" class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs text-slate-500">Store Name</label>
                        <input v-model="name" required class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50" placeholder="Amazon" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs text-slate-500">Website URL</label>
                        <input v-model="website_url" required type="url" class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50" placeholder="https://amazon.com" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs text-slate-500">Logo URL (optional)</label>
                        <input v-model="logo_url" type="url" class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50" placeholder="https://logo.png" />
                    </div>
                    <div class="md:col-span-3">
                        <button type="submit" class="rounded-lg bg-sky-500 px-5 py-2.5 text-sm font-semibold hover:bg-sky-400">
                            Save Store
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto rounded-lg border border-white/10">
                <table class="w-full min-w-[600px] text-sm">
                    <thead class="bg-white/[0.03] text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-6 py-4 text-left">Store</th>
                            <th class="px-6 py-4 text-left">Website</th>
                            <th class="px-6 py-4 text-left">Phones Listed</th>
                            <th class="px-6 py-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="store in stores" :key="store.id" class="border-t border-white/10 bg-[#0f1620] hover:bg-white/[0.05]">
                            <td class="px-6 py-4 font-semibold">{{ store.name }}</td>
                            <td class="px-6 py-4 text-sky-300">
                                <a :href="store.website_url" target="_blank" class="hover:underline">{{ store.website_url }}</a>
                            </td>
                            <td class="px-6 py-4">{{ store.phone_prices_count }}</td>
                            <td class="px-6 py-4">
                                <button @click="confirmDelete(store.id)" class="text-red-300 hover:text-red-200">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="stores.length === 0" class="mt-4 text-center text-sm text-slate-500">
                No stores yet. Add your first retailer above.
            </div>
        </main>
    </div>
</template>
