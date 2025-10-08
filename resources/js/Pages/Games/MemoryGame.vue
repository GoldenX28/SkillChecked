<template>
    <GameLayout gameName="Memory">
        <template #gamewindow>
            <div class="memory-game">
                    <!-- Difficulty picker shown before starting -->
                    <div v-if="showDifficultyPicker" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                        <div class="bg-white p-6 rounded shadow-lg w-80 text-center">
                            <h3 class="text-xl font-bold mb-4">Choose difficulty</h3>
                            <div class="flex flex-col gap-3">
                                <button @click="setDifficulty('easy')" class="px-4 py-2 bg-green-600 text-white rounded">Easy (2x6)</button>
                                <button @click="setDifficulty('normal')" class="px-4 py-2 bg-blue-600 text-white rounded">Normal (2x10)</button>
                                <button @click="setDifficulty('hard')" class="px-4 py-2 bg-red-600 text-white rounded">Hard (3x10)</button>
                            </div>
                            <p class="text-sm text-gray-500 mt-3">You can change difficulty anytime before starting a run.</p>
                        </div>
                    </div>
                <div class="game-board" :style="{ gridTemplateColumns: 'repeat(' + boardColumns() + ', 60px)'}">
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
                <div v-if="!showResults" class="game-info">
                    <p>Moves: {{ moves }}</p>
                    <button @click="resetGame">Restart</button>
                    <p v-if="!$page.props.auth.user" class="text-sm text-gray-500 mt-2">Log in to save your result.</p>
                </div>
                <div v-else class="results-screen mt-4 text-center">
                    <h3 class="text-xl font-bold mb-2">Results</h3>
                    <p>Moves: <span class="font-semibold">{{ moves }}</span></p>
                    <p>Time: <span class="font-semibold">{{ elapsedSeconds }}s</span></p>
                    <div class="mt-3 flex items-center justify-center gap-3">
                        <button
                            v-if="$page.props.auth.user"
                            @click="saveResult"
                            :disabled="resultsSaved"
                            class="px-4 py-2 bg-blue-600 text-white rounded"
                        >
                            {{ resultsSaved ? 'Saved' : 'Save Result' }}
                        </button>
                        <button @click="resetGame" class="px-4 py-2 bg-gray-500 text-white rounded">Play Again</button>
                    </div>
                </div>
            </div>
        </template>

        <template #leaderboard>
            <div class="leader-board">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold">Leader Board</h2>
                    <div>
                        <label class="mr-2 font-semibold">Show:</label>
                        <select v-model="leaderboardSort" @change="fetchLeaderboard" class="px-2 py-1 rounded border mr-2">
                            <option value="moves">Least Moves</option>
                            <option value="time">Best Time</option>
                        </select>
                        <label class="mr-2 font-semibold">Difficulty:</label>
                        <select v-model="difficulty" @change="fetchLeaderboard" class="px-2 py-1 rounded border mr-2">
                            <option value="easy">Easy</option>
                            <option value="normal">Normal</option>
                            <option value="hard">Hard</option>
                        </select>
                        <label class="mr-2 font-semibold">Scope:</label>
                        <select v-model="leaderboardScope" @change="fetchLeaderboard" class="px-2 py-1 rounded border">
                            <option value="public">Public</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>
                </div>
                <div v-if="leaderboardScope === 'personal' && !$page.props.auth.user" class="p-4 text-center">
                    <p class="mb-2 font-semibold">Log in to view your personal runs.</p>
                    <div class="flex items-center justify-center gap-3">
                        <a href="/login" class="px-4 py-2 bg-blue-600 text-white rounded">Log in</a>
                        <a href="/register" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Register</a>
                    </div>
                </div>
                <!-- Show the public leader board when scope=public -->
                <div v-if="leaderboardScope === 'public'">
                    <MemoryLeaderBoard :users="leaderboardUsers" />
                </div>

                <!-- For personal scope, keep the same leaderboard component behavior as public (so layout doesn't break).
                     The user's full 'My runs' history is available under Profile -> My Runs. -->
                <div v-else-if="leaderboardScope === 'personal' && $page.props.auth.user">
                    <MemoryLeaderBoard :users="leaderboardUsers" />
                </div>
            </div>
        </template>
    </GameLayout>
</template>

<script setup>
import GameLayout from '@/Pages/Games/GameLayout.vue';
import MemoryLeaderBoard from '@/Components/LeaderBoards/MemoryLeaderBoard.vue';
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
                // difficulty: easy|normal|hard
                difficulty: 'easy',
                showDifficultyPicker: true,
            startTime: null,
            showResults: false,
            elapsedSeconds: null,
            resultsSaved: false,
            leaderboardUsers: [],
            leaderboardSort: 'moves',
            leaderboardScope: 'public',
            // personalBestSummary removed (moved to Profile/MyRuns)
        };
    },
    created() {
        this.resetGame();
        this.fetchLeaderboard();
    },
    methods: {
        resetGame() {
            // Build card set based on difficulty
            let matchSize = 2;
            let uniqueCount = 6;
            if (this.difficulty === 'easy') {
                matchSize = 2; uniqueCount = 6;
            } else if (this.difficulty === 'normal') {
                matchSize = 2; uniqueCount = 10;
            } else if (this.difficulty === 'hard') {
                matchSize = 3; uniqueCount = 10;
            }

            // Generate the symbol values if not enough provided
            const baseValues = this.values.slice();
            // extend baseValues if needed (use letters)
            let i = 0;
            while (baseValues.length < uniqueCount) {
                baseValues.push(String.fromCharCode(65 + baseValues.length));
                i++;
            }

            // Create repeated sets according to matchSize
            let cards = [];
            for (let j = 0; j < uniqueCount; j++) {
                for (let k = 0; k < matchSize; k++) {
                    cards.push({ value: baseValues[j], flipped: false, matched: false, groupId: j });
                }
            }

            const shuffled = cards
                .map((v) => ({ ...v, sort: Math.random() }))
                .sort((a, b) => a.sort - b.sort)
                .map(({ value, flipped, matched, groupId }) => ({ value, flipped, matched, groupId }));

            this.cards = shuffled;
            this.flippedIndices = [];
            this.moves = 0;
            this.startTime = Date.now();
            // Reset result state so user can save a new run after restarting
            this.resultsSaved = false;
            this.showResults = false;
            this.elapsedSeconds = null;
        },
        setDifficulty(level) {
            this.difficulty = level;
            this.showDifficultyPicker = false;
            this.resetGame();
        },
        boardColumns() {
            const total = this.cards.length || 0;
            if (total <= 12) return 4;
            if (total <= 20) return 5;
            return 6;
        },
        flipCard(index) {
            // Determine required match size based on difficulty
            const matchSize = this.difficulty === 'hard' ? 3 : 2;

            // Guard: cannot flip an already flipped or matched card
            if (this.cards[index].flipped || this.cards[index].matched) return;

            // Guard: do not allow flipping more than matchSize cards at once
            if (this.flippedIndices.length >= matchSize) return;

            // Flip the card and record selection
            this.cards[index].flipped = true;
            this.flippedIndices.push(index);

            // Only evaluate when we've flipped enough cards for a match
            if (this.flippedIndices.length === matchSize) {
                this.moves++;
                const indices = [...this.flippedIndices];
                const values = indices.map(i => this.cards[i].value);

                const allEqual = values.every(v => v === values[0]);
                if (allEqual) {
                    // mark matched
                    indices.forEach(i => (this.cards[i].matched = true));
                    this.flippedIndices = [];
                    // Check for completion
                    if (this.cards.every(c => c.matched)) {
                        this.onGameComplete();
                    }
                } else {
                    setTimeout(() => {
                        indices.forEach(i => (this.cards[i].flipped = false));
                        this.flippedIndices = [];
                    }, 1000);
                }
            }
        },

        onGameComplete() {
            const endTime = Date.now();
            const elapsedMs = endTime - this.startTime;
            this.elapsedSeconds = +(elapsedMs / 1000).toFixed(3);
            this.showResults = true;
        },

        async saveResult() {
            if (this.resultsSaved) return;

            if (!this.$page?.props?.auth?.user) {
                alert('Please log in to save your result');
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                // Ensure CSRF cookie is set (required for session-based Sanctum auth)
                try {
                    await window.axios.get('/sanctum/csrf-cookie');
                } catch (e) {
                    // ignore; server may not expose this endpoint if using token auth
                }

                const res = await window.axios.post('/api/memory/result', {
                    moves: this.moves,
                    time_seconds: this.elapsedSeconds,
                    user_id: this.$page.props.auth.user.id,
                    difficulty: this.difficulty,
                });

                const data = res.data;

                if (!data.success) {
                    alert(data.message || 'Not saved');
                    return;
                }

                // Server now always persists the run to the user's personal history and
                // returns whether it improved public leaderboard metrics. We mark the run saved
                // for the user (so Save button locks) and refresh leaderboards.
                this.resultsSaved = true;
                if (data.improved) {
                    alert('Result saved — this improved your personal best!');
                } else {
                    alert('Result saved to your personal history (did not improve public best).');
                }

                // refresh leaderboard (public will only change if improved; personal page will show the run)
                this.fetchLeaderboard();
            } catch (err) {
                console.error('Failed to save memory result', err);
                // Normalize axios errors
                const msg = err.response?.data?.message || err.message || 'unknown error';
                alert('Failed to save result: ' + msg);
            }
        },
        // personal best logic moved to Profile/MyRuns page

        async fetchLeaderboard() {
            try {
                if (this.leaderboardScope === 'personal') {
                    // personal leaderboard requires auth
                    if (!this.$page.props.auth.user) {
                        this.leaderboardUsers = [];
                        return;
                    }

                    // use axios so cookies/xsrf are handled consistently
                    const res = await window.axios.get(`/api/memory/personal?count=20&sort=${this.leaderboardSort}&difficulty=${this.difficulty}`);
                    const data = res.data || [];
                    // The shared LeaderBoard expects rows with a `user` object (public endpoint returns memory_results with user relation).
                    // Personal endpoint returns runs without `user`; add the current user's name so the table shape matches.
                    const mapped = data.map(r => ({
                        ...r,
                        user: { name: this.$page.props.auth.user?.name || 'You' }
                    }));
                    this.leaderboardUsers = mapped;
                    return;
                }

                // use axios for public leaderboard as well so auth/csrf cookies are handled consistently
                const res = await window.axios.get(`/api/memory/leaderboard?count=10&sort=${this.leaderboardSort}&difficulty=${this.difficulty}`);
                const data = res.data || [];
                this.leaderboardUsers = data;
            } catch (err) {
                console.error('Failed to fetch leaderboard', err);
                this.leaderboardUsers = [];
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