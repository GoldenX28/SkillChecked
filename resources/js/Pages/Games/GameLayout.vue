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

        <div class="flex flex-1 pt-2 px-2">
            <!-- Left ad -->
            <div class="hidden md:flex w-1/6 flex-shrink-0 items-center justify-center">
                <div class="bg-gray-200 h-full w-full rounded-lg flex items-center justify-center text-gray-500">Ad</div>
            </div>

            <!-- Game content center with vertical layout -->
            <div class="flex-1 flex flex-col px-4 items-center justify-between h-full-screen">
                <!-- Header always at top -->
                <div class="game-header w-full bg-white shadow-md py-6 flex justify-center items-center">
                    <h1 class="text-3xl font-bold text-gray-800">{{ gameName }}</h1>
                </div>

                <!-- Game window fills middle -->
                <div class="game-content w-full mx-auto flex-1 flex items-center justify-center bg-white rounded-lg shadow p-6 mt-4">
                    <slot name="gamewindow"/>
                </div>

                <!-- Leaderboard at bottom -->
                <div class="leader-board w-full mx-auto bg-white rounded-lg shadow p-0 mt-4">
                    <slot name="leaderboard" />
                </div>
            </div>

            <!-- Right ad -->
            <div class="hidden md:flex w-1/6 flex-shrink-0 items-center justify-center">
                <div class="bg-gray-200 h-full w-full rounded-lg flex items-center justify-center text-gray-500">Ad</div>
            </div>
        </div>
    </div>
</template>
