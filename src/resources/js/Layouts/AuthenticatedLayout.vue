<script setup>
import {onMounted, onUnmounted, ref, watch} from 'vue'
import Header from '@/Components/layout/Header.vue'
import Sidebar from '@/Layouts/GlobalSideBar.vue'
import {useNavigationStore} from "@/core/stores/useNavigationStore.js";
import {usePage} from "@inertiajs/vue3";
import FullScreenLoader from "@/Components/ui/FullScreenLoader.vue";
import {useUiStore} from "@/core/stores/uiStore.js";
import {useMetaStore} from "@/core/stores/metaStore.js";
import SessionTimeout from "@/Components/ui/SessionTimeout.vue";
import GlobalNotification from "@/Components/ui/GlobalNotification.vue";
import {useNotificationStore} from "@/core/stores/useNotificationStore.js";

const page = usePage();
// navigation should now be the top-level modules array from Laravel
const navigation = page.props.navigation;

const notify = useNotificationStore();



const navigationStore = useNavigationStore()
const metaStore = useMetaStore()
const ui = useUiStore()
const emit = defineEmits(['toggleSidebar'])
const user = page.props.auth.user;

onMounted(async () => {
    ui.startLoading()
    navigationStore.setModules(navigation || [])
    await metaStore.loadMeta()
    ui.stopLoading()

    // Get the history from the database
    await notify.fetchNotifications();
//  Start realtime listener
    notify.listenForNotifications(user.id);

    notify.listenForPushNotifications();
})

// onMounted(async () => {
//     ui.startLoading();
//     navigationStore.setModules(navigation || []);
//     await metaStore.loadMeta();
//
//     ui.stopLoading();
//
//     await notify.fetchNotifications();
// });
//
// watch(() => user.value, (newUser) => {
//     if (!newUser?.id) return;
//
//     console.log("👤 User detected:", newUser.id);
//
//     notify.listenForNotifications(newUser.id);
// }, { immediate: true });

// onMounted(async () => {
//     ui.startLoading()
//     navigationStore.setModules(navigation || [])
//     await metaStore.loadMeta()
//     ui.stopLoading()
//
//     // Get the history from the database
//     await notify.fetchNotifications();
//
//     //  Start realtime listener
//     notify.listenForNotifications(user.id);
//     notify.listenForPushNotifications();
//
// })


watch(() => page.props.flash, (flash) => {
    if (flash.success) {
        notify.success(flash.success);
    }
    if (flash.error) {
        notify.error(flash.error);
    }
}, { deep: true });

const sidebarOpen = ref(false)
</script>

<template>
    <div  class="min-h-screen !bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]flex flex-col">

        <Header @toggle-sidebar="sidebarOpen = true" />

        <div class="flex flex-1">
            <Sidebar
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
