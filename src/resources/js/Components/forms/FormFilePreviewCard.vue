<script setup>
const props = defineProps({
    filePath: String,
    fileName: String
});

const emit = defineEmits(['preview']);

// Change the click behavior to notify the parent
const handlePreview = () => {
    emit('preview', `/storage/${props.filePath}`);
};
</script>

<template>
    <div
        @click="handlePreview"
        class="group flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer"
    >
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-900 truncate">{{ fileName || 'Documents' }}</p>
            <p class="text-[10px] text-blue-500 font-bold">CLICK TO VIEW</p>
        </div>
    </div>
</template>
