<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const activeTab = ref('email');

// Email & Password form
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

// OTP form
const otpStep = ref('phone'); // 'phone' or 'verify'
const otpPhone = ref('');
const otpCode = ref('');
const otpError = ref('');
const otpSuccess = ref('');
const otpLoading = ref(false);
const otpDebug = ref(null);

const sendOtp = async () => {
    otpError.value = '';
    otpSuccess.value = '';
    otpLoading.value = true;

    try {
        const response = await axios.post(route('otp.send'), {
            phone: otpPhone.value,
        });
        otpSuccess.value = response.data.message;
        otpDebug.value = response.data.otp_debug || null;
        otpStep.value = 'verify';
    } catch (error) {
        const data = error.response?.data;
        if (error.response?.status === 422) {
            otpError.value = data?.errors?.phone?.[0] || data?.message || 'Invalid phone number.';
        } else if (error.response?.status === 429) {
            otpError.value = data?.message;
        } else {
            otpError.value = 'Something went wrong. Please try again.';
        }
    } finally {
        otpLoading.value = false;
    }
};

const verifyOtp = async () => {
    otpError.value = '';
    otpLoading.value = true;

    try {
        const response = await axios.post(route('otp.verify'), {
            phone: otpPhone.value,
            otp: otpCode.value,
        });
        router.visit(response.data.redirect || '/dashboard');
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            otpError.value = errors?.otp?.[0] || errors?.phone?.[0] || error.response.data.message || 'Invalid OTP.';
        } else if (error.response?.status === 429) {
            otpError.value = error.response.data.message;
        } else {
            otpError.value = 'Something went wrong. Please try again.';
        }
    } finally {
        otpLoading.value = false;
    }
};

const changePhone = () => {
    otpStep.value = 'phone';
    otpCode.value = '';
    otpError.value = '';
    otpSuccess.value = '';
    otpDebug.value = null;
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />

        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Log in to your account</h2>

        <div v-if="status" class="mb-4 text-sm font-medium text-success-600">
            {{ status }}
        </div>

        <!-- Login Method Tabs -->
        <div class="flex mb-6 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <button
                type="button"
                @click="activeTab = 'email'"
                :class="[
                    'flex-1 py-2.5 text-sm font-medium transition-colors',
                    activeTab === 'email'
                        ? 'bg-primary-600 text-white'
                        : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
                ]"
            >
                Email &amp; Password
            </button>
            <button
                type="button"
                @click="activeTab = 'otp'"
                :class="[
                    'flex-1 py-2.5 text-sm font-medium transition-colors',
                    activeTab === 'otp'
                        ? 'bg-primary-600 text-white'
                        : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
                ]"
            >
                OTP Login
            </button>
        </div>

        <!-- Email & Password Tab -->
        <form v-if="activeTab === 'email'" @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-primary-600 hover:text-primary-700"
                >
                    Forgot password?
                </Link>
            </div>

            <button
                type="submit"
                class="mt-6 w-full py-3 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors disabled:opacity-50"
                :disabled="form.processing"
            >
                Login
            </button>
        </form>

        <!-- OTP Login Tab -->
        <div v-if="activeTab === 'otp'">
            <!-- Step 1: Enter Phone -->
            <form v-if="otpStep === 'phone'" @submit.prevent="sendOtp">
                <div>
                    <InputLabel for="phone" value="Mobile Number" />
                    <div class="mt-1 flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                            +91
                        </span>
                        <input
                            id="phone"
                            type="tel"
                            class="flex-1 rounded-r-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                            v-model="otpPhone"
                            placeholder="9876543210"
                            maxlength="10"
                            required
                        />
                    </div>
                    <p v-if="otpError" class="mt-2 text-sm text-red-600">{{ otpError }}</p>
                </div>

                <button
                    type="submit"
                    class="mt-6 w-full py-3 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors disabled:opacity-50"
                    :disabled="otpLoading"
                >
                    {{ otpLoading ? 'Sending...' : 'Send OTP' }}
                </button>
            </form>

            <!-- Step 2: Verify OTP -->
            <form v-if="otpStep === 'verify'" @submit.prevent="verifyOtp">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    OTP sent to <span class="font-medium text-gray-900 dark:text-gray-100">+91 {{ otpPhone }}</span>
                    <button type="button" @click="changePhone" class="ml-2 text-primary-600 hover:text-primary-700 text-sm font-medium">
                        Change
                    </button>
                </p>

                <!-- Dev mode: Show OTP -->
                <div v-if="otpDebug" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <span class="font-medium">Dev Mode OTP:</span> {{ otpDebug }}
                    </p>
                </div>

                <div>
                    <InputLabel for="otp" value="Enter OTP" />
                    <input
                        id="otp"
                        type="text"
                        inputmode="numeric"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500 text-center text-lg tracking-widest"
                        v-model="otpCode"
                        placeholder="------"
                        maxlength="6"
                        required
                        autofocus
                    />
                    <p v-if="otpError" class="mt-2 text-sm text-red-600">{{ otpError }}</p>
                </div>

                <button
                    type="submit"
                    class="mt-6 w-full py-3 px-4 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors disabled:opacity-50"
                    :disabled="otpLoading || otpCode.length !== 6"
                >
                    {{ otpLoading ? 'Verifying...' : 'Verify & Login' }}
                </button>

                <button
                    type="button"
                    @click="sendOtp"
                    class="mt-3 w-full py-2 text-sm text-primary-600 hover:text-primary-700 font-medium disabled:opacity-50"
                    :disabled="otpLoading"
                >
                    Resend OTP
                </button>
            </form>
        </div>

        <!-- Divider -->
        <div class="mt-6 flex items-center gap-3">
            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
            <span class="text-sm text-gray-400">or</span>
            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
        </div>

        <!-- Google Login -->
        <a
            :href="route('auth.google')"
            class="mt-4 w-full flex items-center justify-center gap-3 py-3 px-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Continue with Google
        </a>

        <p class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
            Don't have an account?
            <Link :href="route('register')" class="text-primary-600 font-medium hover:text-primary-700">Register</Link>
        </p>
    </GuestLayout>
</template>
