<script setup lang="ts">
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Navbar from '@/Components/Site/Navbar.vue';
import type { Phone } from '@/types/Phone';

const page = usePage();
const user = computed(() => (page.props.auth as { user: any })?.user ?? null);

const props = defineProps<{
    phones: Phone[];
    compareIds: string[];
    comparePhones: Phone[];
}>();

const specs = [
    { label: 'Brand', key: 'brand' },
    { label: 'Model', key: 'model' },
    { label: 'Price', key: 'price' },
    { label: 'RAM', key: 'ram' },
    { label: 'Storage', key: 'storage' },
    { label: 'Camera', key: 'camera' },
    { label: 'Battery', key: 'battery' },
    { label: 'Screen Size', key: 'screen_size' },
    { label: 'OS', key: 'os' },
    { label: 'Rating', key: 'rating' },
];

const availablePhones = computed(() => {
    return props.phones.filter(
        (p) => !props.compareIds.includes(String(p.id)),
    );
});

const saving = ref(false);
const saveMessage = ref('');

const addPhone = (phoneId: number) => {
    const ids = [...props.compareIds, String(phoneId)];
    router.get('/compare', { ids: ids.join(',') });
};

const removePhone = (phoneId: number) => {
    const ids = props.compareIds.filter((id) => Number(id) !== phoneId);
    router.get('/compare', { ids: ids.length ? ids.join(',') : '' });
};

const clearAll = () => {
    router.get('/compare');
};

const saveComparison = () => {
    if (!user.value) {
        const guestSaved = JSON.parse(localStorage.getItem('savedComparisons') || '[]');
        const newComp = {
            id: Date.now(),
            name: 'Comparison (' + new Date().toLocaleDateString() + ')',
            phone_ids: props.compareIds.map(Number),
            phones: props.comparePhones,
            created_at: new Date().toISOString(),
        };
        guestSaved.unshift(newComp);
        localStorage.setItem('savedComparisons', JSON.stringify(guestSaved.slice(0, 10)));
        saveMessage.value = 'Saved to browser! Check your dashboard.';
        setTimeout(() => { saveMessage.value = ''; }, 3000);
        return;
    }

    saving.value = true;
    router.post('/saved-comparisons', {
        name: 'Comparison (' + new Date().toLocaleDateString() + ')',
        phone_ids: props.compareIds.map(Number),
    }, {
        preserveScroll: true,
        onSuccess: () => {
            saveMessage.value = 'Saved! View in your dashboard.';
            setTimeout(() => { saveMessage.value = ''; }, 3000);
            saving.value = false;
        },
        onError: () => {
            saveMessage.value = 'Failed to save. Try again.';
            setTimeout(() => { saveMessage.value = ''; }, 3000);
            saving.value = false;
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-950">
        <Navbar />

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h1 class="text-4xl font-bold text-white">Compare Phones</h1>
                <p class="mt-2 text-sm text-gray-400">Select up to 3 phones to compare</p>
            </div>

            <div v-if="saveMessage" class="mb-6 rounded-lg border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-center text-sm text-emerald-300">
                {{ saveMessage }}
            </div>

            <!-- Slots -->
            <div class="mb-10 flex justify-center gap-6">
                <div v-for="index in 3" :key="index" class="w-full max-w-xs">
                    <div v-if="comparePhones[index - 1]" class="overflow-hidden rounded-2xl bg-gray-800 shadow-lg">
                        <img :src="comparePhones[index - 1].image" :alt="comparePhones[index - 1].model" class="h-52 w-full object-cover" />
                        <div class="p-5 text-center">
                            <p class="text-xs tracking-wide text-gray-400 uppercase">{{ comparePhones[index - 1].brand }}</p>
                            <h3 class="mt-1 text-lg font-semibold text-white">{{ comparePhones[index - 1].model }}</h3>
                            <button @click="removePhone(comparePhones[index - 1].id)" class="mt-3 text-sm text-red-400 transition-colors hover:text-red-300">Remove</button>
                        </div>
                    </div>

                    <div v-else class="flex h-full min-h-[320px] flex-col items-center justify-center rounded-2xl border border-dashed border-gray-600 bg-gray-800 p-6">
                        <p class="mb-4 text-sm text-gray-500">Add a phone</p>
                        <select class="w-full rounded-xl bg-gray-700 px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            @change="(e) => { const val = (e.target as HTMLSelectElement).value; if (val) addPhone(Number(val)); (e.target as HTMLSelectElement).value = ''; }">
                            <option value="">Select a phone...</option>
                            <option v-for="phone in availablePhones" :key="phone.id" :value="phone.id">{{ phone.brand }} {{ phone.model }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Specs Table -->
            <div v-if="comparePhones.length > 0" class="overflow-hidden rounded-2xl border border-gray-800">
                <div v-for="(spec, index) in specs" :key="spec.key" class="flex" :class="index % 2 === 0 ? 'bg-gray-900' : 'bg-gray-800'">
                    <div class="w-40 shrink-0 border-r border-gray-700 px-6 py-4 text-sm font-medium text-gray-400">{{ spec.label }}</div>
                    <div v-for="phone in comparePhones" :key="phone.id" class="flex-1 border-r border-gray-700 px-6 py-4 text-sm text-white last:border-0">
                        {{ spec.key === 'price' ? '$' + phone[spec.key as keyof typeof phone] : phone[spec.key as keyof typeof phone] }}
                    </div>
                </div>
            </div>

            <div v-else class="rounded-2xl border border-dashed border-gray-700 py-20 text-center">
                <p class="text-gray-500">Add phones to start comparing</p>
            </div>

            <!-- Actions -->
            <div v-if="comparePhones.length > 0" class="mt-6 flex items-center justify-center gap-4">
                <button @click="clearAll()" class="rounded-full border border-white/10 px-6 py-2.5 text-sm text-gray-400 transition-colors hover:text-white">Clear all</button>
                <button @click="saveComparison()" :disabled="saving" class="rounded-full bg-blue-600 px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-500 disabled:opacity-50">
                    {{ saving ? 'Saving...' : 'Save Comparison' }}
                </button>
            </div>
        </div>
    </div>
</template>
