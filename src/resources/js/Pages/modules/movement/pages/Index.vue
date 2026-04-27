<script setup>
import { ref, watch } from 'vue';
import { Head, router } from "@inertiajs/vue3";
import { Search, History, Inbox } from 'lucide-vue-next';
import debounce from 'lodash/debounce';
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import PageHeader from "@/Components/layout/PageHeader.vue";
import Timeline from "@/Components/page/Timeline.vue";

const props = defineProps({
    movements: Object, // Paginated object from Controller
    filters: Object
});

const search = ref(props.filters.search || '');

// Search logic - hits the controller and refreshes props.movements
watch(search, debounce((value) => {
    router.get(route('files.index'), { search: value }, {
        preserveState: true,
        replace: true
    });
}, 500));
</script>

<template>
    <ContextModuleLayout>
        <Head title="File Movement History" />

        <div class="h-full flex flex-col bg-gray-50 overflow-hidden">
            <PageHeader title="File Movement History" subtitle="Full audit trail of all correspondence" />

            <div class="px-8 py-4 bg-white border-b shadow-sm">
                <div class="max-w-4xl mx-auto relative">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                    <input 
                        v-model="search"
                        type="text" 
                        placeholder="Search by File Number, Subject, or Minutes..." 
                        class="w-full pl-12 pr-4 py-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all"
                    />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-8">
                <div class="max-w-4xl mx-auto">
                    
                    <div v-if="movements.data.length > 0">
                        <Timeline :movements="movements.data" />
                        
                        <div class="mt-12 mb-8 flex justify-center">
                            <div class="flex items-center gap-1 bg-white p-2 rounded-xl shadow-sm border">
                                <Component 
                                    :is="link.url ? 'Link' : 'span'"
                                    v-for="(link, k) in movements.links"
                                    :key="k"
                                    :href="link.url"
                                    v-html="link.label"
                                    :class="[
                                        'px-4 py-2 text-xs rounded-lg font-bold transition-all',
                                        link.active ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50',
                                        !link.url ? 'opacity-30 cursor-not-allowed' : ''
                                    ]"
                                />
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex flex-col items-center justify-center py-20 text-slate-300">
                        <Inbox :size="64" class="mb-4 opacity-20" />
                        <p class="text-sm font-black uppercase tracking-widest">No movements found</p>
                        <p class="text-xs mt-2">Try adjusting your search criteria</p>
                    </div>

                </div>
            </div>
        </div>
    </ContextModuleLayout>
</template>