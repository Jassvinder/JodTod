<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ status: String });

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Email Verify Karein" />

        <h2 class="text-xl font-semibold text-gray-900 mb-4">Verify your email</h2>

        <p class="mb-4 text-sm text-gray-600">
            Thanks for signing up! Before getting started, please verify your email address by clicking the link we just sent you.
        </p>

        <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-success-600">
            A new verification link has been sent to your email address.
        </div>

        <form @submit.prevent="submit">
            <button
                type="submit"
                class="w-full py-3 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50"
                :disabled="form.processing"
            >
                Resend Verification Email
            </button>

            <div class="mt-4 text-center">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Logout
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
