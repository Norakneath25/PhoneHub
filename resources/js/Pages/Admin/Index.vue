<script setup lang="ts">
import type { PageProps } from '@inertiajs/core';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Navbar from '@/Components/Site/Navbar.vue';
import type { Phone } from '@/types/Phone';

const props = defineProps<{
    phones: Phone[];
}>();

type AdminPageProps = PageProps & {
    flash?: {
        success?: string | null;
        error?: string | null;
        scrape_logs?: ScrapeLog[];
    };
};

type ScrapeLog = {
    status: 'info' | 'saved' | 'skipped' | 'error';
    message: string;
    name?: string | null;
    price?: number;
    url?: string;
};

type ScrapeLinksResponse = {
    links: string[];
    logs: ScrapeLog[];
    message?: string;
};

type ScrapeProductResponse = {
    log: ScrapeLog;
    saved: number;
    skipped: number;
};

const page = usePage<AdminPageProps>();
const flash = computed(() => page.props.flash ?? {});
const averagePrice = computed(() => {
    if (!props.phones.length) {
        return 0;
    }

    const total = props.phones.reduce(
        (sum, phone) => sum + Number(phone.price),
        0,
    );

    return Math.round(total / props.phones.length);
});
const brandCount = computed(
    () => new Set(props.phones.map((phone) => phone.brand)).size,
);

const bulkScrapeUrl = ref('');
const bulkScraping = ref(false);
const scrapeLimit = ref('5');
const scrapeLogs = ref<ScrapeLog[]>(flash.value.scrape_logs ?? []);
const scrapeSaved = ref(0);
const scrapeSkipped = ref(0);
const scrapeExisting = ref(0);

const scrapeSummary = computed(() => ({
    total: scrapeLogs.value.filter((log) => log.status !== 'info').length,
    saved: scrapeSaved.value,
    skipped: scrapeSkipped.value,
    existing: scrapeExisting.value,
    errors: scrapeLogs.value.filter((log) => log.status === 'error').length,
}));

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

const csrfToken = () =>
    document
        .querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';

const postJson = async <T,>(url: string, payload: Record<string, unknown>) => {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(payload),
    });

    const data = (await response.json()) as T & { message?: string };

    if (!response.ok) {
        throw new Error(data.message ?? 'Scrape request failed.');
    }

    return data;
};

const addLog = (log: ScrapeLog) => {
    scrapeLogs.value.unshift(log);
};

const submitBulkScrape = async () => {
    bulkScraping.value = true;
    scrapeSaved.value = 0;
    scrapeSkipped.value = 0;
    scrapeExisting.value = 0;
    scrapeLogs.value = [];

    try {
        addLog({
            status: 'info',
            message: 'Starting scrape job.',
            url: bulkScrapeUrl.value,
        });

        const discovery = await postJson<ScrapeLinksResponse>(
            '/admin/scrape-links',
            {
                url: bulkScrapeUrl.value,
                limit: Number(scrapeLimit.value),
            },
        );

        discovery.logs.reverse().forEach(addLog);
        scrapeExisting.value = discovery.logs.filter((log) =>
            log.message.toLowerCase().includes('already in catalog'),
        ).length;

        if (discovery.links.length === 0) {
            addLog({
                status: 'skipped',
                message:
                    scrapeExisting.value > 0
                        ? 'No new product links found. Existing catalog products were skipped.'
                        : 'No product links found on this listing page.',
            });

            return;
        }

        for (const [index, link] of discovery.links.entries()) {
            addLog({
                status: 'info',
                message: `Scraping product ${index + 1} of ${discovery.links.length}.`,
                url: link,
            });

            try {
                const result = await postJson<ScrapeProductResponse>(
                    '/admin/scrape-product',
                    { url: link },
                );

                scrapeSaved.value += result.saved;
                scrapeSkipped.value += result.skipped;
                addLog(result.log);
            } catch (error) {
                scrapeSkipped.value += 1;
                addLog({
                    status: 'error',
                    message:
                        error instanceof Error
                            ? error.message
                            : 'Product scrape failed.',
                    url: link,
                });
            }
        }

        addLog({
            status: 'info',
            message: `Finished scrape. Saved ${scrapeSaved.value}, skipped ${scrapeSkipped.value}, already existed ${scrapeExisting.value}.`,
        });
        router.reload({ only: ['phones'] });
    } catch (error) {
        addLog({
            status: 'error',
            message:
                error instanceof Error ? error.message : 'Scrape job failed.',
        });
    } finally {
        bulkScraping.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0f14] text-white">
        <Navbar />

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <section
                class="mb-8 grid gap-6 border-b border-white/10 pb-8 lg:grid-cols-[1fr_360px]"
            >
                <div>
                    <p class="text-sm font-semibold text-sky-300">
                        Admin Panel
                    </p>
                    <h1
                        class="mt-3 max-w-3xl text-4xl font-bold leading-tight text-white"
                    >
                        Manage the PhoneHub catalog.
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">
                        Add phones, review scraped data, bulk remove stale
                        listings, and moderate customer reviews from one place.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <Link
                            href="/"
                            class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                        >
                            View site
                        </Link>
                        <Link
                            href="/admin/stores"
                            class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                        >
                            Manage stores
                        </Link>
                        <Link
                            href="/admin/reviews"
                            class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                        >
                            Manage reviews
                        </Link>
                        <Link
                            href="/admin/blog"
                            class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                        >
                            Manage blog
                        </Link>
                        <Link
                            href="/dashboard"
                            class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                        >
                            Dashboard
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.04] p-4"
                    >
                        <p class="text-2xl font-bold text-white">
                            {{ phones.length }}
                        </p>
                        <p class="mt-1 text-sm text-slate-400">Phones</p>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.04] p-4"
                    >
                        <p class="text-2xl font-bold text-white">
                            {{ brandCount }}
                        </p>
                        <p class="mt-1 text-sm text-slate-400">Brands</p>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/[0.04] p-4"
                    >
                        <p class="text-2xl font-bold text-white">
                            ${{ averagePrice }}
                        </p>
                        <p class="mt-1 text-sm text-slate-400">Avg price</p>
                    </div>
                </div>
            </section>

            <div
                class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >
                <div>
                    <h2 class="text-2xl font-bold text-white">Catalog tools</h2>
                    <p class="mt-1 text-sm text-slate-400">
                        Import phones or create a listing manually.
                    </p>
                </div>
                <div class="flex gap-3">
                    <Link
                        href="/admin/blog/create"
                        class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white"
                    >
                        New blog post
                    </Link>
                    <Link
                        href="/admin/phones/create"
                        class="rounded-full bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400"
                    >
                        Add phone
                    </Link>
                </div>
            </div>

            <div
                v-if="flash.success"
                class="mb-6 rounded-lg border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash.error"
                class="mb-6 rounded-lg border border-red-400/20 bg-red-400/10 px-4 py-3 text-sm text-red-200"
            >
                {{ flash.error }}
            </div>

            <div
                class="mb-8 rounded-lg border border-white/10 bg-white/[0.04] p-5"
            >
                <div class="mb-5 flex flex-col gap-1">
                    <h2 class="font-semibold text-white">Bulk scrape</h2>
                    <p class="text-sm text-slate-400">
                        Paste a Nika2u or IMobi category URL. Existing catalog
                        products are skipped, so the limit imports that many new
                        phones.
                    </p>
                </div>

                <form
                    @submit.prevent="submitBulkScrape"
                    class="grid gap-4 lg:grid-cols-[1fr_180px_180px]"
                >
                    <div>
                        <label
                            class="mb-2 block text-xs font-semibold text-slate-500"
                            >CATEGORY URL</label
                        >
                        <input
                            v-model="bulkScrapeUrl"
                            type="url"
                            placeholder="https://imobi.biz/en/products/smartphones"
                            class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-sky-300/50"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-xs font-semibold text-slate-500"
                            >LIMIT</label
                        >
                        <select
                            v-model="scrapeLimit"
                            class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50"
                        >
                            <option value="5" class="bg-slate-950">
                                5 phones
                            </option>
                            <option value="10" class="bg-slate-950">
                                10 phones
                            </option>
                            <option value="20" class="bg-slate-950">
                                20 phones
                            </option>
                            <option value="50" class="bg-slate-950">
                                50 phones
                            </option>
                            <option value="100" class="bg-slate-950">
                                All
                            </option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        :disabled="bulkScraping || !bulkScrapeUrl"
                        class="mt-6 h-11 rounded-lg bg-emerald-500 px-5 text-sm font-semibold text-white transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-50 lg:mt-6"
                    >
                        {{ bulkScraping ? 'Scraping...' : 'Start scrape' }}
                    </button>
                </form>

                <div
                    class="mt-6 rounded-lg border border-white/10 bg-[#0b0f14] p-4"
                >
                    <div
                        class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
                    >
                        <div>
                            <h3 class="font-semibold text-white">Scrape log</h3>
                            <p class="mt-1 text-sm text-slate-400">
                                Updates appear as each product is scanned,
                                saved, or skipped.
                            </p>
                        </div>
                        <div class="grid grid-cols-5 gap-2 text-center text-xs">
                            <div
                                class="rounded-md border border-white/10 bg-white/[0.04] px-3 py-2"
                            >
                                <p class="font-bold text-white">
                                    {{ scrapeSummary.total }}
                                </p>
                                <p class="text-slate-500">Total</p>
                            </div>
                            <div
                                class="rounded-md border border-emerald-400/20 bg-emerald-400/10 px-3 py-2"
                            >
                                <p class="font-bold text-emerald-200">
                                    {{ scrapeSummary.saved }}
                                </p>
                                <p class="text-emerald-200/70">Saved</p>
                            </div>
                            <div
                                class="rounded-md border border-amber-400/20 bg-amber-400/10 px-3 py-2"
                            >
                                <p class="font-bold text-amber-200">
                                    {{ scrapeSummary.skipped }}
                                </p>
                                <p class="text-amber-200/70">Skipped</p>
                            </div>
                            <div
                                class="rounded-md border border-sky-400/20 bg-sky-400/10 px-3 py-2"
                            >
                                <p class="font-bold text-sky-200">
                                    {{ scrapeSummary.existing }}
                                </p>
                                <p class="text-sky-200/70">Existing</p>
                            </div>
                            <div
                                class="rounded-md border border-red-400/20 bg-red-400/10 px-3 py-2"
                            >
                                <p class="font-bold text-red-200">
                                    {{ scrapeSummary.errors }}
                                </p>
                                <p class="text-red-200/70">Errors</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="max-h-72 space-y-2 overflow-y-auto pr-1"
                        aria-live="polite"
                    >
                        <div
                            v-if="scrapeLogs.length === 0"
                            class="rounded-md border border-dashed border-white/10 px-4 py-8 text-center text-sm text-slate-500"
                        >
                            Start a scrape to see live progress here.
                        </div>

                        <div
                            v-for="(log, index) in scrapeLogs"
                            :key="`${log.status}-${index}-${log.url ?? ''}`"
                            class="rounded-md border px-4 py-3 text-sm"
                            :class="[
                                log.status === 'saved'
                                    ? 'border-emerald-400/20 bg-emerald-400/10 text-emerald-100'
                                    : '',
                                log.status === 'skipped'
                                    ? 'border-amber-400/20 bg-amber-400/10 text-amber-100'
                                    : '',
                                log.status === 'error'
                                    ? 'border-red-400/20 bg-red-400/10 text-red-100'
                                    : '',
                                log.status === 'info'
                                    ? 'border-white/10 bg-white/[0.04] text-slate-300'
                                    : '',
                            ]"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <p class="font-medium">
                                    {{ log.message }}
                                </p>
                                <span
                                    class="shrink-0 rounded-full bg-black/20 px-2 py-0.5 text-[11px] uppercase tracking-wide"
                                >
                                    {{ log.status }}
                                </span>
                            </div>
                            <a
                                v-if="log.url"
                                :href="log.url"
                                target="_blank"
                                rel="noreferrer"
                                class="mt-2 block truncate text-xs opacity-70 transition hover:opacity-100"
                            >
                                {{ log.url }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-white/10">
                <div
                    class="flex flex-col justify-between gap-3 border-b border-white/10 bg-white/[0.04] px-5 py-4 sm:flex-row sm:items-center"
                >
                    <p class="text-sm text-slate-400">
                        {{ selectedPhones.length }} selected
                    </p>

                    <button
                        @click="deleteSelected"
                        :disabled="selectedPhones.length === 0"
                        class="rounded-full border border-red-400/30 px-4 py-2 text-sm font-semibold text-red-200 transition hover:bg-red-400/10 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Delete Selected
                    </button>
                </div>

                <table class="w-full min-w-[820px] text-sm text-white">
                    <thead
                        class="bg-white/[0.03] text-xs uppercase text-slate-500"
                    >
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
                            class="border-t border-white/10 bg-[#0f1620] transition hover:bg-white/[0.05]"
                        >
                            <td class="px-6 py-4">
                                <input
                                    type="checkbox"
                                    :value="phone.id"
                                    v-model="selectedPhones"
                                    class="h-4 w-4"
                                />
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ phone.brand }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-white">
                                {{ phone.model }}
                            </td>
                            <td class="px-6 py-4 text-sky-200">
                                ${{ phone.price }}
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ phone.ram }}
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ phone.storage }}
                            </td>

                            <td class="flex gap-3 px-6 py-4">
                                <Link
                                    :href="`/admin/phones/${phone.id}/edit`"
                                    class="font-medium text-sky-300 transition hover:text-sky-200"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="confirmDelete(phone.id)"
                                    class="font-medium text-red-300 transition hover:text-red-200"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</template>
