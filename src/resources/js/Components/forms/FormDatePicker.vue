<!-- resources/js/Components/Form/FormDatePicker.vue -->
<script setup>
import { computed, ref } from 'vue'
import { Calendar, AlertCircle } from "lucide-vue-next"

const props = defineProps({
    id: {
        type: String,
        required: true
    },
    label: {
        type: String,
        default: ''
    },
    modelValue: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    min: {
        type: String,
        default: ''
    },
    max: {
        type: String,
        default: ''
    },
    showAge: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const hasError = computed(() => !!props.error)

const updateValue = (event) => {
    emit('update:modelValue', event.target.value)
}

const calculateAge = () => {
    if (!props.modelValue) return null
    const today = new Date()
    const birthDate = new Date(props.modelValue)
    let age = today.getFullYear() - birthDate.getFullYear()
    const m = today.getMonth() - birthDate.getMonth()
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--
    return age
}
</script>

<template>
    <div class="space-y-1">
        <label :for="id" class="block text-xs font-semibold text-gray-600  tracking-wider">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>

        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Calendar class="h-4 w-4 text-gray-400" />
            </div>

            <input
                :id="id"
                :value="modelValue"
                @input="updateValue"
                type="date"
                :min="min"
                :max="max"
                :disabled="disabled"
                :class="[
                    'block w-full rounded-md shadow-sm text-sm pl-10',
                    hasError
                        ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500'
                        : 'border-gray-300 focus:ring-green-600 focus:border-green-600',
                    disabled ? 'bg-gray-100 cursor-not-allowed' : ''
                ]"
            />
        </div>

        <!-- Age Display -->
        <div v-if="showAge && modelValue" class="text-xs text-gray-500 mt-1">
            Age: {{ calculateAge() }} years
        </div>

        <p v-if="hasError" class="text-xs text-red-600 flex items-center mt-1">
            <AlertCircle class="h-3 w-3 mr-1" />
            {{ error }}
        </p>

        <slot></slot>
    </div>
</template>
