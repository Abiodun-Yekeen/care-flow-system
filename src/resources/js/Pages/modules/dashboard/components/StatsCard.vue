

<template>
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm border-t-4 ">
        <p class="text-xs font-bold  tracking-wider text-slate-500">{{ title }}</p>
        <div class="mt-2 flex items-center justify-between">
            <h3 class=" font-bold text-[#091E3E]">{{ value }}</h3>

            <div class="rounded-full bg-blue-50 p-2 text-[#06A3DA]">

            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    value: [String, Number],
})
</script>
