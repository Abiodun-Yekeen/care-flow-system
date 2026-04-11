<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Recent Activity</h2>

        <ul class="space-y-4">
            <li v-for="(item, i) in items" :key="i" class="text-sm text-slate-600">
                <p>{{ item.text }}</p>
                <span class="text-xs text-slate-400">{{ item.time }}</span>
            </li>
        </ul>
    </div>
</template>

<script setup>
defineProps({
    items: Array,
})
</script>
