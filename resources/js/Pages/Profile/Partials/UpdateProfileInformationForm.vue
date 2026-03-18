<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed, nextTick, onBeforeUnmount } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

// Avatar state
const avatarOverride = ref(null); // instant preview after upload/remove

const avatarUrl = computed(() => {
    if (avatarOverride.value === 'removed') return null;
    if (avatarOverride.value) return avatarOverride.value;
    if (!user.avatar) return null;
    return `/storage/${user.avatar}?t=${avatarTimestamp.value}`;
});
const avatarTimestamp = ref(Date.now());
const userInitials = computed(() => {
    const name = user.name || '';
    const parts = name.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return (name[0] || '?').toUpperCase();
});

// Crop modal state
const showCropModal = ref(false);
const cropImageSrc = ref('');
const cropImgRef = ref(null);
const fileInputRef = ref(null);
const avatarUploading = ref(false);
const avatarError = ref('');
let cropperInstance = null;

const triggerFileInput = () => {
    fileInputRef.value?.click();
};

const onFileSelected = (event) => {
    const file = event.target.files?.[0];
    if (!file) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
        avatarError.value = 'Please select an image file.';
        return;
    }

    // Validate file size (max 10MB raw, will be compressed after crop)
    if (file.size > 10 * 1024 * 1024) {
        avatarError.value = 'Image must be less than 10MB.';
        return;
    }

    avatarError.value = '';

    const reader = new FileReader();
    reader.onload = (e) => {
        cropImageSrc.value = e.target.result;
        showCropModal.value = true;
        nextTick(() => {
            initCropper();
        });
    };
    reader.readAsDataURL(file);

    // Reset input so same file can be selected again
    event.target.value = '';
};

const initCropper = () => {
    destroyCropper();

    nextTick(() => {
        const img = cropImgRef.value;
        if (!img) return;

        cropperInstance = new Cropper(img, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.8,
            responsive: true,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    });
};

const destroyCropper = () => {
    if (cropperInstance) {
        cropperInstance.destroy();
        cropperInstance = null;
    }
};

const closeCropModal = () => {
    showCropModal.value = false;
    cropImageSrc.value = '';
    destroyCropper();
};

const saveCroppedAvatar = async () => {
    if (!cropperInstance) return;

    avatarUploading.value = true;
    avatarError.value = '';

    try {
        const canvas = cropperInstance.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            avatarError.value = 'Failed to crop image.';
            avatarUploading.value = false;
            return;
        }

        const base64 = canvas.toDataURL('image/jpeg', 0.8);

        // Instant preview
        avatarOverride.value = base64;

        const response = await axios.post(route('profile.avatar.update'), {
            avatar: base64,
        });

        avatarTimestamp.value = Date.now();
        router.reload({ only: ['auth'] });
        closeCropModal();
    } catch (error) {
        const data = error.response?.data;
        if (error.response?.status === 422) {
            avatarError.value = data?.errors?.avatar?.[0] || data?.message || 'Invalid image.';
        } else {
            avatarError.value = 'Failed to upload avatar. Please try again.';
        }
    } finally {
        avatarUploading.value = false;
    }
};

const removeAvatar = async () => {
    const confirmed = await confirmAction({
        title: 'Remove Photo?',
        text: 'Your profile photo will be deleted.',
        confirmText: 'Remove',
        danger: true,
    });
    if (!confirmed) return;
    avatarUploading.value = true;
    avatarError.value = '';
    try {
        await axios.delete(route('profile.avatar.destroy'));
        avatarOverride.value = 'removed';
        avatarTimestamp.value = Date.now();
        router.reload({ only: ['auth'] });
    } catch {
        avatarError.value = 'Failed to remove photo. Please try again.';
    } finally {
        avatarUploading.value = false;
    }
};

onBeforeUnmount(() => {
    destroyCropper();
});

// Phone verification state
const phoneStep = ref('idle'); // 'idle' | 'input' | 'verify'
const phoneInput = ref('');
const otpCode = ref('');
const phoneError = ref('');
const phoneSuccess = ref('');
const phoneLoading = ref(false);
const otpDebug = ref(null);

const hasVerifiedPhone = computed(() => !!user.phone && !!user.phone_verified_at);

const sendPhoneOtp = async () => {
    phoneError.value = '';
    phoneLoading.value = true;

    try {
        const response = await axios.post(route('profile.phone.send'), {
            phone: phoneInput.value,
        });
        phoneSuccess.value = response.data.message;
        otpDebug.value = response.data.otp_debug || null;
        phoneStep.value = 'verify';
    } catch (error) {
        const data = error.response?.data;
        if (error.response?.status === 422) {
            phoneError.value = data?.errors?.phone?.[0] || data?.message || 'Invalid phone number.';
        } else if (error.response?.status === 429) {
            phoneError.value = data?.message;
        } else {
            phoneError.value = 'Something went wrong. Please try again.';
        }
    } finally {
        phoneLoading.value = false;
    }
};

const verifyPhoneOtp = async () => {
    phoneError.value = '';
    phoneLoading.value = true;

    try {
        const response = await axios.post(route('profile.phone.verify'), {
            phone: phoneInput.value,
            otp: otpCode.value,
        });
        phoneSuccess.value = response.data.message;
        phoneStep.value = 'idle';
        otpCode.value = '';
        otpDebug.value = null;
        // Reload page to get updated user data
        router.reload({ only: ['auth'] });
    } catch (error) {
        const data = error.response?.data;
        if (error.response?.status === 422) {
            phoneError.value = data?.errors?.otp?.[0] || data?.errors?.phone?.[0] || data?.message || 'Invalid OTP.';
        } else if (error.response?.status === 429) {
            phoneError.value = data?.message;
        } else {
            phoneError.value = 'Something went wrong. Please try again.';
        }
    } finally {
        phoneLoading.value = false;
    }
};

const removePhone = async () => {
    phoneError.value = '';
    phoneLoading.value = true;

    try {
        await axios.delete(route('profile.phone.remove'));
        phoneSuccess.value = 'Phone number removed.';
        phoneStep.value = 'idle';
        phoneInput.value = '';
        router.reload({ only: ['auth'] });
    } catch {
        phoneError.value = 'Failed to remove phone number.';
    } finally {
        phoneLoading.value = false;
    }
};

const updateNotificationPref = async (key, value) => {
    try {
        await axios.put(route('notifications.preferences'), {
            notification_email: key === 'notification_email' ? value : user.notification_email,
            notification_push: key === 'notification_push' ? value : user.notification_push,
        });
        router.reload({ only: ['auth'] });
    } catch {
        // Silently fail, checkbox will revert on reload
    }
};

const cancelPhoneEdit = () => {
    phoneStep.value = 'idle';
    phoneInput.value = '';
    otpCode.value = '';
    phoneError.value = '';
    phoneSuccess.value = '';
    otpDebug.value = null;
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <!-- Avatar Section -->
        <div class="mt-6 flex items-center gap-5">
            <div class="relative">
                <!-- Avatar display -->
                <div
                    v-if="avatarUrl"
                    class="h-20 w-20 rounded-full overflow-hidden ring-2 ring-gray-200"
                >
                    <img
                        :src="avatarUrl"
                        :alt="user.name"
                        class="h-full w-full object-cover"
                    />
                </div>
                <div
                    v-else
                    class="h-20 w-20 rounded-full bg-primary-100 ring-2 ring-primary-200 flex items-center justify-center"
                >
                    <span class="text-2xl font-semibold text-primary-700">{{ userInitials }}</span>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="triggerFileInput"
                        class="px-4 py-2 text-sm font-medium text-primary-600 border border-primary-300 rounded-lg hover:bg-primary-50 transition-colors"
                    >
                        {{ avatarUrl ? 'Change Photo' : 'Upload Photo' }}
                    </button>
                    <button
                        v-if="avatarUrl"
                        type="button"
                        @click="removeAvatar"
                        :disabled="avatarUploading"
                        class="px-4 py-2 text-sm font-medium text-red-600 border border-red-300 rounded-lg hover:bg-red-50 transition-colors disabled:opacity-50"
                    >
                        Remove
                    </button>
                </div>
                <input
                    ref="fileInputRef"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="onFileSelected"
                />
                <p v-if="avatarError" class="mt-1.5 text-sm text-red-600">{{ avatarError }}</p>
            </div>
        </div>

        <!-- Crop Modal -->
        <Modal :show="showCropModal" max-width="lg" @close="closeCropModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Crop Photo</h3>

                <div class="relative w-full rounded-lg overflow-hidden" style="max-height: 400px;">
                    <img
                        v-if="cropImageSrc"
                        ref="cropImgRef"
                        :src="cropImageSrc"
                        alt="Crop preview"
                        style="display: block; max-width: 100%;"
                    />
                </div>

                <p v-if="avatarError" class="mt-3 text-sm text-red-600">{{ avatarError }}</p>

                <div class="mt-5 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="closeCropModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="saveCroppedAvatar"
                        :disabled="avatarUploading"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50"
                    >
                        {{ avatarUploading ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>
        </Modal>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>

        <!-- Phone Number Section (separate from main form, verified via OTP) -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Phone Number</h3>
            <p class="mt-1 text-sm text-gray-600">
                A verified phone number is required to create or join groups. Phone will be verified via OTP.
            </p>

            <!-- Current phone display -->
            <div v-if="hasVerifiedPhone && phoneStep === 'idle'" class="mt-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                        <span class="text-gray-900 font-medium">+91 {{ user.phone }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            Verified
                        </span>
                    </div>
                    <button
                        type="button"
                        @click="phoneStep = 'input'; phoneInput = ''; phoneError = ''; phoneSuccess = '';"
                        class="text-sm text-primary-600 hover:text-primary-700 font-medium"
                    >
                        Change
                    </button>
                    <button
                        type="button"
                        @click="removePhone"
                        class="text-sm text-red-600 hover:text-red-700 font-medium"
                        :disabled="phoneLoading"
                    >
                        Remove
                    </button>
                </div>
            </div>

            <!-- No phone yet -->
            <div v-if="!hasVerifiedPhone && phoneStep === 'idle'" class="mt-4">
                <button
                    type="button"
                    @click="phoneStep = 'input'; phoneError = ''; phoneSuccess = '';"
                    class="px-4 py-2 text-sm font-medium text-primary-600 border border-primary-300 rounded-lg hover:bg-primary-50 transition-colors"
                >
                    Add Phone Number
                </button>
            </div>

            <!-- Step 1: Enter phone number -->
            <div v-if="phoneStep === 'input'" class="mt-4 space-y-3">
                <div>
                    <InputLabel for="phone_new" value="Mobile Number" />
                    <div class="mt-1 flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            +91
                        </span>
                        <input
                            id="phone_new"
                            type="tel"
                            class="flex-1 rounded-r-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                            v-model="phoneInput"
                            placeholder="9876543210"
                            maxlength="10"
                        />
                    </div>
                    <p v-if="phoneError" class="mt-2 text-sm text-red-600">{{ phoneError }}</p>
                    <p v-if="phoneSuccess" class="mt-2 text-sm text-green-600">{{ phoneSuccess }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="sendPhoneOtp"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50"
                        :disabled="phoneLoading || phoneInput.length !== 10"
                    >
                        {{ phoneLoading ? 'Sending...' : 'Send OTP' }}
                    </button>
                    <button
                        type="button"
                        @click="cancelPhoneEdit"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800"
                    >
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Step 2: Verify OTP -->
            <div v-if="phoneStep === 'verify'" class="mt-4 space-y-3">
                <p class="text-sm text-gray-600">
                    OTP sent to <span class="font-medium text-gray-900">+91 {{ phoneInput }}</span>
                    <button type="button" @click="phoneStep = 'input'; otpCode = ''; phoneError = '';" class="ml-2 text-primary-600 hover:text-primary-700 text-sm font-medium">
                        Change
                    </button>
                </p>

                <div v-if="otpDebug" class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <span class="font-medium">Dev Mode OTP:</span> {{ otpDebug }}
                    </p>
                </div>

                <div>
                    <InputLabel for="phone_otp" value="Enter OTP" />
                    <input
                        id="phone_otp"
                        type="text"
                        inputmode="numeric"
                        class="mt-1 block w-48 rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-center text-lg tracking-widest"
                        v-model="otpCode"
                        placeholder="------"
                        maxlength="6"
                    />
                    <p v-if="phoneError" class="mt-2 text-sm text-red-600">{{ phoneError }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="verifyPhoneOtp"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50"
                        :disabled="phoneLoading || otpCode.length !== 6"
                    >
                        {{ phoneLoading ? 'Verifying...' : 'Verify & Save' }}
                    </button>
                    <button
                        type="button"
                        @click="sendPhoneOtp"
                        class="text-sm text-primary-600 hover:text-primary-700 font-medium disabled:opacity-50"
                        :disabled="phoneLoading"
                    >
                        Resend OTP
                    </button>
                    <button
                        type="button"
                        @click="cancelPhoneEdit"
                        class="text-sm text-gray-600 hover:text-gray-800 font-medium"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Notification Preferences Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Notification Preferences</h3>
            <p class="mt-1 text-sm text-gray-600">
                Choose how you want to receive notifications.
            </p>

            <div class="mt-4 space-y-4">
                <label class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        :checked="user.notification_email"
                        @change="updateNotificationPref('notification_email', $event.target.checked)"
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                    />
                    <div>
                        <span class="text-sm font-medium text-gray-900">Email Notifications</span>
                        <p class="text-xs text-gray-500">Receive notifications via email (group expenses, settlements, etc.)</p>
                    </div>
                </label>

                <label class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        :checked="user.notification_push"
                        @change="updateNotificationPref('notification_push', $event.target.checked)"
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                    />
                    <div>
                        <span class="text-sm font-medium text-gray-900">Push Notifications</span>
                        <p class="text-xs text-gray-500">Receive push notifications in browser (coming soon)</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Email Verification Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Email Verification</h3>
            <p class="mt-1 text-sm text-gray-600">
                Verify your email address to ensure account security.
            </p>

            <div class="mt-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                        <span class="text-gray-900 font-medium">{{ user.email }}</span>
                        <span
                            v-if="user.email_verified_at"
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"
                        >
                            Verified
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700"
                        >
                            Unverified
                        </span>
                    </div>
                </div>

                <!-- Verification action for unverified email -->
                <div v-if="!user.email_verified_at" class="mt-3">
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="px-4 py-2 text-sm font-medium text-primary-600 border border-primary-300 rounded-lg hover:bg-primary-50 transition-colors"
                    >
                        Send Verification Email
                    </Link>

                    <p
                        v-if="status === 'verification-link-sent'"
                        class="mt-2 text-sm font-medium text-green-600"
                    >
                        A new verification link has been sent to your email address.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
