<template>
    <div class="max-w-4xl mx-auto p-4 min-h-screen">
        <h1 class="text-2xl font-bold mb-4">Leaderboards</h1>

        <div class="flex space-x-2 mb-4">
            <button
                :class="tabClass('typespeed')"
                @click="activeTab = 'typespeed'"
                type="button"
            >
                Type Speed
            </button>
            <button
                :class="tabClass('aimtrainer')"
                @click="activeTab = 'aimtrainer'"
                type="button"
            >
                Aim Trainer
            </button>
            <button
                :class="tabClass('memory')"
                @click="activeTab = 'memory'"
                type="button"
            >
                Memory
            </button>
        </div>

        <div class="bg-black rounded border border-gray-800">
            <!-- TypeSpeed -->
            <div v-if="activeTab === 'typespeed'">
                <div v-if="loading.typespeed" class="text-gray-400">Loading Type Speed leaderboard...</div>
                <div v-else-if="error.typespeed" class="text-red-400">Failed to load Type Speed leaderboard</div>
                <TypeSpeedLeaderBoard v-else :users="lists.typespeed" />
            </div>

            <!-- Aim Trainer -->
            <div v-if="activeTab === 'aimtrainer'">
                <div v-if="loading.aimtrainer" class="text-gray-400">Loading Aim Trainer leaderboard...</div>
                <div v-else-if="error.aimtrainer" class="text-red-400">Failed to load Aim Trainer leaderboard</div>
                <AimTrainerLeaderBoard v-else :users="lists.aimtrainer" />
            </div>

            <!-- Memory -->
            <div v-if="activeTab === 'memory'">
                <div v-if="loading.memory" class="text-gray-400">Loading Memory leaderboard...</div>
                <div v-else-if="error.memory" class="text-red-400">Failed to load Memory leaderboard</div>
                <MemoryLeaderBoard v-else :users="lists.memory" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import TypeSpeedLeaderBoard from '@/Components/LeaderBoards/TypeSpeedLeaderBoard.vue';
import AimTrainerLeaderBoard from '@/Components/LeaderBoards/AimTrainerLeaderBoard.vue';
import MemoryLeaderBoard from '@/Components/LeaderBoards/MemoryLeaderBoard.vue';

const activeTab = ref('typespeed');

const lists = ref({
    typespeed: [],
    aimtrainer: [],
    memory: []
});

const loading = ref({
    typespeed: false,
    aimtrainer: false,
    memory: false
});

const error = ref({
    typespeed: false,
    aimtrainer: false,
    memory: false
});

function tabClass(name) {
    const base = 'px-4 py-2 rounded font-medium';
    return [
        base,
        activeTab.value === name
            ? 'bg-gray-900 text-white border border-gray-700'
            : 'bg-gray-800 text-gray-300 hover:bg-gray-700'
    ].join(' ');
}

async function fetchList(key, url) {
    loading.value[key] = true;
    error.value[key] = false;
    try {
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network error');
        const json = await res.json();
        lists.value[key] = Array.isArray(json) ? json : [];
    } catch (e) {
        console.error(`Failed to load ${key} leaderboard`, e);
        error.value[key] = true;
        lists.value[key] = [];
    } finally {
        loading.value[key] = false;
    }
}

onMounted(async () => {
    await Promise.all([
        fetchList('typespeed', '/api/typespeed/leaderboard'),
        fetchList('aimtrainer', '/api/aimtrainer/leaderboard'),
        fetchList('memory', '/api/memory/leaderboard')
    ]);
});
</script>

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