<script setup>
import GameLayout from '@/Pages/Games/GameLayout.vue';
</script>

<template>
    <GameLayout gameName="TypeSpeed">
        <template #gamewindow>
            <div class="game-instructions">
                <h2 class="text-2xl font-bold">Instructions</h2>
                <p>Type the words as fast as you can!</p>
            </div>
        </template>

        <template #leaderboard>
            <div class="leader-board">
                <h2 class="text-2xl font-bold">Leader Board</h2>
                <ul>
                    <li>User1: 100 WPM</li>
                    <li>User2: 95 WPM</li>
                    <li>User3: 90 WPM</li>
                </ul>
            </div>
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