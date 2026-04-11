<!-- resources/js/Components/Filter/RiskFilter.vue -->
<script setup>
import { ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['update:modelValue'])

const riskLevels = ref([
    { value: 'high', label: 'High Risk', color: 'red', count: 56 },
    { value: 'moderate', label: 'Moderate', color: 'orange', count: 234 },
    { value: 'low', label: 'Low Risk', color: 'green', count: 950 }
])

const toggleRisk = (value) => {
    const newValue = props.modelValue.includes(value)
        ? props.modelValue.filter(v => v !== value)
        : [...props.modelValue, value]
    emit('update:modelValue', newValue)
}
</script>

<template>
    <div class="grid grid-cols-2 gap-2">
        <button v-for="risk in riskLevels"
                :key="risk.value"
                @click="toggleRisk(risk.value)"
                :class="[
                    'text-[10px] font-bold border rounded p-2 transition',
                    modelValue.includes(risk.value)
                        ? `bg-${risk.color}-50 border-${risk.color}-500 text-${risk.color}-700`
                        : 'hover:bg-gray-50 border-gray-200'
                ]">
            {{ risk.label }}
            <span class="block text-[8px] text-gray-500">{{ risk.count }} patients</span>
        </button>
    </div>
</template>
