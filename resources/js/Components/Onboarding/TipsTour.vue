<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { PAGE_TIPS, finishTour, onboarding } from '@/Utils/onboarding.js';

// Spotlight tour: dims the page, highlights the element with the tip's data-tip attribute,
// and shows a bubble next to it with "1 of N", Skip, Back and Next / Got it.
const PADDING = 6;
const GAP = 12;
const MARGIN = 16;

const steps = ref([]);
const index = ref(0);
const rect = ref(null);
const bubble = ref(null);
const bubbleSize = ref({ width: 320, height: 160 });
const viewport = ref({ width: window.innerWidth, height: window.innerHeight });

const step = computed(() => steps.value[index.value] || null);
const isLast = computed(() => index.value === steps.value.length - 1);

function findElement(anchor) {
    const el = document.querySelector(`[data-tip="${anchor}"]`);
    if (!el || el.getClientRects().length === 0) return null;
    const r = el.getBoundingClientRect();
    // Skip zero-size elements and ones moved off-screen sideways (e.g. the closed mobile sidebar).
    if (r.width === 0 || r.height === 0 || r.right <= 0 || r.left >= window.innerWidth) return null;
    return el;
}

function measure() {
    viewport.value = { width: window.innerWidth, height: window.innerHeight };
    const el = step.value && findElement(step.value.anchor);
    if (!el) {
        // The target disappeared (page changed or layout switched); end the tour instead of leaving it stuck.
        if (steps.value.length > 0) close();
        return;
    }
    const r = el.getBoundingClientRect();
    rect.value = { top: r.top, left: r.left, width: r.width, height: r.height };
    if (bubble.value) {
        bubbleSize.value = { width: bubble.value.offsetWidth, height: bubble.value.offsetHeight };
    }
}

let frame = null;
function scheduleMeasure() {
    if (frame) return;
    frame = requestAnimationFrame(() => {
        frame = null;
        measure();
    });
}

async function showStep(i) {
    index.value = i;
    const el = step.value && findElement(step.value.anchor);
    if (!el) return;
    const r = el.getBoundingClientRect();
    const fullyVisible = r.top >= 72 && r.bottom <= window.innerHeight - 24;
    if (!fullyVisible) {
        el.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }
    await nextTick();
    measure();
    // Measure again once a smooth scroll has settled.
    setTimeout(measure, 350);
}

function start(page) {
    // Wait a moment so the page has rendered its anchors.
    setTimeout(() => {
        if (onboarding.tourPage !== page) return;
        steps.value = (PAGE_TIPS[page] || []).filter((tip) => findElement(tip.anchor));
        if (steps.value.length === 0) {
            // Nothing to point at in this layout; keep the tips unseen for a later visit.
            onboarding.tourPage = null;
            return;
        }
        showStep(0);
    }, 250);
}

function close() {
    steps.value = [];
    rect.value = null;
    finishTour();
}

function next() {
    if (isLast.value) close();
    else showStep(index.value + 1);
}

function back() {
    if (index.value > 0) showStep(index.value - 1);
}

watch(() => onboarding.tourPage, (page) => {
    if (page) start(page);
    else {
        steps.value = [];
        rect.value = null;
    }
});

// Keep the bubble size current after its text changes.
watch([index, () => steps.value.length], async () => {
    await nextTick();
    measure();
});

const spotlightStyle = computed(() => {
    if (!rect.value) return {};
    return {
        top: `${rect.value.top - PADDING}px`,
        left: `${rect.value.left - PADDING}px`,
        width: `${rect.value.width + PADDING * 2}px`,
        height: `${rect.value.height + PADDING * 2}px`,
    };
});

const placement = computed(() => {
    if (!rect.value) return null;
    const { width: vw, height: vh } = viewport.value;
    const width = Math.min(340, vw - MARGIN * 2);
    const height = bubbleSize.value.height;
    const targetBottom = rect.value.top + rect.value.height + PADDING;
    const targetTop = rect.value.top - PADDING;

    const spaceBelow = vh - targetBottom;
    const below = spaceBelow >= height + GAP + MARGIN || spaceBelow >= targetTop;
    let top = below ? targetBottom + GAP : targetTop - GAP - height;
    top = Math.max(MARGIN, Math.min(top, vh - height - MARGIN));

    const center = rect.value.left + rect.value.width / 2;
    const left = Math.max(MARGIN, Math.min(center - width / 2, vw - width - MARGIN));
    const arrowLeft = Math.max(16, Math.min(center - left, width - 16));

    return { below, top, left, width, arrowLeft };
});

function onKeydown(e) {
    if (!onboarding.tourPage || steps.value.length === 0) return;
    if (e.key === 'Escape') close();
    else if (e.key === 'ArrowRight' || e.key === 'Enter') {
        e.preventDefault();
        next();
    } else if (e.key === 'ArrowLeft') back();
}

onMounted(() => {
    window.addEventListener('resize', scheduleMeasure);
    window.addEventListener('scroll', scheduleMeasure, true);
    document.addEventListener('keydown', onKeydown);
});

onUnmounted(() => {
    window.removeEventListener('resize', scheduleMeasure);
    window.removeEventListener('scroll', scheduleMeasure, true);
    document.removeEventListener('keydown', onKeydown);
    if (frame) cancelAnimationFrame(frame);
});
</script>

<template>
    <Teleport to="body">
        <div v-if="onboarding.tourPage && step && rect" class="fixed inset-0 z-[65]" role="dialog" aria-modal="true" :aria-label="step.title">
            <!-- Blocks clicks on the page while the tour is open -->
            <div class="absolute inset-0" @click.stop></div>

            <!-- Spotlight: the huge shadow dims everything around the target -->
            <div
                class="pointer-events-none absolute rounded-xl ring-2 ring-white/80 transition-all duration-300 ease-out"
                :style="{ ...spotlightStyle, boxShadow: '0 0 0 9999px rgba(17, 24, 39, 0.62)' }"
            ></div>

            <!-- Bubble -->
            <div
                ref="bubble"
                class="absolute rounded-xl bg-white p-4 shadow-2xl transition-[top,left] duration-300 ease-out dark:bg-gray-800 dark:ring-1 dark:ring-gray-700"
                :style="{ top: `${placement.top}px`, left: `${placement.left}px`, width: `${placement.width}px` }"
            >
                <span
                    class="absolute h-3 w-3 rotate-45 bg-white dark:bg-gray-800"
                    :class="placement.below ? '-top-1.5' : '-bottom-1.5'"
                    :style="{ left: `${placement.arrowLeft - 6}px` }"
                ></span>

                <p class="text-xs font-semibold uppercase tracking-wide text-primary-600 dark:text-primary-400">
                    {{ index + 1 }} of {{ steps.length }}
                </p>
                <h3 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ step.title }}</h3>
                <p class="mt-1 text-sm leading-relaxed text-gray-600 dark:text-gray-300">{{ step.text }}</p>

                <div class="mt-4 flex items-center justify-between gap-2">
                    <button
                        v-if="!isLast"
                        type="button"
                        class="rounded-lg px-2 py-1.5 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        @click="close"
                    >
                        Skip
                    </button>
                    <span v-else></span>
                    <div class="flex items-center gap-2">
                        <button
                            v-if="index > 0"
                            type="button"
                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                            @click="back"
                        >
                            Back
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-primary-600 px-4 py-1.5 text-sm font-semibold text-white hover:bg-primary-700"
                            @click="next"
                        >
                            {{ isLast ? 'Got it' : 'Next' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
