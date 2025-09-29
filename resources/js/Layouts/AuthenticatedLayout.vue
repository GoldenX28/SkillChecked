<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const siteName = ref('Skillcheck');
const showingNavigationDropdown = ref(false);
</script>

<template>
    <header class="fixed top-0 z-50 w-full bg-white shadow-lg dark:bg-gray-800">
            <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex items-center">
                        <Link
                            :href="route('welcome')"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-300 dark:hover:text-blue-400"
                        >
                            <div class="text-2xl font-bold text-blue-600">{{ siteName }}</div>
                        </Link>
                    </div>
                    
                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:ml-6 sm:flex sm:items-center">
                        <a href="#home" @click.prevent="scrollToSection('home')" 
                           class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-100 dark:hover:text-blue-400">
                            Home
                        </a>
                        <a href="#services" @click.prevent="scrollToSection('services')" 
                           class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-100 dark:hover:text-blue-400">
                            Services
                        </a>
                        <a href="#contact" @click.prevent="scrollToSection('contact')" 
                           class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-100 dark:hover:text-blue-400">
                            Contact
                        </a>
                    </div>

                    <!-- Auth Links -->
                    <nav class="flex items-center space-x-4">
                        <div
                        >
                            <!-- Responsive Settings Options -->
                            <div
                                class="border-t border-gray-200 pb-1 pt-4"
                            >
                                    <!-- Settings Dropdown -->
                                    <div class="relative ms-3">
                                        <Dropdown align="right" width="48">
                                            <template #trigger>
                                                <span class="inline-flex rounded-md">
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                                    >
                                                        {{ $page.props.auth.user.name }}

                                                        <svg
                                                            class="-me-0.5 ms-2 h-4 w-4"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 20 20"
                                                            fill="currentColor"
                                                        >
                                                            <path
                                                                fill-rule="evenodd"
                                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                                clip-rule="evenodd"
                                                            />
                                                        </svg>
                                                    </button>
                                                </span>
                                            </template>

                                            <template #content>
                                                <DropdownLink
                                                    :href="route('profile.edit')"
                                                >
                                                    Profile
                                                </DropdownLink>
                                                <DropdownLink
                                                    :href="route('logout')"
                                                    method="post"
                                                    as="button"
                                                >
                                                    Log Out
                                                </DropdownLink>
                                            </template>
                                        </Dropdown>
                                    </div>
                                </div>    

                    </div>
                    </nav>

                    
                </div>
            </nav>
        </header>

<!-- Page Heading -->   
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

        <!-- Page Content -->
        <div>
            <slot />
        </div>
</template>
