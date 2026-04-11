<script setup>
defineProps({
    size: {
        type: String,
        default: "medium"
    }
})

const sizes = {
    small: "w-4 h-4 border-2",
    medium: "w-6 h-6 border-2",
    large: "w-10 h-10 border-4"
}
</script>

<template>
    <div
        :class="[
      'animate-spin rounded-full border-gray-300 border-t-secondary',
      sizes[size]
    ]"
    />
</template>
