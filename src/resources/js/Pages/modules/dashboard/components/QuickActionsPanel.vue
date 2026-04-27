<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Quick Actions</h2>

       <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
    <Link 
        :href="route('files.index')" 
        as="button" 
        class="action-btn text-center"
    >
        My Desk
    </Link>

    <Link 
        :href="route('register.index')" 
        as="button" 
        class="action-btn text-center"
    >
        Receive & Register
    </Link>

    <Link 
        :href="route('temp.file')" 
        as="button" 
        class="action-btn text-center"
    >
        Temporary Files
    </Link>

    <Link 
        :href="route('mydesk.history')" 
        as="button" 
        class="action-btn text-center"
    >
        Track Files
    </Link>
</div>
    </div>
</template>

<style scoped>
.action-btn {
    @apply w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-100;
}
</style>
