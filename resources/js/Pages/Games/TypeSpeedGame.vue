<script setup>
import { ref, onMounted } from 'vue';
import GameLayout from '@/Pages/Games/GameLayout.vue';
import TypeSpeedGameCore from '@/Components/TypeSpeedGameCore.vue';
import TypeSpeedLeaderBoard from '@/Components/LeaderBoards/TypeSpeedLeaderBoard.vue';

const startFlag = ref(false);
const leaderboardLoading = ref(true);
const users = ref([]);

const startGame = () => {
    startFlag.value = true;
};


const getUsers = async () => {
    const response = await fetch('/api/typespeed/leaderboard/?count=10');
    console.log(response)
    const data = await response.json();
    return data;
};
getUsers()

onMounted(async () => {
    users.value = await getUsers();
    leaderboardLoading.value = false;
});
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
            <div v-if="leaderboardLoading" class="w-full h-full flex flex-col items-center justify-center">
                <div class="flex flex-col items-center justify-center h-full">
                    <svg class="animate-spin h-12 w-12 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span class="text-xl font-semibold text-blue-600">leaderboardLoading text...</span>
                </div>
            </div>

            <TypeSpeedLeaderBoard 
                v-else 
                :users="users" 
            />
        </template>
    </GameLayout>
</template>

<style>
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}
.animate-blink {
    animation: blink 1s step-end infinite;
}
</style>

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