<!-- resources/js/Components/Filter/FilterSection.vue -->
<script setup>
import { ChevronDown, ChevronUp } from "lucide-vue-next"
import { ref } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    count: {
        type: Number,
        default: 0
    }
})

const isExpanded = ref(true)
</script>

<template>
    <div class="border-b border-gray-100 pb-3">
        <div class="flex justify-between items-center cursor-pointer mb-2"
             @click="isExpanded = !isExpanded">
            <div class="flex items-center space-x-2">
                <span class="text-[10px] font-black uppercase text-gray-400">{{ title }}</span>
                <span v-if="count > 0" class="bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full text-[8px] font-bold">
                    {{ count }}
                </span>
            </div>
            <component :is="isExpanded ? ChevronUp : ChevronDown" class="w-3 h-3 text-gray-400" />
        </div>
        <div v-show="isExpanded" class="space-y-1">
            <slot></slot>
        </div>
    </div>
</template>
