<script setup>
import Spinner from "./Spinner.vue"

defineProps({
    loading: Boolean,
    disabled: Boolean,
    variant: {
        type: String,
        default: 'primary'
    }
})
</script>

<template>
    <button
        :disabled="loading || disabled"
        :class="[
            'inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md transition-colors disabled:opacity-60',
            variant === 'primary'
                ? 'bg-[#06A3DA] text-white hover:bg-[#091E3E]'
                : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50'
        ]"
    >
        <Spinner v-if="loading" size="small" class="mr-2" />
        <slot />
    </button>
</template>
