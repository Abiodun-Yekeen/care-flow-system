<script setup>
import { Mail } from "lucide-vue-next"
defineProps({ name: String, email: String })
</script>

<template>
    <div class="flex items-center gap-3">
        <div class="h-9 w-9 rounded-full bg-[#06A3DA]/10 flex items-center justify-center text-[#06A3DA] font-bold text-xs border border-[#06A3DA]/20 uppercase">
            {{ name?.charAt(0) || '?' }}
        </div>
        <div>
            <div class="font-bold text-[#091E3E] text-sm">{{ name }}</div>
            <div v-if="email" class="text-[11px] text-slate-500 flex items-center gap-1">
                <Mail :size="10" /> {{ email }}
            </div>
        </div>
    </div>
</template>
