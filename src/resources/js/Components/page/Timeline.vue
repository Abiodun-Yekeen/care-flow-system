<script setup>
import { 
    CheckCircle2, Clock, ArrowRight, User, 
    MessageSquare, Send, Calendar 
} from 'lucide-vue-next';

const props = defineProps({
    movements: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <div class="relative px-2 py-4">
        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-slate-100"></div>

        <div v-if="movements.length > 0" class="space-y-8 relative">
            <div v-for="(move, index) in movements" :key="move.id" class="flex gap-4">
                
                <div class="relative flex flex-col items-center">
                    <div :class="[
                        'z-10 w-8 h-8 rounded-full flex items-center justify-center ring-4 ring-white shadow-sm',
                        index === 0 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500'
                    ]">
                        <CheckCircle2 v-if="index === 0" :size="16" />
                        <Clock v-else :size="16" />
                    </div>
                </div>

                <div class="flex-1 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all">
                    <div class="bg-slate-50/50 px-4 py-2 border-b border-slate-100 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">
                                {{ move.movement_type === 'submitted_to_hod' ? 'Created and Routed' : 'Routed' }}
                            </span>
                            <span v-if="index === 0" class="text-[8px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold uppercase">
                                Current
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2 text-slate-400">
                            <Calendar :size="10" />
                            <span class="text-[10px] font-mono font-bold uppercase">
                                {{ move.acted_at || 'Pending' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-4 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-1 p-1.5 bg-slate-100 text-slate-600 rounded-md">
                                <User :size="14" />
                            </div>
                            <div class="flex-1">
                                <p class="text-[9px] text-slate-400 font-bold uppercase mb-1">Processed By (Actor)</p>
                                <p class="text-sm font-bold text-slate-800 leading-tight">
                                    {{ move.from_user_name }} 
                                    <span class="text-[10px] font-medium text-slate-500 block">
                                        {{ move.from_dept_name }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="bg-amber-50/50 border border-amber-100/50 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <MessageSquare :size="12" class="text-amber-500" />
                                <span class="text-[10px] font-bold text-amber-600 uppercase">Minute</span>
                            </div>
                            <p class="text-xs text-slate-700 italic leading-relaxed">
                                "{{ move.minute || move.remarks || 'No instructions provided.' }}"
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-3 border-t border-slate-50">
                            <div class="flex-1">
                                <p class="text-[9px] text-slate-400 font-bold uppercase mb-1">Routed To</p>
                                <div class="flex items-center gap-2 text-blue-700">
                                    <Send :size="12" />
                                    <span class="text-xs font-black uppercase tracking-tight">
                                        {{ move.to_user_name || 'Unassigned' }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5 ml-5 font-medium">
                                    {{ move.to_dept_name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>