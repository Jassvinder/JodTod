<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Shared/Icon.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { copyText } from '@/Utils/clipboard.js';

const props = defineProps({
    support: { type: Object, required: true },
});

const hasPaymentDetails = computed(() => Boolean(props.support.qr_url || props.support.upi_id));

const copied = ref(false);
let resetTimer = null;

async function copyUpiId() {
    await copyText(props.support.upi_id);
    copied.value = true;
    clearTimeout(resetTimer);
    resetTimer = setTimeout(() => { copied.value = false; }, 1800);
}

const qrFileName = computed(() => {
    const ext = (props.support.qr_url || '').split('.').pop().split('?')[0] || 'png';
    return `jodtod-support-qr.${ext}`;
});
</script>

<template>
    <Head title="Support JodTod" />

    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Support JodTod</h2>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Help keep JodTod running.</p>
            </div>

            <!-- Free-for-now note -->
            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start gap-4">
                    <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-500 dark:bg-rose-900/30 dark:text-rose-300">
                        <Icon name="heart" class="h-6 w-6" />
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ support.title }}</h3>
                        <div class="mt-2 space-y-3 text-gray-600 dark:text-gray-300">
                            <p v-for="(paragraph, index) in support.paragraphs" :key="index">{{ paragraph }}</p>
                        </div>
                        <p v-if="support.early_users_note" class="mt-3 font-medium text-primary-700 dark:text-primary-300">{{ support.early_users_note }}</p>
                    </div>
                </div>
            </div>

            <!-- UPI payment -->
            <div v-if="hasPaymentDetails" class="mt-6 rounded-xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pay with UPI</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Any amount is welcome. Donations are voluntary and do not unlock features.</p>

                <div class="mt-5 flex flex-col items-center gap-6 sm:flex-row sm:items-start">
                    <!-- QR -->
                    <div v-if="support.qr_url" class="flex flex-col items-center gap-3">
                        <div class="rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-600">
                            <img :src="support.qr_url" alt="JodTod UPI QR code" class="h-56 w-56 object-contain" />
                        </div>
                        <a
                            :href="support.qr_url"
                            :download="qrFileName"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary-700"
                        >
                            <Icon name="download" class="h-4 w-4" />
                            Save QR Code
                        </a>
                    </div>

                    <!-- Steps + UPI ID -->
                    <div class="w-full min-w-0 flex-1">
                        <ol class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li v-for="(step, index) in support.pay_steps" :key="index" class="flex gap-3">
                                <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-primary-50 text-xs font-semibold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">{{ index + 1 }}</span>
                                <span class="pt-0.5">{{ step }}</span>
                            </li>
                        </ol>

                        <div v-if="support.upi_id" class="mt-5">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">UPI ID</p>
                            <div class="mt-1 flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                                <span class="min-w-0 flex-1 truncate font-mono font-semibold text-gray-900 dark:text-gray-100">{{ support.upi_id }}</span>
                                <button
                                    type="button"
                                    class="inline-flex flex-shrink-0 items-center gap-1 rounded-md px-2 py-1 text-sm font-medium text-primary-700 transition-colors hover:bg-primary-50 dark:text-primary-300 dark:hover:bg-primary-900/30"
                                    @click="copyUpiId"
                                >
                                    <Icon :name="copied ? 'check' : 'copy'" class="h-4 w-4" />
                                    {{ copied ? 'Copied' : 'Copy' }}
                                </button>
                            </div>
                            <p v-if="support.payee_name" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Payee: {{ support.payee_name }}</p>

                            <!-- UPI deep links only open on phones with a UPI app -->
                            <a
                                v-if="support.upi_link"
                                :href="support.upi_link"
                                class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-primary-600 px-4 py-2 text-sm font-medium text-primary-700 transition-colors hover:bg-primary-50 dark:text-primary-300 dark:hover:bg-primary-900/30 lg:hidden"
                            >
                                Pay with UPI App
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">Thank you for using JodTod. ❤️</p>
        </div>
    </AppLayout>
</template>
