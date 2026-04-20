<script setup>
defineProps({
    title: String,
})
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold">Module Overview Works</h1>
        <p>{{ title }}</p>
    </div>
</template>
