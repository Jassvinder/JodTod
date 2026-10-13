<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Shared/Icon.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { HELP_TOPICS, openIntro, resetTips } from '@/Utils/onboarding.js';

const tipsReset = ref(false);

function showAllTipsAgain() {
    resetTips();
    tipsReset.value = true;
}
</script>

<template>
    <Head title="Help & Tips" />

    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Help &amp; Tips</h2>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Everything JodTod can do, and quick ways to learn it.</p>
            </div>

            <!-- Replay actions -->
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <button
                    type="button"
                    class="group flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-5 text-left transition-colors hover:border-primary-300 hover:bg-primary-50/40 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-primary-700 dark:hover:bg-primary-900/20"
                    @click="openIntro"
                >
                    <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/40 dark:text-primary-300">
                        <Icon name="play" class="h-6 w-6" />
                    </span>
                    <span>
                        <span class="block font-semibold text-gray-900 dark:text-gray-100">Watch Intro</span>
                        <span class="mt-0.5 block text-sm text-gray-500 dark:text-gray-400">See the welcome slides again.</span>
                    </span>
                </button>

                <button
                    type="button"
                    class="group flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-5 text-left transition-colors hover:border-primary-300 hover:bg-primary-50/40 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-primary-700 dark:hover:bg-primary-900/20"
                    @click="showAllTipsAgain"
                >
                    <span
                        class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg"
                        :class="tipsReset ? 'bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-300' : 'bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300'"
                    >
                        <Icon :name="tipsReset ? 'check' : 'refresh'" class="h-6 w-6" />
                    </span>
                    <span>
                        <span class="block font-semibold text-gray-900 dark:text-gray-100">Show All Tips Again</span>
                        <span class="mt-0.5 block text-sm text-gray-500 dark:text-gray-400">
                            {{ tipsReset ? 'Done. Each page will show its tips on your next visit.' : 'Every page shows its tips again on your next visit.' }}
                        </span>
                    </span>
                </button>
            </div>

            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                Tip: click the
                <span class="mx-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full border border-gray-300 text-xs font-semibold dark:border-gray-600">?</span>
                button at the top of a page to see its tips any time.
            </p>

            <!-- Features -->
            <h3 class="mt-8 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Features</h3>
            <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="topic in HELP_TOPICS"
                    :key="topic.title"
                    :href="route(topic.route)"
                    class="group flex flex-col rounded-xl border border-gray-200 bg-white p-5 transition-all hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-primary-700"
                >
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/40 dark:text-primary-300">
                            <Icon :name="topic.icon" class="h-5 w-5" />
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ topic.title }}</span>
                    </div>
                    <p class="mt-3 flex-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ topic.text }}</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-primary-600 group-hover:text-primary-700 dark:text-primary-400">
                        Open {{ topic.title }}
                        <Icon name="chevron-right" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                    </span>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
