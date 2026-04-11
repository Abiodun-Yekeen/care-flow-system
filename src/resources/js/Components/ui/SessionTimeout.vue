<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import {router, usePage} from '@inertiajs/vue3';
import {metaApi} from "@/core/api/metaApi.js";

const page = usePage();

// Get lifetime from Laravel (e.g., 1 minute)
const SESSION_LIFETIME_MINUTES = window.AppConfig?.sessionLifetime || 120;
const warningBeforeSeconds = 45; // Show warning after 15 seconds if lifetime is 1 min

const showModal = ref(false);
const timeLeft = ref(warningBeforeSeconds);
let warningTimer = null;
let countdownInterval = null;

const startTimers = () => {
    clearTimeout(warningTimer);
    clearInterval(countdownInterval);
    showModal.value = false;

    // Calculate: (Total Minutes * 60) - Warning Window
    const totalSeconds = SESSION_LIFETIME_MINUTES * 60;
    const timeUntilWarning = (totalSeconds - warningBeforeSeconds) * 1000;

    warningTimer = setTimeout(() => {
        showModal.value = true;
        startCountdown();
    }, Math.max(timeUntilWarning, 0));
};

const startCountdown = () => {
    timeLeft.value = warningBeforeSeconds;
    countdownInterval = setInterval(() => {
        timeLeft.value--;
        if (timeLeft.value <= 0) {
            logout();
        }
    }, 1000);
};

const stayLoggedIn = async () => {
    try {
        await metaApi.getSessionRefresh();
        startTimers();
    } catch (error) {
        logout();
    }
};

const logout = () => {
    clearInterval(countdownInterval);
    // Directly to the login page to avoid the CSRF check on the logout route
    window.location.replace('/login?expired=1');
};

// Reset timer on every Inertia navigation
watch(() => page.url, () => startTimers());

onMounted(() => startTimers());
onUnmounted(() => {
    clearTimeout(warningTimer);
    clearInterval(countdownInterval);
});
</script>

<template>
    <div v-if="showModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/75 backdrop-blur-sm p-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-6 text-center border border-gray-100">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 mb-4">
                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h3 class="text-lg font-bold text-gray-900">Are you still there?</h3>
            <p class="mt-2 text-sm text-gray-600">
                Your session is about to expire. For your security, you will be logged out in
                <span class="font-mono font-bold text-red-600 text-lg">{{ timeLeft }}</span> seconds.
            </p>

            <div class="mt-6 flex flex-col gap-3">
                <button @click="stayLoggedIn" class="w-full inline-flex justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-600">
                    Stay Logged In
                </button>
                <button @click="logout" class="w-full inline-flex justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Logout Now
                </button>
            </div>
        </div>
    </div>
</template>
