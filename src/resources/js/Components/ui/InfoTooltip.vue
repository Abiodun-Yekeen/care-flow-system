<!-- resources/js/Components/UI/InfoTooltip.vue -->
<script setup>
import { Info } from "lucide-vue-next"
import { ref } from 'vue'

defineProps({
    text: {
        type: String,
        required: true
    },
    position: {
        type: String,
        default: 'top',
        validator: (value) => ['top', 'bottom', 'left', 'right'].includes(value)
    }
})

const show = ref(false)
</script>

<template>
    <div class="relative inline-block"
         @mouseenter="show = true"
         @mouseleave="show = false">
        <Info class="w-4 h-4 text-gray-400 cursor-help" />

        <div v-if="show"
             :class="[
                 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-800 rounded shadow-lg whitespace-nowrap',
                 {
                     'bottom-full left-1/2 transform -translate-x-1/2 mb-2': position === 'top',
                     'top-full left-1/2 transform -translate-x-1/2 mt-2': position === 'bottom',
                     'right-full top-1/2 transform -translate-y-1/2 mr-2': position === 'left',
                     'left-full top-1/2 transform -translate-y-1/2 ml-2': position === 'right'
                 }
             ]">
            {{ text }}
        </div>
    </div>
</template>
