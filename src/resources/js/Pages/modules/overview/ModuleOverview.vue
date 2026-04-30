<script setup>
import { computed, onMounted } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import StatsCard from "@/Pages/modules/dashboard/components/StatsCard.vue";
import Watchlist from "@/Pages/modules/dashboard/components/Watchlist.vue";
import DashboardTable from "@/Pages/modules/dashboard/components/DashboardTable.vue";
import ActivityTimeline from "@/Pages/modules/dashboard/components/ActivityTimeline.vue";


const props = defineProps({
    stats: {
        type: Array,
        default: () => []
    },
    tableData: {
        type: Array,
        default: () => []
    },
    recentActivity: {
        type: Array,
        default: () => []
    },
    overdueFiles: {
        type: Array,
        default: () => []
    }
})

const url = computed(() => usePage().url)

const moduleConfigs = {
    '/my-desk': {
        title: 'My Digital Desk',
        tableTitle: "Files on my Desk",
        activityTitle: "My Recent Actions",
        watchlistTitle: "Overdue Actions",
        watchlistType: 'overdue' // Key to switch logic
    },
    '/registry': {
        title: 'Registry Overview',
        tableTitle: "Today's Intake",
        activityTitle: "Recent Registry Activity",
        watchlistTitle: "Temp File Watchlist",
        watchlistType: 'temp'
    },
    '/admin': {
        title: 'System Admin Overview',
        tableTitle: "Today's Intake",
        activityTitle: "Recent Registry Activity",
        watchlistTitle: "Temp File Watchlist",
        watchlistType: 'temp'
    },
    '/org': {
        title: 'Department Overview',
        tableTitle: "Today's Intake",
        activityTitle: "Recent Registry Activity",
        watchlistTitle: "Temp File Watchlist",
        watchlistType: 'temp'
    },
    '/tracking': {
        title: 'Tracking Overview',
        tableTitle: "Today's Intake",
        activityTitle: "Recent Registry Activity",
        watchlistTitle: "Temp File Watchlist",
        watchlistType: 'temp'
    },
    // ... admin and organization configs
}

const currentConfig = computed(() => {
    const key = Object.keys(moduleConfigs).find(k => url.value.startsWith(k))
    return moduleConfigs[key] || moduleConfigs['/registry']
})

// REAL-TIME SETUP
onMounted(() => {
    // Listen for file updates
    window.Echo.private(`user.${usePage().props.auth.user.id}`)
        .listen('.FileMoved', (e) => {
            // Trigger a silent partial reload to refresh props
            router.reload({ only: ['stats', 'tableData', 'recentActivity', 'overdueFiles'] })
        })
})
</script>

<template>
    <ContextModuleLayout>
        <Head :title="currentConfig.title" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between border-b pb-4">
                <h1 class="text-2xl font-black text-[#091E3E] tracking-tighter uppercase italic">
                    {{ currentConfig.title }}
                </h1>
                <div class="flex items-center gap-2">
                    <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Live System</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <StatsCard v-for="stat in stats" :key="stat.title" v-bind="stat" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Table Component -->
                <div class="lg:col-span-2">
                    <DashboardTable
                        :title="currentConfig.tableTitle"
                        :data="tableData"
                    />
                </div>

                <!-- Watchlist Component (Changes based on Module) -->
                <div>
                    <Watchlist
                        :title="currentConfig.watchlistTitle"
                        :type="currentConfig.watchlistType"
                        :items="
                        currentConfig.watchlistType === 'overdue'
                            ? (overdueFiles || [])
                            : (tableData || []).filter(i => i.is_priority)
                    "
                    />
                </div>
            </div>

            <!-- Timeline Component -->
            <ActivityTimeline
                :title="currentConfig.activityTitle"
                :activities="recentActivity"
            />
        </div>
    </ContextModuleLayout>
</template>
