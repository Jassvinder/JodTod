<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    settings: Object,
    supportQrUrl: String,
});

const form = useForm({
    site_name: props.settings.site_name,
    default_currency: props.settings.default_currency,
    maintenance_mode: props.settings.maintenance_mode,
    support_upi_id: props.settings.support_upi_id,
    support_payee_name: props.settings.support_payee_name,
    support_qr: null,
    remove_support_qr: false,
});

const qrInput = ref(null);
const qrPreview = ref(null);
const shownQrUrl = computed(() => {
    if (qrPreview.value) return qrPreview.value;
    return form.remove_support_qr ? null : props.supportQrUrl;
});

function pickQr(event) {
    const file = event.target.files[0];
    if (!file) return;
    form.support_qr = file;
    form.remove_support_qr = false;
    qrPreview.value = URL.createObjectURL(file);
}

function removeQr() {
    form.support_qr = null;
    form.remove_support_qr = true;
    qrPreview.value = null;
    if (qrInput.value) qrInput.value.value = '';
}

function save() {
    // Files need multipart, so send POST with a PUT override.
    form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.settings.update'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.support_qr = null;
            form.remove_support_qr = false;
            qrPreview.value = null;
            if (qrInput.value) qrInput.value.value = '';
        },
    });
}
</script>

<template>
    <Head title="Settings" />

    <AdminLayout>
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Site Settings</h1>

            <form @submit.prevent="save" class="space-y-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <!-- Site Name -->
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Site Name</label>
                    <input
                        id="site_name"
                        v-model="form.site_name"
                        type="text"
                        maxlength="100"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                    />
                    <p v-if="form.errors.site_name" class="mt-1 text-sm text-red-600">{{ form.errors.site_name }}</p>
                </div>

                <!-- Default Currency -->
                <div>
                    <label for="default_currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Currency</label>
                    <select
                        id="default_currency"
                        v-model="form.default_currency"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                    >
                        <option value="INR">₹ INR - Indian Rupee</option>
                        <option value="USD">$ USD - US Dollar</option>
                        <option value="EUR">€ EUR - Euro</option>
                        <option value="GBP">£ GBP - British Pound</option>
                        <option value="AED">د.إ AED - UAE Dirham</option>
                        <option value="SAR">﷼ SAR - Saudi Riyal</option>
                    </select>
                    <p v-if="form.errors.default_currency" class="mt-1 text-sm text-red-600">{{ form.errors.default_currency }}</p>
                </div>

                <!-- Maintenance Mode -->
                <div>
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maintenance Mode</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">When enabled, only admins can access the site.</p>
                        </div>
                        <button
                            type="button"
                            @click="form.maintenance_mode = form.maintenance_mode === '1' ? '0' : '1'"
                            :class="[
                                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2',
                                form.maintenance_mode === '1' ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-600'
                            ]"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                    form.maintenance_mode === '1' ? 'translate-x-5' : 'translate-x-0'
                                ]"
                            />
                        </button>
                    </div>
                </div>

                <!-- Support JodTod (UPI) -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Support JodTod (UPI)</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Shown to users on the Support JodTod page (web and mobile app). Leave empty to hide the payment details.</p>
                    </div>

                    <div>
                        <label for="support_upi_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UPI ID</label>
                        <input
                            id="support_upi_id"
                            v-model.trim="form.support_upi_id"
                            type="text"
                            maxlength="100"
                            placeholder="name@bank"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                        />
                        <p v-if="form.errors.support_upi_id" class="mt-1 text-sm text-red-600">{{ form.errors.support_upi_id }}</p>
                    </div>

                    <div>
                        <label for="support_payee_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payee Name</label>
                        <input
                            id="support_payee_name"
                            v-model="form.support_payee_name"
                            type="text"
                            maxlength="100"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Shown in the UPI app when a user taps "Pay with UPI App".</p>
                        <p v-if="form.errors.support_payee_name" class="mt-1 text-sm text-red-600">{{ form.errors.support_payee_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UPI QR Code</label>
                        <div class="flex items-start gap-4">
                            <div class="w-28 h-28 flex-shrink-0 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                <img v-if="shownQrUrl" :src="shownQrUrl" alt="UPI QR code" class="w-full h-full object-contain bg-white" />
                                <span v-else class="text-xs text-gray-400 text-center px-2">No QR</span>
                            </div>
                            <div class="space-y-2">
                                <input
                                    ref="qrInput"
                                    type="file"
                                    accept="image/png,image/jpeg,image/webp"
                                    class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-primary-700 hover:file:bg-primary-100"
                                    @change="pickQr"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or WebP, up to 2 MB.</p>
                                <button
                                    v-if="shownQrUrl"
                                    type="button"
                                    class="text-sm font-medium text-red-600 hover:text-red-700"
                                    @click="removeQr"
                                >
                                    Remove QR
                                </button>
                                <p v-if="form.errors.support_qr" class="text-sm text-red-600">{{ form.errors.support_qr }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save -->
                <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50"
                    >
                        Save Settings
                    </button>
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600">Saved.</p>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
