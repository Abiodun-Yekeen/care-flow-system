<script setup>
import Spinner from "./Spinner.vue"
</script>

<template>
    <div class="flex items-center justify-center min-h-[300px]">
        <div class="flex flex-col items-center">
            <Spinner size="large" />
            <p class="mt-3 text-sm font-medium text-gray-900 animate-pulse">Loading...</p>
        </div>
    </div>
</template>



