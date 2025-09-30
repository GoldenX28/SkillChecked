<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    gameName: {
        type: String,
        required: true,
    }
});

function scrollToSection(sectionId) {
    document.getElementById(sectionId)?.scrollIntoView({
        behavior: 'smooth'
    });
}
</script>

<template>
    <div class="relative min-h-screen bg-gray-50 flex flex-col">
        <Head :title="gameName" />

        

        <!-- Main content with side ads -->
        <div class="flex flex-1 pt-24 pb-8 px-2">
            <!-- Left ad -->
            <div class="hidden md:flex w-32 flex-shrink-0 items-center justify-center">
                <div class="bg-gray-200 h-full w-28 rounded-lg flex items-center justify-center text-gray-500">Ad</div>
            </div>

            <!-- Game content center -->
            <div class="flex-1 flex flex-col items-center justify-center">
                <!-- Top header -->
                <div class="game-header w-full bg-white shadow-md py-6 flex justify-center items-center">
                    <h1 class="text-3xl font-bold text-gray-800">{{ gameName }}</h1>
                </div>

                <div class="game-content w-full max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
                    <slot name="gamewindow"/>
                </div>

                <div class="leader-board w-full max-w-2xl mx-auto mt-8 bg-white rounded-lg shadow p-6">
                    <slot name="leaderboard" />
                </div>
            </div>

            <!-- Right ad -->
            <div class="hidden md:flex w-32 flex-shrink-0 items-center justify-center">
                <div class="bg-gray-200 h-full w-28 rounded-lg flex items-center justify-center text-gray-500">Ad</div>
            </div>
        </div>
    </div>
</template>
