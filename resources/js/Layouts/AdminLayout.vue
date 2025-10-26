<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const siteName = ref('Skillcheck');
const showing = ref(false);

// Scroll helper for `Contact` consistency with main layout
const scrollToFooter = () => {
    const el = document.getElementById('contact');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    }
    if (typeof route === 'function') {
        window.location.href = route('welcome') + '#contact';
    } else {
        window.location.hash = '#contact';
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Header matching the main authenticated layout so style is consistent -->
        <header class="w-full bg-white shadow-lg dark:bg-gray-800">
            <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center">
                        <Link
                            :href="route('welcome')"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-300 dark:hover:text-blue-400"
                        >
                            <div class="text-2xl font-bold text-blue-600">{{ siteName }} - Admin</div>
                        </Link>
                    </div>

                    <div class="hidden space-x-8 sm:ml-6 sm:flex sm:items-center">
                        <Link
                            :href="route('welcome') + '#services'"
                            class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-100 dark:hover:text-blue-400"
                        >
                            Games
                        </Link>

                        <Link
                            :href="route('leaderboard')"
                            class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-100 dark:hover:text-blue-400"
                        >
                            Leaderboard
                        </Link>

                        <button
                            @click.prevent="scrollToFooter"
                            class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-100 dark:hover:text-blue-400"
                        >
                            Contact
                        </button>
                    </div>

                    <div class="flex items-center space-x-4">
                        <Dropdown class="relative">
                            <template #trigger>
                                <button class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <span class="mr-2">{{ $page.props.auth.user?.name || 'Account' }}</span>
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.1 1.02l-4.25 4.65a.75.75 0 01-1.1 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('admin.dashboard')">Dashboard</DropdownLink>
                                <DropdownLink :href="route('leaderboard')">Leaderboard</DropdownLink>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </nav>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-12 gap-6">
                <aside class="col-span-3 bg-white rounded shadow p-4">
                    <nav class="flex flex-col space-y-2">
                        <Link :href="route('admin.dashboard')" class="px-3 py-2 rounded hover:bg-gray-100">Overview</Link>
                        <Link :href="route('admin.users')" class="px-3 py-2 rounded hover:bg-gray-100">Users</Link>
                        <Link :href="route('admin.results')" class="px-3 py-2 rounded hover:bg-gray-100">Results</Link>
                        <Link :href="route('admin.settings')" class="px-3 py-2 rounded hover:bg-gray-100">Settings</Link>
                    </nav>
                </aside>

                <main class="col-span-9">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
