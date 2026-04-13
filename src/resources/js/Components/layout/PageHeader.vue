<script setup>

const props=defineProps({
    title:String,
    subtitle:String
})
</script>

<template>
    <header class="bg-white p-1 border-b shrink-0 z-10">
        <h1 class="text-2xl font-bold text-[#091E3E]">{{title}}</h1>
        <p class="text-sm text-slate-500">{{ subtitle }}</p>
    </header>
</template>


