
<script setup>
import { computed } from 'vue'
import { AlertCircle } from "lucide-vue-next"
import { ChevronDown } from 'lucide-vue-next'

const props = defineProps({
    id: String,
    label: String,
    modelValue: [String, Number],
    error: String,
    triedNext: Boolean,
    required: Boolean,
    options: {
        type: Array,
        default: () => [] // Expected format: [{ value: 'M', label: 'Male' }]
    },
    placeholder: { type: String, default: 'Select an option' }
})

const emit = defineEmits(['update:modelValue'])

const showError = computed(() => {
    return !!props.error || (props.triedNext && props.required && !props.modelValue);
});

const errorMessage = computed(() => {
    if (props.error) return props.error;
    if (props.triedNext && props.required && !props.modelValue) {
        return `Please select a ${props.label}.`;
    }
    return '';
});
</script>

<template>
    <div class="space-y-1">
<!--        <label :for="id" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">-->
        <label :for="id" class="block text-xs font-semibold text-gray-600 capitalize tracking-wider">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="relative">
            <select
                :id="id"
                :value="modelValue"
                @change="$emit('update:modelValue', Number($event.target.value))"
                class="w-full px-4 py-2.5  border rounded-xl text-sm transition-all outline-none appearance-none"
                :class="[
                    showError
                        ? 'border-red-300 text-red-900 bg-red-50 focus:border-red-500'
                        : 'border-gray-300 focus:ring-secondary/10 focus:border-secondary transition-all'
                ]"
            >
                <option value="" disabled>{{ placeholder }}</option>
                <option v-for="opt in options" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>

            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <AlertCircle v-if="showError" class="h-4 w-4 text-red-500" />
                <ChevronDown v-else class="h-4 w-4" />
            </div>
        </div>

        <p v-if="showError" class="text-[11px] font-medium text-red-600 pl-1 mt-1">
            {{ errorMessage }}
        </p>
    </div>
</template>
