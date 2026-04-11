<!-- resources/js/Components/Form/FormTextarea.vue -->
<script setup>
import { computed } from 'vue'
import { AlertCircle } from "lucide-vue-next"

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
    rows: {
        type: Number,
        default: 3
    }
})

const emit = defineEmits(['update:modelValue'])

const hasError = computed(() => !!props.error)

const updateValue = (event) => {
    emit('update:modelValue', event.target.value)
}
</script>

<template>
    <div class="space-y-1">
        <label :for="id" class="block text-xs font-semibold text-gray-600 capitalize tracking-wider">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>

        <textarea
            :id="id"
            :value="modelValue"
            @input="updateValue"
            :rows="rows"
            :disabled="disabled"
            :class="[
                'block w-full rounded-md shadow-sm text-sm',
                hasError
                    ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-primary/10 focus:border-primary transition-all',
                disabled ? 'bg-gray-100 cursor-not-allowed' : ''
            ]"
        />

        <p v-if="hasError" class="text-xs text-red-600 flex items-center mt-1">
            <AlertCircle class="h-3 w-3 mr-1" />
            {{ error }}
        </p>
    </div>
</template>
