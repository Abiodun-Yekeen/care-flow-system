<!-- resources/js/Components/Form/FormDivider.vue -->
<script setup>
defineProps({
    label: {
        type: String,
        default: ''
    }
})
</script>

<template>
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div v-if="label" class="relative flex justify-center">
            <span class="px-3 bg-white text-xs font-bold text-gray-400 uppercase tracking-wider">
                {{ label }}
            </span>
        </div>
    </div>
</template>
