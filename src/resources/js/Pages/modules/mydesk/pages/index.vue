<script setup>
import { ref, computed } from 'vue';
import {
    Search, Inbox, Send, FileText, Layers, FolderPlus,
    Info, Clock, ChevronRight, History, PenLine
} from 'lucide-vue-next';
import { Head, router , useForm} from "@inertiajs/vue3"; // Added router
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import PageHeader from "@/Components/layout/PageHeader.vue";
import Timeline from "@/Components/page/Timeline.vue";
import FormFilePreviewCard from "@/Components/forms/FormFilePreviewCard.vue";
import LoadButton from "@/Components/ui/LoadButton.vue";
import FormTextarea from "@/Components/forms/FormTextarea.vue";




const props = defineProps({
    files: Object,
    filters: Object
});

const form = useForm({
    'remark': '',
    'minute': ''

});

const selectedFile = ref(null);
const searchQuery = ref(props.filters?.search || '');
const rightTab = ref('action');

// Fix: Filter the files.data array instead of the props.files object
const filteredFiles = computed(() => {
    const data = props.files?.data || [];
    return data.filter(file => {
        return file.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            file.file_number.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

const uuid = selectedFile.uuid

// Reactivity for the modal
const previewUrl = ref(null);
const isModalOpen = ref(false);

const openModal = (url) => {
    previewUrl.value = url;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    previewUrl.value = null;
};

const isPast = (dateString) => {
    if (!dateString || dateString === 'No Deadline') return false;
    return new Date(dateString) < new Date();
};
// Helper for pagination
const changePage = (url) => {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true,
            only: ['files']
        });
    }
};

  const submit_treat_file = () => {
    // 1. Safety Check: Ensure a file is actually selected
    if (!selectedFile.value) {
        alert("Please select a file first");
        return;
    }

    form.clearErrors();

    // 2. Use selectedFile.value.id (or .uuid if that's what your route uses)
    form.put(route('files.treat', { file: selectedFile.value.uuid }), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: reset form or clear selection
            form.reset();
        },
    });
};

</script>

<template>
    <ContextModuleLayout>
        <Head title="My Desk - Triple View" />

        <div class="h-full flex flex-col bg-gray-50 overflow-hidden">
            <PageHeader title="My Desk" subtitle="Process incoming correspondence" />

            <div class="flex flex-1 overflow-hidden p-4 gap-4">

                <div class="w-80 flex flex-col bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="p-4 border-b bg-gray-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-tight">Inbox</h3>
                            <span class="bg-blue-100 text-blue-700 text-[10px] px-2 py-0.5 rounded-full font-bold">
                {{ props.files.total }} Files
            </span>
                        </div>
                        <div class="relative">
                            <Search class="absolute left-3 top-2.5 text-gray-400" :size="14" />
                            <input v-model="searchQuery" type="text" placeholder="Search..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 outline-none" />
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <div v-for="file in filteredFiles" :key="file.id"
                             @click="selectedFile = file"
                             :class="['p-4 border-b cursor-pointer transition-all relative group', selectedFile?.id === file.id ? 'bg-blue-50/50' : 'hover:bg-gray-50']">
                            <div v-if="selectedFile?.id === file.id" class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600"></div>
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-[9px] font-mono font-bold text-gray-400 uppercase">{{ file.file_number }}</span>
                                <Clock :size="10" class="text-gray-300" />
                            </div>
                            <h4 class="text-xs font-bold text-gray-800 line-clamp-2 leading-snug">{{ file.source_name }}</h4>
                            <p class="text-[10px] text-gray-500 truncate mt-1">{{ file.date_received }}</p>
                        </div>
                    </div>

                    <div v-if="props.files.links.length > 3" class="p-3 border-t bg-gray-50 flex justify-between items-center">
                        <button @click="changePage(props.files.prev_page_url)" :disabled="!props.files.prev_page_url" class="p-1 disabled:opacity-20">
                            <ChevronRight class="rotate-180" :size="16" />
                        </button>
                        <span class="text-[10px] font-bold text-gray-400">Page {{ props.files.current_page }} / {{ props.files.last_page }}</span>
                        <button @click="changePage(props.files.next_page_url)" :disabled="!props.files.next_page_url" class="p-1 disabled:opacity-20">
                            <ChevronRight :size="16" />
                        </button>
                    </div>
                </div>


                <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border overflow-hidden">
                    <template v-if="selectedFile">
                        <div class="p-4 border-b bg-gray-50/30">
                            <div class="flex justify-between items-start">
                                <h2 class="text-3xl font-black text-blue-900 tracking-tighter">
                                    {{ selectedFile.file_number }}
                                </h2>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-[10px] font-bold uppercase">
                                        {{ selectedFile.status }}
                                    </span>
                                    <span class="text-[11px] font-bold text-blue-600 uppercase">{{ selectedFile.priority }}</span>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ selectedFile.date_received }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto p-6">
                            <div class="bg-white rounded-xl p-2 shadow-sm">
                                <div class="flex items-center gap-2 mb-4 text-slate-800 font-bold">
                                    <FileText :size="18" class="text-slate-400" />
                                    File Details
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between border-b pb-1"><span class="text-slate-500">Subject:</span> <span class="font-medium">{{ selectedFile.subject }}</span></div>
                                    <div class="flex justify-between border-b pb-1"><span class="text-slate-500">Current Office:</span> <span class="font-medium">{{ selectedFile.department }}</span></div>
                                    <div class="flex justify-between border-b pb-1"><span class="text-slate-500">Current Holder:</span> <span class="font-medium">{{ selectedFile.current_holder }}</span></div>
                                    <div class="flex justify-between border-b pb-1"><span class="text-slate-500">Created By:</span> <span class="font-medium capitalize">{{ selectedFile.creator_name }}</span></div>
                                    <div class="flex justify-between pb-1">
                                        <span class="text-slate-500">Deadline:</span> 
                                        <span :class="['font-medium', isPast(selectedFile.deadline) ? 'text-red-600' : 'text-gray-700']">
                                            {{ selectedFile.deadline }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="py-4">
                                <button class="flex items-center gap-2 px-4 py-2 border-2 border-red-100 text-red-600 rounded-lg text-xs font-bold hover:bg-red-50 transition">
                                    <FileText :size="16" /> Export PDF
                                </button>
                            </div>

                            <div class="mt-4">
                                <div class="flex items-center justify-between mb-4 border-b pb-2">
                                    <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Attached Files</h3>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] px-2 py-0.5 rounded-full font-bold">
                                        {{ selectedFile.attachments?.length || 0 }}
                                    </span>
                                </div>
                                <div v-if="selectedFile.attachments?.length > 0" class="flex flex-col gap-3">
                                    <FormFilePreviewCard
                                        v-for="doc in selectedFile.attachments"
                                        :key="doc.id"
                                        :file-path="doc.file_path"
                                        :file-name="doc.original_name"
                                        @preview="openModal"
                                    />
                                    <p class="text-[10px] text-slate-400 italic mt-2 text-center">Click a card to view document</p>
                                </div>

                                <div v-else class="bg-gray-50 border-2 border-dashed border-gray-100 rounded-xl p-8 flex flex-col items-center justify-center">
                                    <Layers :size="32" class="text-gray-200 mb-2" />
                                    <p class="text-[11px] font-bold text-gray-400 uppercase">No attachments uploaded yet.</p>
                                </div>
                            </div>

                            <div class="mt-8">
                                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest border-b pb-2 mb-4">
                                    Timeline Summary
                                </h3>
                                <Timeline :movements="selectedFile.movements || []" />
                            </div>
                        </div>
                    </template>

                <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-300">
                    <Inbox :size="48" class="opacity-10 mb-2" />
                    <p class="text-xs italic font-medium">Select a file to begin processing</p>
                </div>
            </div>

              

                <div class="w-96 flex flex-col bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="flex border-b bg-gray-50/50">
                        <button @click="rightTab = 'action'" :class="['flex-1 py-4 text-[10px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 transition', rightTab === 'action' ? 'bg-white border-t-2 border-t-blue-600 text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600']">
                            <PenLine :size="14" /> Treat File
                        </button>
                        <button @click="rightTab = 'history'" :class="['flex-1 py-4 text-[10px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 transition', rightTab === 'history' ? 'bg-white border-t-2 border-t-blue-600 text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600']">
                            <History :size="14" /> Route History
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-5">
                        <template v-if="selectedFile">
                            <div v-if="rightTab === 'action'" class="space-y-4">

                                <button class="w-full flex items-center justify-between p-4 bg-indigo-50 border border-indigo-100 rounded-xl hover:bg-indigo-100 transition text-left group">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-indigo-600 text-white rounded-lg shadow-sm"><Layers :size="16" /></div>
                                        <div>
                                            <p class="text-xs font-bold text-indigo-900">Merge File</p>
                                            <p class="text-[9px] text-indigo-500">Attach to existing volume</p>
                                        </div>
                                    </div>
                                    <ChevronRight :size="14" class="text-indigo-300 group-hover:translate-x-1 transition-transform" />
                                </button>


                                <div class="mt-6 pt-6 border-t">
                                    
                                <div class="col-span-12 md:col-span-6">
                                    <FormTextarea
                                        id="minute"
                                        v-model="form.minute"
                                        label="Minute"
                                        :error="form.errors.minute"
                                        required
                                        :rows="2"
                                    />
                                </div>
                                 <div class="col-span-12 md:col-span-6">
                                    <FormTextarea
                                        id="remark"
                                        v-model="form.remark"
                                        label="Note"
                                        :error="form.errors.remark"
                                        :rows="2"
                                    />
                                </div>
                                </div>
                            </div>

                            <div class="mt-2 justify-center">
                        <LoadButton
                            type="button"
                            :loading="form.processing "
                            :disabled="form.processing"
                            @click="submit_treat_file">
                            {{ form.processing  ? 'Submitting...' : 'Treated' }}
                        </LoadButton>
                    </div>
                        </template>
                        <div v-else class="h-full flex items-center justify-center text-gray-300 text-xs italic">
                            No file selected
                        </div>
                    </div>
                </div>

            </div>
        </div>

         <Teleport to="body">
                    <div v-if="isModalOpen"
                         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-sm"
                         @click.self="closeModal"
                    >
                        <button @click="closeModal" class="absolute top-6 right-6 text-white hover:bg-white/20 p-2 rounded-full transition-all">
                            <XIcon class="w-8 h-8" />
                        </button>

                        <div class="max-w-5xl w-full h-[90vh] flex items-center justify-center">
                            <img v-if="previewUrl.match(/\.(jpg|jpeg|png|webp)$/i)"
                                 :src="previewUrl"
                                 class="max-w-full max-h-full object-contain rounded-lg shadow-2xl shadow-black"
                            />

                            <iframe v-else-if="previewUrl.endsWith('.pdf')"
                                    :src="previewUrl"
                                    class="w-full h-full rounded-lg border-0"
                            ></iframe>

                            <div v-else class="text-white text-center">
                                <p>Preview not available for this file type.</p>
                                <a :href="previewUrl" download class="mt-4 inline-block bg-blue-600 px-4 py-2 rounded">Download Instead</a>
                            </div>
                        </div>
                    </div>
                </Teleport>
                
    </ContextModuleLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
</style>
