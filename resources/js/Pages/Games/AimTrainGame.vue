<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import GameLayout from '@/Pages/Games/GameLayout.vue';
import AimTrainerLeaderBoard from '@/Components/LeaderBoards/AimTrainerLeaderBoard.vue';
import axios from 'axios';

const page = usePage();

// Game state
const gameDuration = 30; // seconds
const targetRadius = 40; // px

const gameStarted = ref(false);
const timeLeft = ref(gameDuration);
const targetsHit = ref(0);
const totalClicks = ref(0);
const showResults = ref(false);
const target = ref({ x: 200, y: 200 });

let timerInterval = null;

// Leaderboard state
const leaderboard = ref([]);
const leaderboardSort = ref('hits');

// Utility functions
function randomPosition() {
    // Container size: 500x400px
    const x = Math.random() * (500 - targetRadius * 2) + targetRadius;
    const y = Math.random() * (400 - targetRadius * 2) + targetRadius;
    return { x, y };
}

function startGame() {
    gameStarted.value = true;
    showResults.value = false;
    timeLeft.value = gameDuration;
    targetsHit.value = 0;
    totalClicks.value = 0;
    target.value = randomPosition();

    timerInterval = setInterval(() => {
        timeLeft.value--;
        if (timeLeft.value <= 0) {
            endGame();
        }
    }, 1000);
}

function endGame() {
    gameStarted.value = false;
    showResults.value = true;
    clearInterval(timerInterval);
}

function handleClick(e) {
    if (!gameStarted.value) return;
    totalClicks.value++;
    // Get click position relative to container
    const rect = e.currentTarget.getBoundingClientRect();
    const clickX = e.clientX - rect.left;
    const clickY = e.clientY - rect.top;

    // Check if click is inside the target
    const dx = clickX - target.value.x;
    const dy = clickY - target.value.y;
    if (dx * dx + dy * dy <= targetRadius * targetRadius) {
        targetsHit.value++;
        target.value = randomPosition();
    }
}

const accuracy = computed(() => {
    if (totalClicks.value === 0) return 0;
    return ((targetsHit.value / totalClicks.value) * 100).toFixed(2);
});

// Leaderboard functions
async function saveResult() {
    await axios.post('/api/aimtrainer/result', {
        hits: targetsHit.value,
        clicks: totalClicks.value,
        accuracy: accuracy.value,
    });
    fetchLeaderboard();
}

async function fetchLeaderboard() {
    const res = await axios.get('/api/aimtrainer/leaderboard', {
        params: { sort: leaderboardSort.value }
    });
    leaderboard.value = res.data.map(user => ({
        user: user.user,
        hits: user.hits,
        accuracy: user.accuracy,
        clicks: user.clicks,
        errors: user.clicks - user.hits,
    }));

    console.log(leaderboard.value);
}

// Watchers
watch(showResults, (val) => {
    if (val && page.props.auth.user) saveResult();
});

watch(leaderboardSort, () => {
    fetchLeaderboard();
});

onMounted(() => {
    fetchLeaderboard();
});

onUnmounted(() => {
    clearInterval(timerInterval);
});
</script>

<template>
    <GameLayout gameName="Aim Trainer">
        <template #gamewindow>
            <div class="flex flex-col items-center justify-center h-full p-4">
                <div class="game-instructions mb-4">
                    <h2 class="text-2xl font-bold">Aim Trainer</h2>
                    <p>Click on the circles as fast and accurately as you can for 60 seconds. Missing counts as a miss!</p>
                </div>
                <div class="mb-4">
                    <button
                        v-if="!gameStarted && !showResults"
                        @click="startGame"
                        class="px-4 py-2 bg-blue-600 text-white rounded"
                    >
                        Start Game
                    </button>
                    <div v-if="gameStarted" class="mb-2 text-lg font-semibold">
                        Time Left: {{ timeLeft }}s
                    </div>
                </div>
                <div
                    v-if="gameStarted"
                    class="aim-trainer-container"
                    @click="handleClick"
                    style="width: 500px; height: 400px; position: relative; background: #f3f3f3; border-radius: 12px; overflow: hidden; cursor: crosshair;"
                >
                    <div
                        class="target-circle"
                        :style="{
                            position: 'absolute',
                            left: target.x - targetRadius + 'px',
                            top: target.y - targetRadius + 'px',
                            width: targetRadius * 2 + 'px',
                            height: targetRadius * 2 + 'px',
                            borderRadius: '50%',
                            background: '#e53e3e',
                            boxShadow: '0 0 10px #e53e3e88',
                            border: '2px solid #fff',
                            cursor: 'pointer',
                        }"
                    ></div>
                </div>
                <div v-if="showResults" class="mt-6 text-center">
                    <h3 class="text-xl font-bold mb-2">Results</h3>
                    <p>Targets Hit: <span class="font-semibold">{{ targetsHit }}</span></p>
                    <p>Total Clicks: <span class="font-semibold">{{ totalClicks }}</span></p>
                    <p>Accuracy: <span class="font-semibold">{{ accuracy }}</span></p>
                    <button @click="startGame" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">Play Again</button>
                </div>
            </div>
            
        </template>

        <template #leaderboard>
            <AimTrainerLeaderBoard
                v-if="leaderboard.length"
                :users="leaderboard"
            />
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