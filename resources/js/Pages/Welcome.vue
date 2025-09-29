<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// Site data
const siteName = ref('Skillcheck');
const heroTitle = ref('Welcome to Our Amazing Platform');
const heroSubtitle = ref('Build something incredible with Vue.js and Laravel');
const ctaButton = ref('Get Started');

const features = ref([
    {
        id: 1,
        icon: '🎯',
        title: 'Aim trainer',
        description: 'Train your aim with high action gameplay, that requires precision and timing.'
    },
    {
        id: 2,
        icon: '⌨️',
        title: 'Speedwriter',
        description: 'Test your writing skills on the keyboard with fast-paced typing challenges. Using single words or whole sentances'
    },
    {
        id: 3,
        icon: '🧠',
        title: 'Brain Teaser',
        description: 'Enhance your memory skills with engaging challenges and fun gameplay, through numbers, words or sequences.'
    }
]);

const currentYear = new Date().getFullYear();

function scrollToSection(sectionId) {
    document.getElementById(sectionId)?.scrollIntoView({
        behavior: 'smooth'
    });
}
</script>

<template>
    <Head title="Welcome" />
    
    <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
        
        <!-- Hero Section -->
        <section id="home" class="pt-16 bg-gradient-to-br from-blue-600 to-purple-700 text-white">
            <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl animate-fade-in-up">
                        {{ heroTitle }}
                    </h1>
                    <p class="mx-auto mt-6 max-w-2xl text-xl text-blue-100 animate-fade-in-up animation-delay-200">
                        {{ heroSubtitle }}
                    </p>
                    <div class="mt-10 animate-fade-in-up animation-delay-400">
                        <a href="#services" @click.prevent="scrollToSection('services')" 
                           class="inline-block rounded-full bg-white px-8 py-3 text-lg font-medium text-blue-600 shadow-lg hover:bg-gray-50 hover:-translate-y-1 transition-all duration-300">
                            {{ ctaButton }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="services" class="py-20 bg-gray-50 dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                        Our Features
                    </h2>
                </div>

                <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div 
                        v-for="feature in features" 
                        :key="feature.id"
                        class="group rounded-lg bg-white p-8 shadow-md hover:shadow-xl hover:-translate-y-2 transition-all duration-300 dark:bg-gray-700"
                    >
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-2xl dark:bg-blue-900">
                            {{ feature.icon }}
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ feature.title }}
                        </h3>
                        <p class="mt-4 text-gray-600 dark:text-gray-300">
                            {{ feature.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="contact" class="bg-gray-900 text-white">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p>&copy; {{ currentYear }} {{ siteName }}. All rights reserved.</p>
                    <p class="mt-2 text-sm text-gray-400">
                        Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
import { h } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';


export default {
    layout: (h, page) =>{
        const Layout = page.props.auth.user ? AuthenticatedLayout : GuestLayout;
        return h(Layout, null, () => page);
    },
}

</script>

<style scoped>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 1s ease-out;
}

.animation-delay-200 {
    animation-delay: 0.2s;
    animation-fill-mode: both;
}

.animation-delay-400 {
    animation-delay: 0.4s;
    animation-fill-mode: both;
}
</style>