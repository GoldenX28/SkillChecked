<script setup>
import { ref } from 'vue';
import GameLayout from '@/Pages/Games/GameLayout.vue';
import TypeSpeedGameCore from '@/Components/TypeSpeedGameCore.vue';

let startFlag = ref(false);

const startGame = () => {
    startFlag.value = true;
};

</script>

<template>
    <GameLayout gameName="TypeSpeed">
        <template #gamewindow>
            <div class="w-full h-full flex flex-col items-center justify-center">
                <div v-if="!startFlag" class="menu-window">
                    <h1 class="text-3xl font-bold">TypeSpeed Game</h1>
                    <div class="game-instructions mt-5">
                        <h2 class="text-2xl font-bold">Instructions</h2>
                        <p>Type the words as fast as you can!</p>
                    </div>
                    <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded" @click="startGame">
                        Start Game
                    </button>
                </div>

                <TypeSpeedGameCore 
                    v-else
                    @restartGame="() => {
                        startFlag = false;
                    }" 
                />
            </div> 
        </template>

        <template #leaderboard>
            <div class="leader-board">
                <h2 class="text-2xl font-bold">Leader Board</h2>
                <ul>
                    <li>User1: 100 WPM</li>
                    <li>User2: 95 WPM</li>
                    <li>User3: 90 WPM</li>
                </ul>
            </div>
        </template>
    </GameLayout>
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