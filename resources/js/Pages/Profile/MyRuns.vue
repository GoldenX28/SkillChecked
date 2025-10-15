<template>
    <AuthenticatedLayout>
        <template #default>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">My runs</h2>
                    <div>
                        <label class="mr-2 font-semibold">Difficulty:</label>
                        <select v-model="difficulty" @change="fetchRuns" class="px-2 py-1 rounded border">
                            <option value="easy">Easy</option>
                            <option value="normal">Normal</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">Moves</th>
                                <th class="px-4 py-2">Time (s)</th>
                                <th class="px-4 py-2">When</th>
                                <th class="px-4 py-2">Best</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(run, idx) in runs" :key="run.id" class="border-t">
                                <td class="px-4 py-2">{{ idx + 1 }}</td>
                                <td class="px-4 py-2">{{ run.moves }}</td>
                                <td class="px-4 py-2">{{ run.time_seconds }}</td>
                                <td class="px-4 py-2">{{ run.created_at_human || run.created_at }}</td>
                                <td class="px-4 py-2">
                                    <span v-if="run.is_best_moves && run.is_best_time" class="px-2 py-1 bg-green-600 text-white rounded">Best Moves & Time</span>
                                    <span v-else-if="run.is_best_moves" class="px-2 py-1 bg-blue-600 text-white rounded">Best Moves</span>
                                    <span v-else-if="run.is_best_time" class="px-2 py-1 bg-yellow-600 text-white rounded">Best Time</span>
                                    <span v-else class="text-gray-500">&ndash;</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { onMounted, ref } from 'vue';

    const runs = ref([]);
    const difficulty = ref('normal');

    function fetchRuns() {
        return (async () => {
            try {
                const res = await window.axios.get(`/api/memory/personal?count=50&difficulty=${difficulty.value}`);
                const data = res.data || [];
                if (!data.length) {
                    runs.value = [];
                    return;
                }
                const bestMoves = Math.min(...data.map(r => Number(r.moves)));
                const bestTime = Math.min(...data.map(r => Number(r.time_seconds)));
                runs.value = data.map(r => ({
                    ...r,
                    is_best_moves: Number(r.moves) === bestMoves,
                    is_best_time: Number(r.time_seconds) === bestTime,
                    created_at_human: formatTimestamp(r.created_at)
                }));
            } catch (e) {
                console.error('Failed to fetch personal runs', e);
                runs.value = [];
            }
        })();
    }

function formatTimestamp(ts) {
    try {
        const d = new Date(ts);
        return d.toLocaleString();
    } catch (e) {
        return ts;
    }
}

    onMounted(() => {
        fetchRuns();
    });

    // expose difficulty so template can bind
    export { difficulty };
</script>
