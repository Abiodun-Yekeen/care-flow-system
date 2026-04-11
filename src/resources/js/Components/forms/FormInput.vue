

<script setup>
import { computed } from 'vue'
import { AlertCircle } from "lucide-vue-next"

const props = defineProps({
    id: String,
    label: String,
    modelValue: [String, Number],
    error: String, // Server-side error from Inertia
    triedNext: Boolean, // Parent toggle for "Next" button click
    required: Boolean,
    disabled: Boolean,
    readonly: Boolean,
    type: { type: String, default: 'text' },
    placeholder: String
})

const emit = defineEmits(['update:modelValue'])

// Determine if we should show a red border/message
const showError = computed(() => {
    // Show if there is a server error OR if user tried to proceed and required field is empty
    return !!props.error || (props.triedNext && props.required && !props.modelValue);
});

const errorMessage = computed(() => {
    if (props.error) return props.error;
    if (props.triedNext && props.required && !props.modelValue) {
        return `${props.label} is required to continue.`;
    }
    return '';
});
</script>

<template>
    <div class="space-y-1">
        <label :for="id" class="block text-xs font-semibold text-gray-600 capitalize tracking-wider">
        {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="relative group">
            <input
                :id="id"
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                @input="$emit('update:modelValue', $event.target.value)"
                class="w-full px-4 py-2.5  border rounded-xl text-sm transition-all outline-none"
                :class="[
                    showError
                        ? 'border-red-300 text-red-900 bg-red-50 focus:border-red-500'
                        : 'border-gray-300 focus:ring-secondary/10 focus:border-secondary transition-all',
                    disabled ? 'bg-gray-100 cursor-not-allowed' : '',
                    readonly ? 'bg-gray-50' : ''
                ]"
            />
            <div v-if="showError" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-red-500">
                <AlertCircle class="h-4 w-4" />
            </div>
        </div>

        <transition enter-active-class="transition duration-200 ease-out" enter-from-class="transform -translate-y-1 opacity-0" enter-to-class="transform translate-y-0 opacity-100">
            <p v-if="showError" class="text-[11px] font-medium text-red-600 flex items-center pl-1">
                {{ errorMessage }}
            </p>
        </transition>
    </div>
</template>
