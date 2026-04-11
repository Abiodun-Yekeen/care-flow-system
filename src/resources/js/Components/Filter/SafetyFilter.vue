<!-- resources/js/Components/Filter/SafetyFilter.vue -->
<script setup>
import { ref } from 'vue'
import { AlertCircle, AlertTriangle, Shield, Pill } from "lucide-vue-next"

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['update:modelValue'])

const alertTypes = ref([
    { value: 'isolation', label: 'Isolation', icon: Shield, count: 45, color: 'yellow' },
    { value: 'fallRisk', label: 'Fall Risk', icon: AlertTriangle, count: 123, color: 'orange' },
    { value: 'allergies', label: 'Allergies', icon: AlertCircle, count: 267, color: 'red' },
    { value: 'codeStatus', label: 'DNR/DNI', icon: Pill, count: 89, color: 'purple' }
])

const toggleAlert = (value) => {
    const newValue = props.modelValue.includes(value)
        ? props.modelValue.filter(v => v !== value)
        : [...props.modelValue, value]
    emit('update:modelValue', newValue)
}
</script>

<template>
    <div class="space-y-1">
        <div v-for="alert in alertTypes"
             :key="alert.value"
             @click="toggleAlert(alert.value)"
             :class="[
                 'flex items-center justify-between p-2 rounded cursor-pointer transition',
                 modelValue.includes(alert.value)
                     ? `bg-${alert.color}-50`
                     : 'hover:bg-gray-50'
             ]">
            <div class="flex items-center space-x-2">
                <component :is="alert.icon" :class="['w-3 h-3', `text-${alert.color}-600`]" />
                <span class="text-sm">{{ alert.label }}</span>
            </div>
            <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">{{ alert.count }}</span>
        </div>
    </div>
</template>
