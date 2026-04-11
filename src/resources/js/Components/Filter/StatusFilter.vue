<!-- resources/js/Components/Filter/StatusFilter.vue -->
<script setup>
import { ref, computed } from 'vue'


</script>

<template>
    <div class="space-y-1">

            <div class="flex justify-between border-b border-white/10 pb-3">
                <span class="text-sm ">Current Holder</span>
                <span class="text-sm font-medium">Ayo Yusuf -HOD Admin</span>
            </div>
            <div class="flex justify-between border-b border-white/10 pb-3">
                <span class="text-sm ">Current Department</span>
                <span class="text-sm font-medium">ICT</span>
            </div>
            <div class="flex justify-between border-b border-white/10 pb-3">
                <span class="text-sm ">Last Movement</span>
                <span class="text-sm font-medium">10 Apr 2026</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm ">File Status</span>
                <span class="bg-blue-500 text-white text-[10px] px-2 py-0.5 rounded uppercase font-bold">Pending</span>
            </div>


        <button class="w-full mt-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-bold transition">
            VIEW MOVEMENT HISTORY
        </button>
    </div>
</template>
