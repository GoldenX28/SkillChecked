<template>
    <AdminLayout>
        <template #default>
            <div class="p-6 bg-white rounded shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Results</h1>
                        <p class="text-sm text-gray-600">View and manage results from all games.</p>
                    </div>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm">Game</label>
                        <select v-model="selectedGame" @change="fetchResults" class="border rounded px-2 py-1">
                            <option value="">All</option>
                            <option value="memory">Memory</option>
                            <option value="typespeed">TypeSpeed</option>
                            <option value="aimtrainer">AimTrainer</option>
                        </select>

                        <label class="text-sm">Sort</label>
                        <select v-model="sortBy" @change="fetchResults" class="border rounded px-2 py-1">
                            <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>

                        <button @click="toggleOrder" class="px-2 py-1 border rounded">{{ order.toUpperCase() }}</button>
                    </div>
                </div>

                <div class="mt-4">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">User</th>
                                <th v-if="selectedGame === '' || selectedGame === 'memory'" class="px-4 py-2">Moves</th>
                                <th v-if="selectedGame === '' || selectedGame === 'memory'" class="px-4 py-2">Time (s)</th>
                                <th v-if="selectedGame === '' || selectedGame === 'typespeed'" class="px-4 py-2">WPM</th>
                                <th v-if="selectedGame === '' || selectedGame === 'typespeed'" class="px-4 py-2">Accuracy</th>
                                <th v-if="selectedGame === '' || selectedGame === 'aimtrainer'" class="px-4 py-2">Hits</th>
                                <th v-if="selectedGame === '' || selectedGame === 'aimtrainer'" class="px-4 py-2">Accuracy</th>
                                <th class="px-4 py-2">Game</th>
                                <th class="px-4 py-2">When</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(r, idx) in results" :key="r.game + '-' + r.id" class="border-t">
                                <td class="px-4 py-2">{{ idx + 1 }}</td>
                                <td class="px-4 py-2">{{ r.user?.name || r.user_id }}</td>
                                <td v-if="r.game === 'memory'" class="px-4 py-2">{{ r.moves }}</td>
                                <td v-if="r.game === 'memory'" class="px-4 py-2">{{ r.time_seconds }}</td>
                                <td v-if="r.game === 'typespeed'" class="px-4 py-2">{{ r.wpm }}</td>
                                <td v-if="r.game === 'typespeed'" class="px-4 py-2">{{ r.accuracy }}</td>
                                <td v-if="r.game === 'aimtrainer'" class="px-4 py-2">{{ r.hits }}</td>
                                <td v-if="r.game === 'aimtrainer'" class="px-4 py-2">{{ r.accuracy }}</td>
                                <td class="px-4 py-2">{{ r.game }}</td>
                                <td class="px-4 py-2">{{ r.created_at }}</td>
                                <td class="px-4 py-2">
                                    <button @click="deleteResult(r)" class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed, onMounted } from 'vue';

const selectedGame = ref('');
const sortBy = ref('created_at');
const order = ref('desc');
const results = ref([]);

const sortOptions = computed(() => {
    // Provide sort options depending on selectedGame
    if (!selectedGame.value) {
        return [
            { value: 'created_at', label: 'Newest' },
        ];
    }
    if (selectedGame.value === 'memory') {
        return [
            { value: 'moves', label: 'Least Moves' },
            { value: 'time_seconds', label: 'Fastest Time' },
            { value: 'created_at', label: 'Newest' },
        ];
    }
    if (selectedGame.value === 'typespeed') {
        return [
            { value: 'wpm', label: 'Highest WPM' },
            { value: 'accuracy', label: 'Highest Accuracy' },
            { value: 'created_at', label: 'Newest' },
        ];
    }
    if (selectedGame.value === 'aimtrainer') {
        return [
            { value: 'hits', label: 'Most Hits' },
            { value: 'accuracy', label: 'Highest Accuracy' },
            { value: 'created_at', label: 'Newest' },
        ];
    }
    return [{ value: 'created_at', label: 'Newest' }];
});

async function fetchResults() {
    try {
        const res = await window.axios.get('/api/admin/results', {
            params: {
                game: selectedGame.value || undefined,
                sort_by: sortBy.value,
                order: order.value,
            }
        });
        results.value = res.data || [];
    } catch (e) {
        console.error('Failed to fetch results', e);
        results.value = [];
    }
}

function toggleOrder() {
    order.value = order.value === 'asc' ? 'desc' : 'asc';
    fetchResults();
}

onMounted(() => {
    fetchResults();
});

async function deleteResult(r) {
    if (!confirm(`Delete result #${r.id} from ${r.game}?`)) return;
    try {
        await window.axios.delete(`/api/admin/results/${r.game}/${r.id}`);
        results.value = results.value.filter(x => !(x.game === r.game && x.id === r.id));
    } catch (e) {
        alert('Error deleting result: ' + (e.response?.data?.message || e.message));
    }
}
</script>
