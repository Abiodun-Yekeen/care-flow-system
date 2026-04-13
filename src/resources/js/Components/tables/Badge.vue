<script setup>
defineProps({
    variant: { type: String, default: 'blue' } // 'blue', 'green', 'slate'
})
</script>

<template>
    <span :class="[
        'px-2 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wider inline-flex items-center gap-1',
        variant === 'blue' ? 'bg-blue-50 text-[#06A3DA] border-blue-100' : '',
        variant === 'green' ? 'bg-green-100 text-green-700 border-green-200' : '',
        variant === 'slate' ? 'bg-slate-100 text-slate-600 border-slate-200' : '',
    ]">
        <slot />
    </span>
</template>
