<script setup>
import LeaderBoard from './LeaderBoard.vue';

const columns = ['hits', 'accuracy', 'clicks', 'errors'];

defineProps({
    users: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <LeaderBoard 
        :users="users" 
        :columns="columns" 
    />
</template>