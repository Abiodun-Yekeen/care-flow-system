<script setup>
defineProps({
    title: String,
    activities: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-5 py-3 border-b border-slate-200 flex justify-between items-center">
            <h2 class="font-bold text-[#091E3E] text-sm uppercase tracking-tight">{{ title }}</h2>
            <button class="text-[10px] font-black text-[#06A3DA] hover:text-[#091E3E] uppercase tracking-widest transition">
                Full Audit Trail
            </button>
        </div>
        <div class="p-6">
            <div v-if="activities.length > 0" class="relative">
                <!-- Vertical Line -->
                <div class="absolute left-2.5 top-0 h-full w-0.5 bg-slate-100"></div>

                <div class="space-y-8">
                    <div v-for="activity in activities" :key="activity.id" class="relative pl-8 group">
                        <!-- Dot -->
                        <div class="absolute left-0 top-1.5 h-5 w-5 rounded-full border-4 border-white bg-slate-300 group-hover:bg-[#06A3DA] transition-colors shadow-sm"></div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <p class="text-sm text-slate-700 leading-tight">
                                    <span class="font-bold text-[#091E3E]">{{ activity.user }}</span>
                                    <span class="text-slate-500 mx-1">{{ activity.action }}</span>
                                    <span class="font-mono font-bold text-[#06A3DA] bg-blue-50 px-1.5 py-0.5 rounded text-xs uppercase">
                                        {{ activity.target }}
                                    </span>
                                </p>
                                <p v-if="activity.description" class="text-xs text-slate-400 mt-1 italic">
                                    "{{ activity.description }}"
                                </p>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap uppercase tracking-widest bg-slate-50 px-2 py-1 rounded">
                                {{ activity.time }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="py-6 text-center text-slate-400 text-xs italic font-medium">
                No recent activity recorded.
            </div>
        </div>
    </div>
</template>

