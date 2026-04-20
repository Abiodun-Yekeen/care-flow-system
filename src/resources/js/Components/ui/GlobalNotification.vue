<script setup>
import {useNotificationStore} from "@/core/stores/useNotificationStore.js";

const notificationStore = useNotificationStore();

// Helper to get Tailwind classes based on type
const getClasses = (type) => {
    const styles = {
        success: 'bg-[#067d1c] border-green-800',
        error:   'bg-[#d13212] border-red-800',
        warning: 'bg-[#ff9900] border-orange-700',
        info:    'bg-[#0073bb] border-blue-800'
    };
    return styles[type] || styles.info;
};
</script>

<template>
    <div class="fixed top-4 right-4 z-[100] w-full max-w-sm flex flex-col gap-3 pointer-events-none">

        <div v-for="alert in notificationStore.criticalAlerts" :key="alert.id" class="pointer-events-auto">
            <div :class="['flex items-center p-4 rounded-xl shadow-2xl text-white font-medium text-sm border-l-4 transition-all transform hover:scale-[1.02]', getClasses(alert.type)]">
                <div class="mr-3 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <span class="font-bold opacity-80 block text-[10px] tracking-widest text-white/90 uppercase">Critical</span>
                    {{ alert.message }}
                </div>
                <button @click="notificationStore.removeCriticalAlert(alert.id)" class="ml-4 hover:bg-black/10 rounded-full w-6 h-6 flex items-center justify-center transition-colors text-xl">&times;</button>
            </div>
        </div>

        <transition
            enter-active-class="transform transition duration-300 ease-out"
            enter-from-class="translate-x-8 opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="notificationStore.current.visible" class="pointer-events-auto">
                <div :class="['flex items-center p-4 rounded-xl shadow-xl text-white font-medium text-sm border-l-4 transition-all', getClasses(notificationStore.current.type)]">
                    <div class="mr-3 flex-shrink-0">
                        <svg v-if="notificationStore.current.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1">{{ notificationStore.current.message }}</div>
                    <button @click="notificationStore.hide" class="ml-4 hover:bg-black/10 rounded-full w-6 h-6 flex items-center justify-center transition-colors text-xl">&times;</button>
                </div>
            </div>
        </transition>

    </div>
</template>
