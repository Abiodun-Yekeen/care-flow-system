<script setup>
defineProps({ name: String })
</script>

<template>
  <span class="inline-flex w-4 justify-center text-gray-400">
    <component
      :is="icons[name] || icons.default"
      class="h-4 w-4"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
    />
  </span>
</template>

<script>
export const icons = {
  home: { template: '<path d="M3 12l9-9 9 9M4 10v10h5v-6h6v6h5V10" />' },
  users: { template: '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />' },
  heart: { template: '<path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.6l-1-1a5.5 5.5 0 00-7.8 7.8l8.8 8.6 8.8-8.6a5.5 5.5 0 000-7.8z" />' },
  default: { template: '<circle cx="12" cy="12" r="10" />' },
}
</script>
