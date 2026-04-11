<!-- resources/js/Components/Form/FormSection.vue -->
<script setup>
import { ChevronDown, ChevronUp } from "lucide-vue-next"
import { ref } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: ''
    },
    collapsible: {
        type: Boolean,
        default: false
    },
    defaultCollapsed: {
        type: Boolean,
        default: false
    }
})

const isCollapsed = ref(props.defaultCollapsed)
</script>

<template>
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-bold text-gray-900 flex items-center">
                    <span v-if="icon" class="text-green-600 mr-2">{{ icon }}</span>
                    {{ title }}
                </h3>
                <p v-if="subtitle" class="text-xs text-gray-500 mt-0.5">{{ subtitle }}</p>
            </div>
            <button v-if="collapsible"
                    @click="isCollapsed = !isCollapsed"
                    class="text-gray-400 hover:text-gray-600">
                <ChevronUp v-if="!isCollapsed" class="w-4 h-4" />
                <ChevronDown v-else class="w-4 h-4" />
            </button>
        </div>

        <!-- Content -->
        <div v-show="!isCollapsed" class="p-6">
            <slot></slot>
        </div>
    </div>
</template>
