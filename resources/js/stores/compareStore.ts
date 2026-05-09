import { defineStore } from 'pinia';
import type { Phone } from '@/types/Phone';

export const useCompareStore = defineStore('compare', {
    state: () => ({
        phones: [] as Phone[],
    }),

    actions: {
        addPhone(phone: Phone) {
            if (this.phones.length >= 3) {
                return;
            } // max 3 phones

            if (this.phones.find((p) => p.id === phone.id)) {
                return; // no duplicates
            }

            this.phones.push(phone);
        },

        removePhone(id: number) {
            this.phones = this.phones.filter((p) => p.id !== id);
        },

        clearAll() {
            this.phones = [];
        },
    },
    persist: {
        storage: localStorage,
    },
});
