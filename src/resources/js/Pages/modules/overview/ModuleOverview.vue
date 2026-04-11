<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import { usePermissions } from '@/Composables/auth/usePermissions.js'
import { useUiStore } from "@/core/stores/uiStore.js" // Import UI Store
import { storeToRefs } from "pinia"
import {
    UserCheck,
    FlaskConical,
    FileSignature,
    AlertCircle,
    Clock,
    Plus,
    Search
} from "lucide-vue-next"
import StatsCard from "@/Pages/modules/dashboard/components/StatsCard.vue";

// Initialize Stores
const ui = useUiStore()
const { loading } = storeToRefs(ui) // This fixes the "loading was accessed" warning

const title = usePage().props.title

const { can } = usePermissions();

</script>

<template>
    <ContextModuleLayout>
        <Head title="Registry Overview"/>


            <div class="space-y-6">
                <div class="flex items-center justify-between border-b pb-4">
                    <h1 class="text-2xl font-bold text-[#091E3E]">Registry Overview</h1>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <StatsCard title="Files Received" value="15" />
                    <StatsCard title="Submitted" value="6" />
                    <StatsCard title="Pending" value="3" />
                    <StatsCard title="Treated" value="1" />
                    <StatsCard title="Closed" value="0" />
                    <StatsCard title="Await Scan" value="5" />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">

                    <div class="lg:col-span-2 space-y-4">
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="bg-slate-50 px-3 py-3 border-b border-slate-200">
                                <h2 class="font-bold text-[#091E3E]">Today's Intake</h2>
                            </div>
                            <div class="p-0">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 text-slate-500  text-[10px] tracking-wider">
                                    <tr>
                                        <th class="">File No.</th>
                                        <th class="">Subject</th>
                                        <th class="">Status</th>
                                        <th class="">Time</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                    <tr v-for="i in 5" :key="i" class="hover:bg-slate-50 transition">
                                        <td class=" text-[#06A3DA]">FETH/ADM/202{{i}}</td>
                                        <td class=" text-slate-600">Patient Referral Case #{{i}}02</td>
                                        <td class="">
                                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-bold">Submitted</span>
                                        </td>
                                        <td class="text-slate-400">10:45 AM</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>

                    <div class="space-y-6">

                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                            <div class="bg-slate-50 px-5 py-3 border-b border-slate-200">
                                <h2 class="font-bold text-[#091E3E]">Temp File Watchlist</h2>
                            </div>
                            <div class="p-4 space-y-4">
                                <div v-for="i in 2" :key="i" class="border-l-4 border-l-amber-400 pl-3 py-1">
                                    <p class="text-xs font-bold text-slate-700">TMP-2026-00{{i}}</p>
                                    <p class="text-[11px] text-slate-500">Awaiting official number (2 days ago)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<!--                <div class="bg-white rounded-xl border border-slate-200 shadow-sm">-->
<!--                    <div class="bg-slate-50 px-5 py-3 border-b border-slate-200">-->
<!--                        <h2 class="font-bold text-[#091E3E]">Recent Registry Activity</h2>-->
<!--                    </div>-->
<!--                    <div class="p-5">-->
<!--                        <div class="flex items-center gap-4 text-sm text-slate-600 border-b pb-3 border-slate-50">-->
<!--                            <div class="h-2 w-2 rounded-full bg-[#06A3DA]"></div>-->
<!--                            <p><strong>Super Admin</strong> registered file <strong>FETH/REG/091</strong> and assigned to HOD.</p>-->
<!--                            <span class="ml-auto text-xs text-slate-400">2 minutes ago</span>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-5 py-3 border-b border-slate-200 flex justify-between items-center">
                        <h2 class="font-bold text-[#091E3E]">Recent Registry Activity</h2>
                        <button class="text-xs font-semibold text-[#06A3DA] hover:underline">View All Audit Logs</button>
                    </div>

                    <div class="p-6">
                        <div class="relative">
                            <div class="absolute left-2.5 top-0 h-full w-0.5 bg-slate-100"></div>

                            <div class="space-y-8">
                                <div class="relative pl-8">
                                    <div class="absolute left-0 top-1.5 h-5 w-5 rounded-full border-4 border-white bg-[#06A3DA] shadow-sm"></div>

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <p class="text-sm text-slate-700">
                                            <span class="font-bold text-[#091E3E]">Super Admin</span>
                                            registered file
                                            <span class="font-mono font-bold text-[#06A3DA] bg-blue-50 px-1.5 py-0.5 rounded">FETH/REG/091</span>
                                            and assigned it to
                                            <span class="font-semibold text-slate-800">HOD Admin</span>
                                        </p>
                                        <span class="text-xs font-medium text-slate-400 whitespace-nowrap">2 mins ago</span>
                                    </div>
                                </div>

                                <div class="relative pl-8">
                                    <div class="absolute left-0 top-1.5 h-5 w-5 rounded-full border-4 border-white bg-slate-300"></div>

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <p class="text-sm text-slate-600">
                                            <span class="font-bold text-[#091E3E]">Registry Clerk</span>
                                            received physical document for
                                            <span class="font-mono font-semibold">TMP-2026-004</span>
                                        </p>
                                        <span class="text-xs font-medium text-slate-400 whitespace-nowrap">45 mins ago</span>
                                    </div>
                                </div>

                                <div class="relative pl-8">
                                    <div class="absolute left-0 top-1.5 h-5 w-5 rounded-full border-4 border-white bg-amber-400"></div>

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <p class="text-sm text-slate-600">
                                            File <span class="font-mono font-semibold">FETH/ADM/202</span> was
                                            <span class="text-amber-600 font-bold">Returned</span>
                                            by HOD due to missing scan pages
                                        </p>
                                        <span class="text-xs font-medium text-slate-400 whitespace-nowrap">2 hours ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


    </ContextModuleLayout>
</template>
