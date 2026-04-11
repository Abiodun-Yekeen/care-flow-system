<script setup>
import { ref, onBeforeUnmount } from 'vue';

const props = defineProps({
    modelValue: { type: Array, default: () => [] }, // Must be an array
    accept: { type: String, default: '.jpg,.jpeg' },
    maxFiles: { type: Number, default: 5 }
});

const emit = defineEmits(['update:modelValue']);

const isDragging = ref(false);
const fileInput = ref(null);
const filePreviews = ref([]); // Holds data for the visual list

const openFilePicker = () => fileInput.value.click();

// --- Core File Handling ---
const handleFiles = (newFiles) => {
    // Convert FileList to Array and filter by allowed type (basic client-side check)
    const validFiles = Array.from(newFiles).filter(file => {
        return props.accept.includes(file.name.split('.').pop().toLowerCase());
    });

    // Enforce max files
    const availableSlots = props.maxFiles - props.modelValue.length;
    const filesToAdd = validFiles.slice(0, availableSlots);

    if (filesToAdd.length === 0 && newFiles.length > 0) {
        alert(`You can only upload a maximum of ${props.maxFiles} files.`);
        return;
    }

    // Update the model (the actual Files for Inertia)
    const updatedModelValue = [...props.modelValue, ...filesToAdd];
    emit('update:modelValue', updatedModelValue);

    // Generate previews for the UI
    generatePreviews(filesToAdd);
};

// --- Preview Generation ---
const generatePreviews = (files) => {
    files.forEach(file => {
        const isImage = file.type.startsWith('image/');
        const previewItem = {
            id: crypto.randomUUID(),
            name: file.name,
            size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
            type: file.type,
            url: isImage ? URL.createObjectURL(file) : null // Create temporary URL for images
        };
        filePreviews.value.push(previewItem);
    });
};

const removeFile = (index) => {
    // 1. Revoke the object URL to prevent memory leaks
    if (filePreviews.value[index].url) {
        URL.revokeObjectURL(filePreviews.value[index].url);
    }

    // 2. Remove from visual previews
    filePreviews.value.splice(index, 1);

    // 3. Remove from actual model value and emit update
    const updatedModelValue = [...props.modelValue];
    updatedModelValue.splice(index, 1);
    emit('update:modelValue', updatedModelValue);
};

// --- Cleanup ---
// Vital: If the user leaves the page, clean up all generated object URLs
onBeforeUnmount(() => {
    filePreviews.value.forEach(file => {
        if (file.url) URL.revokeObjectURL(file.url);
    });
});

// --- Event Handlers ---
const handleFileChange = (e) => handleFiles(e.target.files);
const handleDrop = (e) => {
    isDragging.value = false;
    handleFiles(e.dataTransfer.files);
};
</script>

<template>
    <div class="space-y-4">
        <div
            @click="openFilePicker"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            :class="[
                'bg-white rounded-2xl border-2 border-dashed p-8 text-center transition-all group cursor-pointer',
                isDragging ? 'border-[#06A3DA] bg-blue-50 scale-[1.01]' : 'border-slate-300 hover:border-[#06A3DA]',
                modelValue.length >= maxFiles ? 'opacity-60 cursor-not-allowed' : ''
            ]"
        >
            <input
                type="file"
                ref="fileInput"
                class="hidden"
                :accept="accept"
                multiple
                :disabled="modelValue.length >= maxFiles"
                @change="handleFileChange"
            />

            <div class="mx-auto w-12 h-12 bg-blue-50 text-[#06A3DA] rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>

            <h3 class="text-sm font-bold text-[#091E3E]">
                Upload Scanned Documents (Multiple)
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Drag and drop JPG here, or click to browse.
                <span class="block mt-1 text-[#06A3DA] font-medium">Max: {{ maxFiles }} files</span>
            </p>
        </div>

        <div v-if="filePreviews.length > 0" class="space-y-3">
            <h4 class="text-xs font-bold text-[#091E3E] uppercase tracking-wider">Selected Files</h4>

            <div v-for="(file, index) in filePreviews" :key="file.id"
                 class="flex items-center gap-4 bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:border-slate-200 transition-colors">

                <div class="w-16 h-16 rounded-lg border border-slate-200 overflow-hidden flex-shrink-0 bg-slate-50 flex items-center justify-center">
                    <img v-if="file.url" :src="file.url" alt="Preview" class="w-full h-full object-cover" />
                    <div v-else class="text-red-500 text-center p-1">
                        <svg class="w-8 h-8 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-[10px] font-bold uppercase">PDF</span>
                    </div>
                </div>

                <div class="flex-grow min-w-0">
                    <p class="text-sm font-semibold text-[#091E3E] truncate" :title="file.name">{{ file.name }}</p>
                    <p class="text-xs text-slate-400">{{ file.size }}</p>
                </div>

                <button
                    type="button"
                    @click="removeFile(index)"
                    class="p-1.5 rounded-full text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
