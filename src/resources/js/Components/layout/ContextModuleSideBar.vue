<script setup>
import { iconRegistry } from "@/config/Icon.js"
import { HelpCircle } from "lucide-vue-next"
import {Link} from "@inertiajs/vue3";

defineProps({
    isCollapsed: Boolean,
    subNavigation: Array,
    pathSegments: Array,
    isActive: Function,
    currentModuleIcon: [Object, Function, Array],
})

defineEmits(['toggle'])
</script>

<template>
    <aside
        v-if="subNavigation.length"
        :class="[isCollapsed ? 'w-16' : 'w-64']"
        class="flex flex-col border-r border-gray-100 transition-all duration-300 ease-in-out relative bg-white h-full shadow-sm"
    >
        <button
            @click="$emit('toggle')"
            class="absolute -right-3 top-10 bg-white border border-gray-200 rounded-full p-1 z-10 hover:bg-gray-50 text-gray-500 shadow-sm transition-transform active:scale-95"
        >
            <component
                :is="iconRegistry[!isCollapsed ? 'chevron-left' : 'chevron-right']"
                class="w-3 h-3"
            />
        </button>

        <div class="px-4 py-4 flex items-center overflow-hidden shrink-0">
            <div class="min-w-[32px] w-8 h-8 bg-secondary rounded flex items-center justify-center text-white shadow-sm shrink-0">
                <component :is="currentModuleIcon" class="w-5 h-5" />
            </div>
            <div v-if="!isCollapsed" class="ml-3 overflow-hidden transition-opacity duration-300">
                <h2 class="text-sm font-bold text-gray-900 truncate uppercase">{{ pathSegments[0] }}</h2>
                <p class="text-[9px] text-gray-500 uppercase font-bold tracking-tight">Resource Group</p>
            </div>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-gray-200">
            <Link
                v-for="item in subNavigation"
                :key="item.key"
                :href="item.route"
                class="group flex items-center px-4 py-2 text-xs font-semibold border-l-4 transition-all mb-1"
                :class="isActive(item)
                    ? 'bg-blue-50/50 text-secondary border-secondary'
                    : 'text-gray-600 border-transparent hover:bg-gray-100 hover:text-gray-900'"
                :title="isCollapsed ? item.label : ''"
            >
                <component
                    :is="iconRegistry[item.icon] || HelpCircle"
                    :class="[
                        isActive(item.route) ? 'text-secondary' : 'text-gray-400 group-hover:text-secondary',
                        isCollapsed ? 'mr-0' : 'mr-3'
                    ]"
                    class="h-4 w-4 shrink-0"
                    stroke-width="2"
                />
                <span v-if="!isCollapsed" class="truncate whitespace-nowrap">{{ item.label }}</span>
            </Link>
        </nav>
    </aside>
</template>
