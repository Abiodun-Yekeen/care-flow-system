<script setup>
import { ref, watch, onMounted } from 'vue'; // Added onMounted
import { XIcon, FileStack, ArrowRight, Loader2 } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    primaryFile: Object,
});

const emit = defineEmits(['close']);

const results = ref([]);
const isLoading = ref(false);

const performAutoSearch = async () => {
    if (!props.primaryFile?.id) return;
    isLoading.value = true;
const data = {
    query: '',
    exclude_id: props.primaryFile.id
}
    try {
        const response = await axios.post(route('files.search-merge-file'), data);
        results.value = response.data;
    } catch (err) {
        console.error("AXIOS ERROR:", err);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    if (props.show) {
        performAutoSearch();
    }
});

//  In case the component stays alive but the prop changes
watch(() => props.show, (newValue) => {
    console.log("WATCHER TRIGGERED, show is:", newValue);
    if (newValue) {
        performAutoSearch();
    }
}, { immediate: true });

const handleMerge = (targetFile) => {
    if (confirm(`Move all data from ${props.primaryFile.file_number} into ${targetFile.file_number}?`)) {
        router.post(route('files.merge'), {
            source_id: props.primaryFile.id,
            target_id: targetFile.id
        }, {
            onSuccess: () => emit('close'),
        });
    }
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @click.self="$emit('close')">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl transform transition-all overflow-hidden border border-slate-200">

                    <div class="p-6 border-b bg-slate-50 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-600 text-white rounded-lg shadow-md"><FileStack :size="20" /></div>
                            <div>
                                <h2 class="text-lg font-black text-slate-800 leading-none tracking-tight">Merge Selector</h2>
                                <p class="text-[10px] text-indigo-600 mt-1 uppercase font-bold tracking-widest">Source: {{ primaryFile?.file_number }}</p>
                            </div>
                        </div>
                        <button @click="$emit('close')" class="p-2 hover:bg-slate-200 text-slate-400 rounded-full transition-colors">
                            <XIcon :size="20" />
                        </button>
                    </div>

                    <div class="p-4 max-h-[400px] overflow-y-auto">
                        <div v-if="isLoading" class="py-12 flex flex-col items-center justify-center">
                            <Loader2 class="animate-spin text-indigo-500 mb-2" :size="32" />
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Searching for matches...</p>
                        </div>

                        <div v-else-if="results.length > 0" class="space-y-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase px-2 mb-2 tracking-widest">Target Suggestions</p>
                            <button
                                v-for="file in results"
                                :key="file.id"
                                @click="handleMerge(file)"
                                class="w-full flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:border-indigo-300 hover:bg-indigo-50 transition-all group text-left bg-white shadow-sm"
                            >
                                <div class="flex-1 pr-4">
                                    <span class="text-[10px] font-mono font-bold text-indigo-600 px-2 py-0.5 bg-indigo-50 rounded mb-1 inline-block">
                                        {{ file.file_number }}
                                    </span>
                                    <p class="text-sm font-bold text-slate-700 line-clamp-1 leading-tight">{{ file.subject }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1 uppercase">{{ file.source_name }}</p>
                                </div>
                                <ArrowRight :size="16" class="text-slate-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all" />
                            </button>
                        </div>

                        <div v-else class="text-center py-12 border-2 border-dashed border-slate-100 rounded-2xl">
                            <p class="text-sm text-slate-400 italic">No other files found to merge with.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t flex justify-end">
                        <button @click="$emit('close')" class="px-5 py-2 text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-slate-600">Cancel</button>
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>
</template>
