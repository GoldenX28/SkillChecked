<template>
    <AdminLayout>
        <template #default>
            <div class="p-6 bg-white rounded shadow">
                <h1 class="text-2xl font-bold mb-4">Memory Results</h1>
                <p class="text-sm text-gray-600">Admin view of memory game results.</p>

                <div class="mt-4">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">User</th>
                                <th class="px-4 py-2">Moves</th>
                                <th class="px-4 py-2">Time (s)</th>
                                <th class="px-4 py-2">Difficulty</th>
                                <th class="px-4 py-2">When</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(r, idx) in results" :key="r.id" class="border-t">
                                <td class="px-4 py-2">{{ idx + 1 }}</td>
                                <td class="px-4 py-2">{{ r.user?.name || r.user_id }}</td>
                                <td class="px-4 py-2">{{ r.moves }}</td>
                                <td class="px-4 py-2">{{ r.time_seconds }}</td>
                                <td class="px-4 py-2">{{ r.difficulty }}</td>
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
import { ref, onMounted } from 'vue';

const results = ref([]);

async function fetchResults() {
    try {
        const res = await window.axios.get('/api/admin/memory-results');
        results.value = res.data || [];
    } catch (e) {
        console.error('Failed to fetch memory results', e);
        results.value = [];
    }
}

onMounted(() => {
    fetchResults();
});

async function deleteResult(r) {
    if (!confirm(`Delete result #${r.id}?`)) return;
    try {
        await window.axios.delete(`/api/admin/memory-results/${r.id}`);
        results.value = results.value.filter(x => x.id !== r.id);
    } catch (e) {
        alert('Error deleting result: ' + (e.response?.data?.message || e.message));
    }
}
</script>
