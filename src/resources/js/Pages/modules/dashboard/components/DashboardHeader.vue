<script setup>

const props =defineProps({
        name: String,
        role: String
    }
)
const currentDate = new Date().toLocaleDateString('en-GB', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
});

</script>
<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h1 class="text-xl font-semibold text-slate-900">
            Welcome {{name}}
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            {{role}} • {{currentDate}}
        </p>
    </div>
</template>
