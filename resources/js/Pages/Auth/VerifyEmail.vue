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

        <h2 class="text-xl font-semibold text-gray-900 mb-4">Email verify karein</h2>

        <p class="mb-4 text-sm text-gray-600">
            Sign up ke liye shukriya! Shuru karne se pehle, apna email verify kar lo - humne ek link bheja hai tumhare email pe.
        </p>

        <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-success-600">
            Ek naya verification link bhej diya gaya hai tumhare email pe.
        </div>

        <form @submit.prevent="submit">
            <button
                type="submit"
                class="w-full py-3 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50"
                :disabled="form.processing"
            >
                Verification Email Dobara Bhejo
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
