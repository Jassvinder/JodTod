<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ status: String });

const form = useForm({ email: '' });

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <h2 class="text-xl font-semibold text-gray-900 mb-4">Password bhool gaye?</h2>

        <p class="mb-4 text-sm text-gray-600">
            Koi baat nahi! Apna email daalo, hum reset link bhej denge.
        </p>

        <div v-if="status" class="mb-4 text-sm font-medium text-success-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <button
                type="submit"
                class="mt-6 w-full py-3 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors disabled:opacity-50"
                :disabled="form.processing"
            >
                Reset Link Bhejo
            </button>

            <p class="mt-4 text-center text-sm text-gray-600">
                <Link :href="route('login')" class="text-primary-600 font-medium hover:text-primary-700">Login pe wapas jaayein</Link>
            </p>
        </form>
    </GuestLayout>
</template>
