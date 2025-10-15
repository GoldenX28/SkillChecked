<script setup>
let resultsSaved = ref(false);
import { ref, useTemplateRef, onUnmounted, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  maxTime: { type: Number, default: 30 }
});
const emit = defineEmits(['restartGame']);

let startFlag = ref(false);
let showResults = ref(false);
let loading = ref(false);
let countdown = ref(props.maxTime);

let typedChars = 0;
let correctChars = 0;
let errors = 0;
let currentWPM = 0;
let accuracy = 0;

let wordIndex = 0;
let letterIndex = 0;

let inputRef = useTemplateRef('input');
let isInputFocused = ref(true);

let intervalId = null;
const displayText = ref([]);

const getText = async (text = "") => {
    const randomResponse = await fetch('https://en.wikipedia.org/api/rest_v1/page/random/summary');
    const data = await randomResponse.json();
    
    text = data.extract + " " + text;

    if (text.length < 1000) {
        return getText(text);
    }

    return text;
};

const startGame = async () => {
    loading.value = true;
    const text = await getText();

    let charId = 0;
    displayText.value = text.split(" ").map((word) => {
        word = word.split("").map((chr) => {
            return { id: charId++, chr: /\s/.test(chr) ? "\u00A0" : chr, status: "totype" };
        });
        word.push({ id: charId++, chr: "\u00A0", status: "totype" });
        return word;
    });

    loading.value = false;
    focusOnInput();
};

const onKeyDown = (event) => {
    if (
        event.key.length > 1 || 
        event.key === 'Backspace' ||
        event.key === 'Delete' ||
        event.key.startsWith('Arrow') ||
        event.ctrlKey || event.metaKey
    ) {
        return;
    }

    if (event.repeat) {
        event.preventDefault();
        return;
    }
    console.log(event.key);

    onTextInput(event.key);
};

const onTextInput = (key) => {
    if (!intervalId) {
        intervalId = setInterval(() => {
            countdown.value--;
            updateWPM();
            if (countdown.value === 0) {
                gameEnd();
                clearInterval(intervalId);
            }
        }, 1000);
    }

    if (displayText.value[wordIndex] === undefined) {
        return;
    }

    const displayChar = displayText.value[wordIndex][letterIndex];
    const typedChar = key == " " ? "\u00A0" : key.slice(-1);
    if (typedChar === displayChar.chr) {
        displayChar.status = "right";
        typedChars++;
        correctChars++;
    } else {
        displayChar.status = "wrong";
        typedChars++;
        errors++;
    }
    
    updateWPM();

    letterIndex++;
    if (!displayText.value[wordIndex][letterIndex]) {
        wordIndex++;
        letterIndex = 0;
    }
}

const updateWPM = () => {
    currentWPM = Math.round((correctChars / 5) / ((props.maxTime - countdown.value || 0.5) / 60));
};

const focusOnInput = () => {
    inputRef.value?.focus();
};

const handleFocus = () => {
    isInputFocused.value = true;
};
const handleBlur = () => {
    isInputFocused.value = false;
};

const gameEnd = () => {
    showResults.value = true;
    startFlag.value = false;
    accuracy = typedChars > 0 ? Math.round((correctChars / typedChars) * 100) : 0;
};



const saveResultsButton = async () => {
    if (resultsSaved.value) return;
    const results = {
        wpm: currentWPM,
        correct_chars: correctChars,
        errors: errors,
        typed_chars: typedChars,
        accuracy: accuracy,
    };
    
    try {
        const data = await saveResults(results);  // Remove response.json() call here
        console.log('Success:', data);
        resultsSaved.value = true;
        alert('Results saved successfully!');
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to save results: ' + error.message);
    }
};

const saveResults = async (results) => {
    try {
        const page = usePage();
        console.log('Auth state:', page.props.auth); // Debug auth state

        if (!page.props.auth?.user) {
            throw new Error('Please log in to save your results');
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('CSRF token:', token); // Debug CSRF token

        const response = await fetch('/api/typespeed/result', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include',
            body: JSON.stringify({
                ...results,
                user_id: page.props.auth.user.id
            })
        });

        console.log('Response status:', response.status); // Debug response

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Failed to save results');
        }

        return data;
    } catch (error) {
        console.error('Save error:', error);
        throw error;
    }
};

const restartGame = () => {
    emit('restartGame');
};

onMounted(async () => {
    await startGame();
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

<template>
    <input 
        ref="input"
        class="opacity-0 absolute"
        @keydown="onKeyDown"
        @focus="handleFocus"
        @blur="handleBlur"
        type="text" 
        autocomplete="off" 
        autocapitalize="off" 
        autocorrect="off" 
        data-gramm="false" 
        spellcheck="false"
    >

    <div v-if="loading" class="w-full h-full flex flex-col items-center justify-center">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-12 w-12 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <span class="text-xl font-semibold text-blue-600">Loading text...</span>
        </div>
    </div>
    <div v-else-if="!showResults" class="w-full h-full flex flex-col items-center justify-center">
        <div class="game h-full flex-col justify-center items-center" @click="focusOnInput">
            <div class="w-full flex justify-center items-center py-4 border-b border-gray-200 bg-white/80">
                <span v-if="!intervalId" class="text-xl font-semibold text-gray-700">Start typing to start</span>
                <template v-else>
                    <div class="flex flex-col items-center mr-8">
                        <span class="text-lg font-semibold text-gray-700">WPM</span>
                        <span class="text-2xl font-bold text-blue-600">{{ currentWPM }}</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-lg font-semibold text-gray-700">Countdown</span>
                        <span class="text-2xl font-bold text-red-600">{{ countdown }}</span>
                    </div>
                </template>
            </div>
            
            <div class="displayed-text flex flex-wrap">
                <div v-for="(word, wIndex) in displayText" :key="wIndex" class="flex flex-nowrap">
                    <div v-for="(character, cIndex) in word" :key="character.id" class="relative">
                        <span 
                            :class="character.status === 'wrong' ? 'bg-red-200' 
                                : character.status === 'right' ? 'bg-green-200' : ''">
                            {{ character.chr }}
                        </span>
                        <span 
                            v-if="wIndex === wordIndex && cIndex === letterIndex 
                            && !showResults && isInputFocused"
                            class="absolute left-0 top-0 h-full w-[2px] bg-blue-600 animate-blink">
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div v-else class="results-screen w-full h-full flex flex-col items-center justify-center bg-white/90">
        <h2 class="text-3xl font-bold mb-6 text-blue-700">Results</h2>
        <div class="flex flex-col gap-4 mb-8">
            <div class="text-xl text-gray-800">WPM: <span class="font-bold">{{ currentWPM }}</span></div>
            <div class="text-xl text-gray-800">Correct Chars: <span class="font-bold">{{ correctChars }}</span></div>
            <div class="text-xl text-gray-800">Errors: <span class="font-bold">{{ errors }}</span></div>
            <div class="text-xl text-gray-800">Total Typed: <span class="font-bold">{{ typedChars }}</span></div>
            <div class="text-xl text-gray-800">Accuracy: <span class="font-bold">{{ accuracy }}%</span></div>
        </div>
        <div class="flex gap-4">
            <button
                v-if="$page.props.auth.user"
                class="px-6 py-2 rounded text-lg font-semibold transition-colors duration-200"
                :class="resultsSaved
                    ? 'bg-gray-400 text-gray-200 cursor-not-allowed opacity-70'
                    : 'bg-blue-500 text-white hover:bg-blue-600'"
                @click="saveResultsButton"
                :disabled="resultsSaved"
            >
                Save Results
            </button>
            <button class="bg-gray-500 text-white px-6 py-2 rounded text-lg font-semibold" @click="restartGame">Restart</button>
        </div>
    </div>
</template>

<style>
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}
.animate-blink {
    animation: blink 1s step-end infinite;
}
</style>