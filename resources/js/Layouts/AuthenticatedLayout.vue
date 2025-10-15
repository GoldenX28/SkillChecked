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

console.log("Authentication layout loaded");

// Scroll helper for "Contact" button
const scrollToFooter = () => {
    const el = document.getElementById('contact');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    }
    // If footer not present on current page, navigate to welcome with fragment
    if (typeof route === 'function') {
        window.location.href = route('welcome') + '#contact';
    } else {
        window.location.hash = '#contact';
    }
};
</script>

<template>
    <header class="w-full bg-white shadow-lg dark:bg-gray-800">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between items-center">
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

                    <!-- Contact: call scroll helper when clicked -->
                    <button
                        @click.prevent="scrollToFooter"
                        class="text-gray-900 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors dark:text-gray-100 dark:hover:text-blue-400"
                    >
                        Contact
                    </button>
                </div>

                <!-- Auth / User Dropdown -->
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
                            <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <slot />
    </main>

    <!-- Footer with contact info -->
    <footer id="contact" class="bg-gray-900 text-white mt-8">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold">Contact</h3>
                    <p class="mt-2 text-sm text-gray-300">Have questions or feedback? Get in touch.</p>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-200">Email</h4>
                    <p class="mt-1 text-sm text-gray-400">support@skillcheck.example</p>

                    <h4 class="mt-4 text-sm font-medium text-gray-200">Phone</h4>
                    <p class="mt-1 text-sm text-gray-400">+1 (555) 123-4567</p>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-200">Address</h4>
                    <p class="mt-1 text-sm text-gray-400">123 Dev St, Suite 100<br/>Dev City, DC 12345</p>

                    <div class="mt-4">
                        <button @click="scrollToFooter" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Contact Us
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center text-sm text-gray-500">
                &copy; {{ new Date().getFullYear() }} {{ siteName }}. All rights reserved.
            </div>
        </div>
    </footer>
</template>
