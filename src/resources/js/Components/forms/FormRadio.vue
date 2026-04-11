<!-- resources/js/Components/Form/FormRadio.vue -->
<script setup>
const props = defineProps({
    id: {
        type: String,
        required: true
    },
    name: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    modelValue: {
        type: [String, Number],
        default: ''
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
    }
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <div class="relative flex items-start">
        <div class="flex items-center h-5">
            <input
                :id="id"
                :name="name"
                :value="value"
                :checked="modelValue === value"
                @change="emit('update:modelValue', value)"
                type="radio"
                :disabled="disabled"
                class="h-4 w-4 border-gray-300 text-green-600 focus:ring-green-500"
            />
        </div>
        <div class="ml-3 text-sm">
            <label :for="id" class="text-xs font-semibold text-gray-700">{{ label }}</label>
            <p v-if="description" class="text-xs font-semibold text-gray-500">{{ description }}</p>
        </div>
    </div>
</template>
