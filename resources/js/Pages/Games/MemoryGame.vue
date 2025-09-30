<template>
    <GameLayout gameName="Memory">
        <template #gamewindow>
            <div class="memory-game">
                <div class="game-board">
                    <div
                        v-for="(card, index) in cards"
                        :key="index"
                        class="card"
                        :class="{ flipped: card.flipped || card.matched }"
                        @click="flipCard(index)"
                    >
                        <span v-if="card.flipped || card.matched">{{ card.value }}</span>
                        <span v-else>?</span>
                    </div>
                </div>
                <div class="game-info">
                    <p>Moves: {{ moves }}</p>
                    <button @click="resetGame">Restart</button>
                </div>
            </div>
        </template>

        <template #leaderboard>
            <div class="leader-board">
                <h2 class="text-2xl font-bold">Leader Board</h2>
                <ul>
                    <li>User1: 10 moves</li>
                    <li>User2: 12 moves</li>
                    <li>User3: 15 moves</li>
                </ul>
            </div>
        </template>
    </GameLayout>
</template>

<script setup>
import GameLayout from '@/Pages/Games/GameLayout.vue';
</script>

<script>
import { h } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: (h, page) => {
        const Layout = page.props.auth.user ? AuthenticatedLayout : GuestLayout;
        return h(Layout, null, () => page);
    },
    data() {
        return {
            cards: [],
            flippedIndices: [],
            moves: 0,
            values: ["A", "B", "C", "D", "E", "F"],
        };
    },
    created() {
        this.resetGame();
    },
    methods: {
        resetGame() {
            const doubled = [...this.values, ...this.values];
            const shuffled = doubled
                .map((v) => ({ value: v, sort: Math.random() }))
                .sort((a, b) => a.sort - b.sort)
                .map((obj) => ({
                    value: obj.value,
                    flipped: false,
                    matched: false,
                }));
            this.cards = shuffled;
            this.flippedIndices = [];
            this.moves = 0;
        },
        flipCard(index) {
            if (
                this.cards[index].flipped ||
                this.cards[index].matched ||
                this.flippedIndices.length === 2
            )
                return;

            this.cards[index].flipped = true;
            this.flippedIndices.push(index);

            if (this.flippedIndices.length === 2) {
                this.moves++;
                const [i1, i2] = this.flippedIndices;
                if (this.cards[i1].value === this.cards[i2].value) {
                    this.cards[i1].matched = true;
                    this.cards[i2].matched = true;
                    this.flippedIndices = [];
                } else {
                    setTimeout(() => {
                        this.cards[i1].flipped = false;
                        this.cards[i2].flipped = false;
                        this.flippedIndices = [];
                    }, 1000);
                }
            }
        },
    },
};
</script>

<style scoped>
.memory-game {
    max-width: 400px;
    margin: auto;
    text-align: center;
}
.game-board {
    display: grid;
    grid-template-columns: repeat(4, 60px);
    gap: 10px;
    justify-content: center;
    margin-bottom: 20px;
}
.card {
    width: 60px;
    height: 80px;
    background: #eee;
    border: 1px solid #ccc;
    font-size: 2em;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    user-select: none;
    transition: background 0.2s;
}
.card.flipped,
.card.matched {
    background: #b3e5fc;
}
.game-info {
    margin-top: 10px;
}
</style>