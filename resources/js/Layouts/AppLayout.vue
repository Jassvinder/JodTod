<script setup>
import Header from '@/Components/Shared/Header.vue';
import Sidebar from '@/Components/Shared/Sidebar.vue';
import BottomNav from '@/Components/Shared/BottomNav.vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const flash = computed(() => usePage().props.flash || {});

// Load collapsed state from localStorage
onMounted(() => {
    sidebarCollapsed.value = localStorage.getItem('sidebar_collapsed') === 'true';
    window.addEventListener('online', onOnline);
    window.addEventListener('offline', onOffline);
});

function toggleCollapse() {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('sidebar_collapsed', sidebarCollapsed.value);
}

// Offline detection
const isOffline = ref(typeof navigator !== 'undefined' ? !navigator.onLine : false);
const onOnline = () => { isOffline.value = false; };
const onOffline = () => { isOffline.value = true; };

onUnmounted(() => {
    window.removeEventListener('online', onOnline);
    window.removeEventListener('offline', onOffline);
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Offline Indicator -->
        <div
            v-if="isOffline"
            class="fixed top-0 left-0 right-0 z-[60] bg-amber-500 text-white text-center text-sm py-1.5 font-medium"
        >
            You're offline. Some features may not be available.
        </div>

        <!-- Sidebar (Desktop) -->
        <Sidebar :open="sidebarOpen" :collapsed="sidebarCollapsed" @close="sidebarOpen = false" @toggle-collapse="toggleCollapse" />

        <!-- Main Content -->
        <div class="transition-all duration-300" :class="[sidebarCollapsed ? 'lg:pl-[68px]' : 'lg:pl-64', { 'pt-8': isOffline }]">
            <!-- Header -->
            <Header @toggle-sidebar="sidebarOpen = !sidebarOpen" />

            <!-- Flash Messages -->
            <div v-if="flash.success" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
                <div class="px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                    {{ flash.success }}
                </div>
            </div>
            <div v-if="flash.error" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
                <div class="px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
                    {{ flash.error }}
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8 pb-20 lg:pb-8">
                <slot />
            </main>
        </div>

        <!-- Bottom Navigation (Mobile) -->
        <BottomNav class="lg:hidden" />
    </div>
</template>
