<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { computed } from 'vue';

defineEmits(['toggle-sidebar']);

const user = computed(() => usePage().props.auth?.user);
</script>

<template>
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <!-- Left: Menu button (mobile) + Title -->
            <div class="flex items-center gap-3">
                <button
                    @click="$emit('toggle-sidebar')"
                    class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <Link :href="route('dashboard')" class="text-xl font-bold text-primary-600">JodTod</Link>
            </div>

            <!-- Right: Notifications + Profile -->
            <div class="flex items-center gap-3">
                <!-- Notification Bell -->
                <button class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

                <!-- Profile Dropdown -->
                <Dropdown v-if="user" align="right" width="48">
                    <template #trigger>
                        <button class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-primary-500 text-white flex items-center justify-center text-sm font-medium">
                                {{ user.name?.charAt(0)?.toUpperCase() }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700">{{ user.name }}</span>
                            <svg class="hidden sm:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Logout</DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </div>
    </header>
</template>
