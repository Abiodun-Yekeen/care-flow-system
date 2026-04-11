<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Quick Actions</h2>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <button class="action-btn">Open My Desk</button>
            <button class="action-btn">Receive & Register</button>
            <button class="action-btn">Temporary Files</button>
            <button class="action-btn">Track Files</button>
        </div>
    </div>
</template>

<style scoped>
.action-btn {
    @apply w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100;
}
</style>
