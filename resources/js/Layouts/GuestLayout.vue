<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
const siteName = ref('Skillcheck');
const showingNavigationDropdown = ref(false);

console.log("GuestLayout loaded");
</script>

<template>
    <!-- Header -->
        <header class="w-full bg-white shadow-lg dark:bg-gray-800">
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
                        <Link
                            :href="route('login')"
                            class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-300 dark:hover:text-blue-400"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register')"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors"
                        >
                            Register
                        </Link>
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
