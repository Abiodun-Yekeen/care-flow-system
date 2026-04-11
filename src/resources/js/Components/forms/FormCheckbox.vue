<!-- resources/js/Components/Form/FormCheckbox.vue -->
<script setup>
import { computed } from 'vue'
import { Check } from "lucide-vue-next"

const props = defineProps({
    id: {
        type: String,
        default: null
    },
    modelValue: {
        type: [Boolean, Array],
        default: false
    },
    value: {
        type: [String, Number, Boolean],
        default: null
    },
    label: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    error: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const isChecked = computed({
    get: () => {
        if (Array.isArray(props.modelValue)) {
            return props.modelValue.includes(props.value)
        }
        return props.modelValue
    },
    set: (value) => {
        if (Array.isArray(props.modelValue)) {
            const newValue = value
                ? [...props.modelValue, props.value]
                : props.modelValue.filter(v => v !== props.value)
            emit('update:modelValue', newValue)
        } else {
            emit('update:modelValue', value)
        }
    }
})
</script>

<template>
    <div class="relative flex items-start">
        <div class="flex items-center h-5">
            <input
                :id="id || label"
                v-model="isChecked"
                type="checkbox"
                :disabled="disabled"
                :class="[
                    'h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500',
                    disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
                ]"
            />
        </div>
        <div class="ml-3 text-sm">
            <label :for="id || label"
                   :class="['font-medium', disabled ? 'text-gray-400' : 'text-gray-700']">
                {{ label }}
            </label>
            <p v-if="description" class="text-xs font-semibold text-gray-500">{{ description }}</p>
        </div>
    </div>
</template>
