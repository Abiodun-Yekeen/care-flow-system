<script setup>
import { ref, watch } from 'vue';
import { Head, router, Link } from "@inertiajs/vue3";
import { Search, Folder, FileText, Download, MoreVertical, Eye } from 'lucide-vue-next';
import debounce from 'lodash/debounce';
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import PageHeader from "@/Components/layout/PageHeader.vue";

const props = defineProps({
    documents: Object,
    filters: Object
});

const search = ref(props.filters.search || '');

watch(search, debounce((value) => {
    router.get(route('documents.index'), { search: value }, { preserveState: true, replace: true });
}, 500));
</script>

<template>
    <ContextModuleLayout>
        <Head title="Document Vault" />

        <div class="h-full flex flex-col bg-slate-50 overflow-hidden">
            <PageHeader title="Document Vault" subtitle="Repository of all uploaded correspondence" />

            <div class="px-8 py-4 bg-white border-b">
                <div class="relative max-w-2xl">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" :size="16" />
                    <input v-model="search" type="text" placeholder="Search documents..." 
                        class="w-full pl-10 pr-4 py-2 bg-slate-100 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    
                    <div v-for="doc in documents.data" :key="doc.id" 
                        class="group bg-white border border-slate-200 rounded-2xl p-4 hover:shadow-xl hover:border-blue-300 transition-all cursor-pointer relative">
                        
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-1 hover:bg-slate-100 rounded-md text-slate-400"><MoreVertical :size="16" /></button>
                        </div>

                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <Folder v-if="doc.mime === 'directory'" :size="24" />
                                <FileText v-else :size="24" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-black text-blue-900 uppercase tracking-tighter truncate">{{ doc.type }}</p>
                                <p class="text-sm font-bold text-slate-700 truncate" :title="doc.name">{{ doc.name }}</p>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-[10px] text-slate-400 font-medium uppercase tracking-widest">
                                <span>Size: {{ doc.size }}</span>
                                <span>{{ doc.date }}</span>
                            </div>
                            <div class="pt-2 border-t border-slate-50">
                                <p class="text-[10px] text-slate-500 font-bold">Uploaded By:</p>
                                <p class="text-[11px] text-slate-700 font-medium">{{ doc.uploader }} ({{ doc.department }})</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button class="flex items-center justify-center gap-2 py-2 bg-slate-50 text-slate-600 rounded-lg text-[10px] font-black uppercase hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                <Eye :size="12" /> Preview
                            </button>
                            <button class="flex items-center justify-center gap-2 py-2 bg-slate-50 text-slate-600 rounded-lg text-[10px] font-black uppercase hover:bg-green-50 hover:text-green-600 transition-colors">
                                <Download :size="12" /> Get File
                            </button>
                        </div>
                    </div>

                </div>

                <div v-if="documents.data.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-300">
                    <Folder :size="48" class="mb-2 opacity-20" />
                    <p class="text-xs font-bold uppercase tracking-widest">No documents found</p>
                </div>

                <div class="mt-12 flex justify-center">
                    <div class="flex gap-1 bg-white p-1 rounded-xl border shadow-sm">
                        <Component :is="link.url ? 'Link' : 'span'" v-for="(link, k) in documents.links" :key="k" :href="link.url" v-html="link.label"
                            :class="['px-4 py-2 text-[10px] font-black rounded-lg transition-all', link.active ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-50', !link.url ? 'opacity-20' : '']" />
                    </div>
                </div>
            </div>
        </div>
    </ContextModuleLayout>
</template>