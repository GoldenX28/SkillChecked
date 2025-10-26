<template>
    <AdminLayout>
        <template #default>
            <div class="p-6 bg-white rounded shadow">
                <h1 class="text-2xl font-bold mb-4">Users</h1>
                <p class="text-sm text-gray-600">User list and management.</p>

                <div class="mt-4">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Admin</th>
                                <th class="px-4 py-2">When</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(u, idx) in users" :key="u.id" class="border-t">
                                <td class="px-4 py-2">{{ idx + 1 }}</td>
                                <td class="px-4 py-2">{{ u.name }}</td>
                                <td class="px-4 py-2">{{ u.email }}</td>
                                <td class="px-4 py-2">{{ u.is_admin ? 'Yes' : 'No' }}</td>
                                <td class="px-4 py-2">{{ u.created_at }}</td>
                                <td class="px-4 py-2">
                                    <button @click="toggleAdmin(u)" class="px-2 py-1 bg-blue-600 text-white rounded mr-2">Toggle Admin</button>
                                    <button @click="deleteUser(u)" class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
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

const users = ref([]);

async function fetchUsers() {
    try {
        const res = await window.axios.get('/api/admin/users');
        users.value = res.data || [];
    } catch (e) {
        console.error('Failed to fetch users', e);
        users.value = [];
    }
}

onMounted(() => {
    fetchUsers();
});

async function toggleAdmin(u) {
    if (!confirm(`Toggle admin for ${u.name}?`)) return;
    try {
        const res = await window.axios.post(`/api/admin/users/${u.id}/toggle`);
        const data = res.data;
        if (data.success) {
            u.is_admin = data.is_admin;
        } else {
            alert(data.message || 'Failed');
        }
    } catch (e) {
        alert('Error: ' + (e.response?.data?.message || e.message));
    }
}

async function deleteUser(u) {
    if (!confirm(`Delete user ${u.name}? This cannot be undone.`)) return;
    try {
        await window.axios.delete(`/api/admin/users/${u.id}`);
        users.value = users.value.filter(x => x.id !== u.id);
    } catch (e) {
        alert('Error deleting user: ' + (e.response?.data?.message || e.message));
    }
}
</script>
