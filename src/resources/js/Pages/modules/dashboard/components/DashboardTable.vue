<script setup>
defineProps({
    title: String,
    data: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex justify-between items-center">
            <h2 class="font-bold text-[#091E3E] text-sm uppercase tracking-tight">{{ title }}</h2>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-200/50 px-2 py-0.5 rounded-full">
                {{ data.length }} Entries
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/50 text-slate-500 text-[10px] tracking-wider uppercase border-b border-slate-100">
                <tr>
                    <th class="px-4 py-3 font-black">Identifier</th>
                    <th class="px-4 py-3 font-black">Details</th>
                    <th class="px-4 py-3 font-black text-center">Status</th>
                    <th class="px-4 py-3 font-black text-right">Time</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                <tr v-for="row in data" :key="row.id" class="hover:bg-blue-50/30 transition-colors group">
                    <td class="px-4 py-3">
                            <span class="text-[#06A3DA] font-black font-mono uppercase tracking-tighter group-hover:underline cursor-pointer">
                                {{ row.identifier }}
                            </span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 font-medium max-w-[200px] truncate">
                        {{ row.details }}
                    </td>
                    <td class="px-4 py-3 text-center">
                            <span :class="[
                                'px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-tighter',
                                row.status === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700'
                            ]">
                                {{ row.status }}
                            </span>
                    </td>
                    <td class="px-4 py-3 text-slate-400 font-mono text-[10px] text-right uppercase">
                        {{ row.time }}
                    </td>
                </tr>
                <tr v-if="data.length === 0">
                    <td colspan="4" class="p-12 text-center">
                        <div class="flex flex-col items-center gap-2 text-slate-300">
                            <span class="italic text-xs">No active records found for this section</span>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
