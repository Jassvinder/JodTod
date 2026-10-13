<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import Icon from '@/Components/Shared/Icon.vue';
import { WELCOME_SLIDES, closeIntro, onboarding } from '@/Utils/onboarding.js';

// First-login intro slides. Replayable from Help & Tips.
const index = ref(0);
const slide = computed(() => WELCOME_SLIDES[index.value]);
const isLast = computed(() => index.value === WELCOME_SLIDES.length - 1);

const TONES = {
    primary: 'bg-primary-50 text-primary-600 dark:bg-primary-900/40 dark:text-primary-300',
    success: 'bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-300',
    amber: 'bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300',
    pink: 'bg-pink-50 text-pink-600 dark:bg-pink-900/40 dark:text-pink-300',
};

watch(() => onboarding.introOpen, (open) => {
    if (open) index.value = 0;
    document.body.style.overflow = open ? 'hidden' : '';
});

function next() {
    if (isLast.value) closeIntro();
    else index.value++;
}

function back() {
    if (index.value > 0) index.value--;
}

function onKeydown(e) {
    if (!onboarding.introOpen) return;
    if (e.key === 'Escape') closeIntro();
    else if (e.key === 'ArrowRight') next();
    else if (e.key === 'ArrowLeft') back();
}

onMounted(() => document.addEventListener('keydown', onKeydown));
onUnmounted(() => {
    document.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="opacity-0"
        >
            <div
                v-if="onboarding.introOpen"
                class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm"
                role="dialog"
                aria-modal="true"
                aria-labelledby="welcome-title"
            >
                <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
                    <button
                        v-if="!isLast"
                        type="button"
                        class="absolute right-3 top-3 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                        @click="closeIntro"
                    >
                        Skip
                    </button>

                    <div class="px-8 pb-6 pt-12 text-center">
                        <Transition
                            mode="out-in"
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 translate-y-2"
                            leave-active-class="transition duration-100 ease-in"
                            leave-to-class="opacity-0"
                        >
                            <div :key="index">
                                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full" :class="TONES[slide.tone]">
                                    <Icon :name="slide.icon" class="h-12 w-12" stroke-width="1.5" />
                                </div>
                                <h2 id="welcome-title" class="mt-6 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ slide.title }}</h2>
                                <p class="mt-3 text-base leading-relaxed text-gray-600 dark:text-gray-300">{{ slide.text }}</p>
                                <p v-if="isLast" class="mt-5 text-sm text-gray-500 dark:text-gray-400">
                                    Click <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-gray-300 text-xs font-semibold dark:border-gray-600">?</span>
                                    on any page for quick tips. Help &amp; Tips in the menu explains every feature.
                                </p>
                            </div>
                        </Transition>
                    </div>

                    <div class="flex justify-center gap-2 pb-6">
                        <button
                            v-for="(item, i) in WELCOME_SLIDES"
                            :key="item.title"
                            type="button"
                            class="h-2 rounded-full transition-all"
                            :class="i === index ? 'w-6 bg-primary-600' : 'w-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600'"
                            :aria-label="`Slide ${i + 1}`"
                            @click="index = i"
                        />
                    </div>

                    <div class="flex gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800/60">
                        <button
                            v-if="index > 0"
                            type="button"
                            class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-white dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                            @click="back"
                        >
                            Back
                        </button>
                        <button
                            type="button"
                            class="flex-[2] rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700"
                            @click="next"
                        >
                            {{ isLast ? 'Get Started' : 'Next' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
