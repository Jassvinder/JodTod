<script setup>
import Header from '@/Components/Shared/Header.vue';
import Sidebar from '@/Components/Shared/Sidebar.vue';
import BottomNav from '@/Components/Shared/BottomNav.vue';
import { ref } from 'vue';

const sidebarOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar (Desktop) -->
        <Sidebar :open="sidebarOpen" @close="sidebarOpen = false" />

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Header -->
            <Header @toggle-sidebar="sidebarOpen = !sidebarOpen" />

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8 pb-20 lg:pb-8">
                <slot />
            </main>
        </div>

        <!-- Bottom Navigation (Mobile) -->
        <BottomNav class="lg:hidden" />
    </div>
</template>
