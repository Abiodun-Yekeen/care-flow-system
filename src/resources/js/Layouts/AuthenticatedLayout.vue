<script setup>
import { onMounted, ref, watch } from 'vue'
import { usePage } from "@inertiajs/vue3";
import { useUiStore } from "@/core/stores/uiStore.js";
import { useNotificationStore } from "@/core/stores/useNotificationStore.js";
import Header from "@/Components/layout/Header.vue";
import GlobalNotification from "@/Components/ui/GlobalNotification.vue";
import SessionTimeout from "@/Components/ui/SessionTimeout.vue";
import FullScreenLoader from "@/Components/ui/FullScreenLoader.vue";
import GlobalSideBar from "@/Layouts/GlobalSideBar.vue";

const page = usePage();
const notify = useNotificationStore();
const ui = useUiStore();

console.log(page.props)
const emit = defineEmits(['toggleSidebar'])

onMounted(() => {
    // Just handle UI state here
    ui.startLoading()
    // Small delay to ensure initial data is painted
    setTimeout(() => ui.stopLoading(), 100)
})


watch(() => page.props.flash, (flash) => {
    if (flash?.success) notify.success(flash.success);
    if (flash?.error) notify.error(flash.error);
}, { deep: true });

const sidebarOpen = ref(false)
</script>

<template>
    <div  class="min-h-screen !bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]flex flex-col">

        <Header @toggle-sidebar="sidebarOpen = true" />

        <div class="flex flex-1">
            <GlobalSideBar
                :open="sidebarOpen"
                @close="sidebarOpen = false"
            />

            <main class="flex-1 transition-all duration-300  !bg-white">
                <GlobalNotification />

                <slot />
            </main>

        </div>

    </div>
    <SessionTimeout />
    <FullScreenLoader v-if="ui.loading" />
</template>
