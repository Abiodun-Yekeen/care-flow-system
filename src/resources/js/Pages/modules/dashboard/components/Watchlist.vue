<!-- Watchlist.vue -->
<template>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-5 py-3 border-b border-slate-200">
            <h2 class="font-bold text-[#091E3E] text-xs uppercase tracking-widest">{{ title }}</h2>
        </div>

        <!-- Add a null check (items?.length) or check if items exists -->
        <div class="p-4 space-y-3">
            <template v-if="items && items.length > 0">
                <div v-for="item in items" :key="item.id"
                     :class="[
                        'border-l-4 pl-3 py-2 transition hover:bg-slate-50',
                        type === 'overdue' ? 'border-l-red-500 bg-red-50/30' : 'border-l-amber-400 bg-amber-50/30'
                    ]"
                >
                    <div class="flex justify-between items-start">
                        <p class="text-xs font-bold text-slate-700 uppercase tracking-tighter">{{ item.identifier }}</p>
                        <span v-if="type === 'overdue'" class="text-[9px] font-black text-red-600 uppercase">
                            {{ item.days_overdue }} Days Late
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-500 line-clamp-1 italic">{{ item.details }}</p>
                </div>
            </template>

            <div v-else class="py-10 text-center">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                    {{ items ? 'All Clear' : 'Loading Watchlist...' }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
// Ensure items has a default value of an empty array
const props = defineProps({
    title: String,
    type: String,
    items: {
        type: Array,
        default: () => []
    }
})
</script>
