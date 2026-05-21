<script setup lang="ts">
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Navbar from '@/components/Navbar.vue';
import type { Phone } from '@/types/Phone';

const props = defineProps<{
    phones: Phone[];
}>();

const scrapeUrl = ref('');
const scraping = ref(false);

const siteType = ref('auto');

const bulkScrapeUrl = ref('');
const bulkScraping = ref(false);

// selected phones
const selectedPhones = ref<number[]>([]);

const allSelected = computed({
    get: () =>
        props.phones.length > 0 &&
        selectedPhones.value.length === props.phones.length,
    set: (value: boolean) => {
        if (value) {
            selectedPhones.value = props.phones.map((phone) => phone.id);
        } else {
            selectedPhones.value = [];
        }
    },
});

const confirmDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this phone?')) {
        router.delete(`/admin/phones/${id}`);
    }
};

const deleteSelected = () => {
    if (selectedPhones.value.length === 0) {
        alert('Please select at least one phone');
        return;
    }

    if (confirm(`Delete ${selectedPhones.value.length} selected phone(s)?`)) {
        router.post('/admin/phones/bulk-delete', {
            ids: selectedPhones.value,
        });

        selectedPhones.value = [];
    }
};

const submitScrape = () => {
    scraping.value = true;

    useForm({
        url: scrapeUrl.value,
    }).post('/admin/scrape', {
        onFinish: () => {
            scraping.value = false;
            scrapeUrl.value = '';
        },
    });
};

const submitBulkScrape = () => {
    bulkScraping.value = true;

    useForm({
        url: bulkScrapeUrl.value,
        site_type: siteType.value,
    }).post('/admin/bulk-scrape', {
        onFinish: () => {
            bulkScraping.value = false;
            bulkScrapeUrl.value = '';
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-950">
        <Navbar />

        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Header -->

            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-3xl font-bold text-white">Admin Panel</h1>
                <div class="flex gap-3">
                    <a
                        href="/admin/reviews"
                        class="rounded-lg border border-gray-600 px-4 py-2 text-sm text-white transition-colors hover:bg-gray-800"
                    >
                        Manage Reviews
                    </a>
                    <Link
                        href="/admin/phones/create"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-500"
                    >
                        + Add Phone
                    </Link>
                </div>
            </div>

            <!-- Success/Error messages -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-6 rounded-lg border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400"
            >
                {{ $page.props.flash.success }}
            </div>
            <div
                v-if="$page.props.flash?.error"
                class="mb-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400"
            >
                {{ $page.props.flash.error }}
            </div>

            <!-- Scrape Form -->
            <div class="mb-8 rounded-xl bg-gray-800 p-6">
                <h2 class="mb-4 font-semibold text-white">
                    Scrape Phone from URL
                </h2>
                <form @submit.prevent="submitScrape" class="flex gap-3">
                    <input
                        v-model="scrapeUrl"
                        type="url"
                        placeholder="https://web.nika2u.com/product/..."
                        class="flex-1 rounded-lg bg-gray-700 px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <button
                        type="submit"
                        :disabled="scraping"
                        class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-500"
                    >
                        {{ scraping ? 'Scraping...' : 'Scrape' }}
                    </button>
                </form>
            </div>

            <!-- Bulk Scrape Form -->
            <div class="mb-8 rounded-xl bg-gray-800 p-6">
                <h2 class="mb-1 font-semibold text-white">
                    Bulk Scrape from Category
                </h2>
                <p class="mb-4 text-xs text-gray-400">
                    Paste a category URL to scrape all phones from it
                </p>

                <form @submit.prevent="submitBulkScrape" class="space-y-4">
                    <!-- Site Selector -->
                    <div>
                        <label class="mb-1 block text-xs text-gray-400"
                            >Website</label
                        >
                        <select
                            v-model="siteType"
                            class="w-full rounded-lg bg-gray-700 px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="auto">Auto Detect</option>
                            <option value="nika2u">Nika2u</option>
                            <option value="soklyphone">Sokly Phone</option>
                            <option value="imobi">iMobi</option>
                        </select>
                    </div>

                    <!-- URL Input -->
                    <div>
                        <label class="mb-1 block text-xs text-gray-400"
                            >Category URL</label
                        >
                        <input
                            v-model="bulkScrapeUrl"
                            type="url"
                            placeholder="https://web.nika2u.com/products?category_id=1"
                            class="w-full rounded-lg bg-gray-700 px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="bulkScraping || !bulkScrapeUrl"
                        class="w-full rounded-lg bg-green-600 px-6 py-3 text-sm font-medium text-white transition-colors hover:bg-green-500 disabled:opacity-50"
                    >
                        {{
                            bulkScraping
                                ? 'Scraping... Please wait'
                                : 'Start Bulk Scrape'
                        }}
                    </button>
                </form>
            </div>

            <!-- Phones Table -->
            <div class="overflow-hidden rounded-xl border border-gray-800">
                <!-- Bulk Actions -->
                <div
                    class="flex items-center justify-between border-b border-gray-800 bg-gray-900 px-6 py-4"
                >
                    <p class="text-sm text-gray-400">
                        Selected: {{ selectedPhones.length }}
                    </p>

                    <button
                        @click="deleteSelected"
                        :disabled="selectedPhones.length === 0"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Delete Selected
                    </button>
                </div>

                <table class="w-full text-sm text-white">
                    <thead class="bg-gray-800 text-xs uppercase text-gray-400">
                        <tr>
                            <th class="px-6 py-4">
                                <input
                                    type="checkbox"
                                    v-model="allSelected"
                                    class="h-4 w-4"
                                />
                            </th>

                            <th class="px-6 py-4 text-left">Brand</th>
                            <th class="px-6 py-4 text-left">Model</th>
                            <th class="px-6 py-4 text-left">Price</th>
                            <th class="px-6 py-4 text-left">RAM</th>
                            <th class="px-6 py-4 text-left">Storage</th>
                            <th class="px-6 py-4 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="phone in phones"
                            :key="phone.id"
                            class="border-t border-gray-800 bg-gray-900 transition-colors hover:bg-gray-800"
                        >
                            <td class="px-6 py-4">
                                <input
                                    type="checkbox"
                                    :value="phone.id"
                                    v-model="selectedPhones"
                                    class="h-4 w-4"
                                />
                            </td>

                            <td class="px-6 py-4">{{ phone.brand }}</td>
                            <td class="px-6 py-4">{{ phone.model }}</td>
                            <td class="px-6 py-4">${{ phone.price }}</td>
                            <td class="px-6 py-4">{{ phone.ram }}</td>
                            <td class="px-6 py-4">{{ phone.storage }}</td>

                            <td class="flex gap-3 px-6 py-4">
                                <Link
                                    :href="`/admin/phones/${phone.id}/edit`"
                                    class="text-blue-400 transition-colors hover:text-blue-300"
                                >
                                    Edit
                                </Link>

                                <button
                                    @click="confirmDelete(phone.id)"
                                    class="text-red-400 transition-colors hover:text-red-300"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
