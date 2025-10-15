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
        description: 'Train your aim with high action gameplay, that requires precision and timing.',
        link: '/games/aimtraingame'
    },
    {
        id: 2,
        icon: '⌨️',
        title: 'Speedwriter',
        description: 'Test your writing skills on the keyboard with fast-paced typing challenges. Using single words or whole sentances',
        link: '/games/typespeed'
    },
    {
        id: 3,
        icon: '🧠',
        title: 'Brain Teaser',
        description: 'Enhance your memory skills with engaging challenges and fun gameplay, through numbers, words or sequences.',
        link: '/games/memorygame'
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
                        <Link 
                            v-if="feature.link"
                            :href="feature.link"
                            class="block text-xl font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                        >
                            {{ feature.title }}
                        </Link>
                        <h3 v-else class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ feature.title }}
                        </h3>
                        <p class="mt-4 text-gray-600 dark:text-gray-300">
                            {{ feature.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

       
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