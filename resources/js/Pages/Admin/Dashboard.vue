<template>
    <AdminLayout>
        <template #default>
            <div class="p-6 bg-white rounded shadow">
                <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
                <p class="text-sm text-gray-600">Overview and quick actions for administrators.</p>

                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded">
                        <h3 class="font-semibold">Users</h3>
                        <p class="text-sm text-gray-500">Manage user accounts and roles.</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <h3 class="font-semibold">Memory Results</h3>
                        <p class="text-sm text-gray-500">View and moderate memory game results.</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <h3 class="font-semibold">Settings</h3>
                        <p class="text-sm text-gray-500">Site configuration and toggles.</p>
                    </div>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
</script>
