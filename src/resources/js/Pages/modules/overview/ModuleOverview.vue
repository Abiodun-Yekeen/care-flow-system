<script setup>
import { computed } from 'vue'
import { Head, usePage, Link } from '@inertiajs/vue3'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import StatsCard from "@/Pages/modules/dashboard/components/StatsCard.vue";

// These props come from your Laravel Controller
const props = defineProps({
    stats: {
        type: Array,
        default: () => [] // Format: [{ title: 'Files', value: 10, color: 'blue' }]
    },
    tableData: {
        type: Array,
        default: () => [] // Format: [{ id: 1, identifier: 'REF-01', details: '...', status: 'Pending', time: '10:00' }]
    },
    recentActivity: {
        type: Array,
        default: () => [] // Format: [{ id: 1, user: 'Admin', action: 'Created', target: 'File-01', time: '2m ago' }]
    }
})

const url = computed(() => usePage().url)

const moduleConfigs = {
    '/registry': {
        title: 'Registry Overview',
        tableTitle: "Today's Intake",
        activityTitle: "Recent Registry Activity",
        watchlistTitle: "Temp File Watchlist"
    },
    '/admin': {
        title: 'System Administration',
        tableTitle: "User Access Requests",
        activityTitle: "Security Audit Logs",
        watchlistTitle: "System Alerts"
    },
    '/organization': {
        title: 'Organization Overview',
        tableTitle: "Departmental Changes",
        activityTitle: "Organizational Updates",
        watchlistTitle: "Structure Notifications"
    }
}

const currentConfig = computed(() => {
    const key = Object.keys(moduleConfigs).find(k => url.value.startsWith(k))
    return moduleConfigs[key] || moduleConfigs['/registry']
})
</script>

<template>
    <ContextModuleLayout>
        <Head :title="currentConfig.title" />

        <div class="space-y-6">
            <div class="flex items-center justify-between border-b pb-4">
                <h1 class="text-2xl font-black text-[#091E3E] tracking-tighter uppercase">
                    {{ currentConfig.title }}
                </h1>
                <button @click="$inertia.reload()" class="text-[10px] font-bold bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded transition-all uppercase tracking-widest">
                    Refresh Data
                </button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <template v-if="stats.length > 0">
                    <StatsCard
                        v-for="stat in stats"
                        :key="stat.title"
                        :title="stat.title"
                        :value="stat.value"
                    />
                </template>
                <div v-else class="col-span-full p-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-400 text-xs font-bold uppercase tracking-widest">
                    Loading Real-time Stats...
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="bg-slate-50 px-3 py-3 border-b border-slate-200">
                            <h2 class="font-bold text-[#091E3E]">{{ currentConfig.tableTitle }}</h2>
                        </div>
                        <div class="p-0 overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 text-slate-500 text-[10px] tracking-wider uppercase">
                                    <tr>
                                        <th class="px-4 py-2">Identifier</th>
                                        <th class="px-4 py-2">Details</th>
                                        <th class="px-4 py-2">Status</th>
                                        <th class="px-4 py-2">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="row in tableData" :key="row.id" class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-[#06A3DA] font-black font-mono uppercase tracking-tighter">{{ row.identifier }}</td>
                                        <td class="px-4 py-3 text-slate-600 font-medium">{{ row.details }}</td>
                                        <td class="px-4 py-3">
                                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[9px] font-black uppercase">
                                                {{ row.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-400 font-mono text-[10px]">{{ row.time }}</td>
                                    </tr>
                                    <tr v-if="tableData.length === 0">
                                        <td colspan="4" class="p-10 text-center text-slate-400 text-xs italic">No entries for today.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="bg-slate-50 px-5 py-3 border-b border-slate-200">
                            <h2 class="font-bold text-[#091E3E]">{{ currentConfig.watchlistTitle }}</h2>
                        </div>
                        <div class="p-4 space-y-4">
                            <div v-for="item in tableData.filter(i => i.is_priority)" :key="item.id" class="border-l-4 border-l-amber-400 pl-3 py-1 bg-amber-50/30">
                                <p class="text-xs font-bold text-slate-700 uppercase tracking-tighter">{{ item.identifier }}</p>
                                <p class="text-[10px] text-slate-500 uppercase font-black">Needs Attention</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 px-5 py-3 border-b border-slate-200 flex justify-between items-center">
                    <h2 class="font-bold text-[#091E3E]">{{ currentConfig.activityTitle }}</h2>
                    <button class="text-xs font-semibold text-[#06A3DA] hover:underline">View All</button>
                </div>
                <div class="p-6">
                    <div v-if="recentActivity.length > 0" class="relative">
                        <div class="absolute left-2.5 top-0 h-full w-0.5 bg-slate-100"></div>
                        <div class="space-y-6">
                            <div v-for="activity in recentActivity" :key="activity.id" class="relative pl-8">
                                <div class="absolute left-0 top-1.5 h-5 w-5 rounded-full border-4 border-white bg-slate-300"></div>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <p class="text-sm text-slate-700">
                                        <span class="font-bold text-[#091E3E]">{{ activity.user }}</span>
                                        {{ activity.action }}
                                        <span class="font-mono font-bold text-[#06A3DA] bg-blue-50 px-1.5 py-0.5 rounded">{{ activity.target }}</span>
                                    </p>
                                    <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap uppercase tracking-widest">
                                        {{ activity.time }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-slate-400 italic">No recent activity found.</p>
                </div>
            </div>
        </div>
    </ContextModuleLayout>
</template>