<script setup>
import LeaderBoard from './LeaderBoard.vue';

const columns = ['moves', 'time_seconds'];

defineProps({
    users: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <LeaderBoard :users="users" :columns="columns" />
</template>
