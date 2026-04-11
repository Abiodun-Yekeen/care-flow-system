<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Department Workload</h2>

        <div class="space-y-3">
            <div v-for="(dept, i) in departments" :key="i">
                <div class="flex justify-between text-sm text-slate-600">
                    <span>{{ dept.name }}</span>
                    <span>{{ dept.count }}</span>
                </div>
                <div class="mt-1 h-2 rounded bg-slate-100">
                    <div
                        class="h-2 rounded bg-blue-600"
                        :style="{ width: dept.count * 3 + '%' }"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    departments: Array,
})
</script>
