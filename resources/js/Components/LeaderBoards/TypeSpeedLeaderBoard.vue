<script setup>
import LeaderBoard from './LeaderBoard.vue';

const columns = ['wpm', 'accuracy', 'errors', 'typed_chars', 'correct_chars'];

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