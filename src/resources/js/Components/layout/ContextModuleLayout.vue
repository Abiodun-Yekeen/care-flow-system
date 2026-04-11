<script setup>
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import BreadCrumb from "@/Components/layout/BreadCrumb.vue"
import BaseCloseButton from "@/Components/forms/BaseCloseButton.vue"
import { iconRegistry, moduleIconMap } from "@/config/Icon.js"
import ContextModuleSideBar from "@/Components/layout/ContextModuleSideBar.vue"
import FullScreenLoader from "@/Components/ui/FullScreenLoader.vue";
import { useUiStore } from "@/core/stores/uiStore.js";
import { useNavigationStore } from "@/core/stores/useNavigationStore.js";
import { storeToRefs } from "pinia";

const ui = useUiStore()
const navStore = useNavigationStore() //  Initialize

const { loading } = storeToRefs(ui)
const { subNavigation: storeSubNav } = storeToRefs(navStore) // Get reactive sub-nav

const isCollapsed = ref(false)
const page = usePage()

const pathSegments = page.url.split('/').filter(Boolean)

const currentModuleIcon = computed(() => {
    const iconKey = moduleIconMap[pathSegments[0]]
    return iconRegistry[iconKey] || iconRegistry['stethoscope']
})

//  sub-navigation logic using the store
const subNavigation = computed(() => {
    const items = storeSubNav.value ?? [] // Use store value
    const baseSegment = pathSegments[0] ? `/${pathSegments[0]}` : '/dashboard'
    return [{
        key: 'overview',
        label: 'Overview',
        icon: 'overview',
        route: baseSegment
    }, ...items]
})

const isActive = (item) => {
    if (item.key === 'overview') return page.url === item.route;
    return page.url.startsWith(item.route);
}

const close = () => router.visit('/dashboard')
</script>

<template>
    <AuthenticatedLayout @toggle-sidebar="isCollapsed = !isCollapsed">


        <div class="flex flex-col h-screen bg-white overflow-hidden relative">


            <div class="px-4 py-2 flex justify-between items-center bg-white  shrink-0 z-20">
                <div class="min-w-0 overflow-hidden">
                    <BreadCrumb />
                </div>
                <BaseCloseButton @close="close" class="ml-4 flex-shrink-0" />
            </div>

            <div class="flex flex-1 overflow-hidden relative">

                <div v-if="!isCollapsed"
                     @click="isCollapsed = true"
                     class="fixed inset-0 bg-slate-900/40 z-30 lg:hidden backdrop-blur-sm transition-opacity">
                </div>

                <ContextModuleSideBar
                    :is-collapsed="isCollapsed"
                    :sub-navigation="subNavigation"
                    :path-segments="pathSegments"
                    :current-module-icon="currentModuleIcon"
                    :is-active="isActive"
                    @toggle="isCollapsed = !isCollapsed"
                    class="z-40"
                />

                <main class="flex-1 flex flex-col bg-white overflow-y-auto transition-all duration-300 w-full relative">

                    <FullScreenLoader v-if="loading" />

                    <div v-else>
                        <div v-if="$slots.command"
                             class="px-4 py-2 sticky top-0 bg-white/90 backdrop-blur-md z-10 border-b shadow-sm border-slate-100">
                            <slot name="command" />
                        </div>

                        <div class="p-4 sm:p-6 lg:p-8">
                            <slot />
                        </div>
                    </div>

                </main>


            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Ensure smooth transitions for width changes */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>



