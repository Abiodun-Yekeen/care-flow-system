<script setup>
import Spinner from "./Spinner.vue"

defineProps({
    loading: Boolean,
    disabled: Boolean
})
</script>

<template>
    <button
        :disabled="loading || disabled"
        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 disabled:opacity-60"
    >
        <Spinner v-if="loading" size="small" class="mr-2" />
        <slot />
    </button>
</template>
