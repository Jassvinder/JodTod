<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    site_name: props.settings.site_name,
    default_currency: props.settings.default_currency,
    maintenance_mode: props.settings.maintenance_mode,
});

function save() {
    form.put(route('admin.settings.update'));
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
