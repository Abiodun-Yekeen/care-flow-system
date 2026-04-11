<script setup>
import {onMounted, ref} from 'vue'
import Header from '@/Components/layout/Header.vue'
import Sidebar from '@/Layouts/GlobalSideBar.vue'
import {useNavigationStore} from "@/core/stores/useNavigationStore.js";
import {usePage} from "@inertiajs/vue3";
import FullScreenLoader from "@/Components/ui/FullScreenLoader.vue";
import {useUiStore} from "@/core/stores/uiStore.js";
import {useMetaStore} from "@/core/stores/metaStore.js";
import SessionTimeout from "@/Components/ui/SessionTimeout.vue";
import GlobalNotification from "@/Components/ui/GlobalNotification.vue";

// navigation should now be the top-level modules array from Laravel
const navigation = usePage().props.navigation;

const navigationStore = useNavigationStore()
const metaStore = useMetaStore()
const ui = useUiStore()
const emit = defineEmits(['toggleSidebar'])
onMounted(async () => {
    ui.startLoading()
    navigationStore.setModules(navigation || [])
    await metaStore.loadMeta()
    ui.stopLoading()

})



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
