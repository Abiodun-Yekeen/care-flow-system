


<script setup>
import { ref, computed, watch } from 'vue'
import {Head, Link, router, usePage} from '@inertiajs/vue3'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import PageHeader from "@/Components/layout/PageHeader.vue"
import ActionBar from "@/Components/forms/ActionBar.vue"
import { Building2, Mail, ShieldCheck, Fingerprint } from "lucide-vue-next"
import FormSearchFilter from "@/Components/forms/FormSearchFilter.vue"
import debounce from 'lodash/debounce'
import { RegistryActions } from "@/Pages/modules/registry/Composables/RegistryActions.js"
import { formatDistanceToNow, isPast } from 'date-fns';

const props = defineProps({
    temp_files: Object,
    filters: Object
})

// --- SELECTION LOGIC ---
const selectedIds = ref([])

// Toggle selection function (This was missing!)
const toggleSelection = (uuid) => {
    console.log("Clicked UUID:", uuid);
    const index = selectedIds.value.indexOf(uuid);
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(uuid);
    }
}

const selectedItem = computed(() => {
    if (selectedIds.value.length !== 1) return null;
    // Find by uuid
    return props.temp_files.data.find(u => u.uuid === selectedIds.value[0]);
})

const selectedReceiveUuid = computed(() => {
    if (!selectedItem.value) return null;
    const latestReceive = selectedItem.value.file_receive?.[0];
    return latestReceive ? latestReceive.uuid : null;
});

const getDeadlineStatus = (dateString) => {
    const date = new Date(dateString);
    if (isPast(date)) return 'Overdue';

    // Returns "in 4 hours" or "in 20 minutes"
    return formatDistanceToNow(date, { addSuffix: true });
}


// --- SEARCH LOGIC ---
const search = ref(props.filters?.search || '')

const handleSearch = () => {
    router.get(route('temp.file'), {
        search: search.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    })
}

// Function to clean up Laravel pagination labels
const cleanLabel = (label) => {
    if (label.includes('Previous')) return 'Prev';
    if (label.includes('Next')) return 'Next';
    return label;
};

watch(search, debounce((value) => {
    handleSearch()
}, 500))
</script>



<template>
    <ContextModuleLayout>
                <Head title="Temporary Files" />
                <PageHeader title="Temporary Files" subtitle="System Registry Directory" />

                <ActionBar :actions="RegistryActions(selectedReceiveUuid)" />

                <div class="space-y-6 mt-6">
                    <FormSearchFilter
                        v-model="search"
                        placeholder="Search directory..."
                    />

    <div v-if="temp_files.data.length > 0"
         class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-x-2 gap-y-8"
    >
        <div
            v-for="file in temp_files.data"
            :key="file.id"
            @click="toggleSelection(file.uuid)"
            class="group relative flex flex-col items-center p-2 rounded-lg cursor-pointer transition-all select-none"
            :class="selectedIds.includes(file.uuid) ? 'bg-primary/10 shadow-sm ring-1 ring-blue-200' : 'hover:bg-slate-50'"

        >

            <div class="absolute z-10 bottom-full mb-2 left-1/2 -translate-x-1/2 w-auto min-w-[14rem] max-w-xs whitespace-nowrap p-3
            bg-slate-900 text-white text-[12px] rounded-lg shadow-2xl opacity-0 group-hover:opacity-100
            pointer-events-none transition-opacity duration-200 border border-slate-700">
                <p class="font-bold border-b border-slate-700 pb-1 mb-1 text-blue-400">File Details</p>
                <p><span class="text-slate-400">Ref:</span> {{ file.source_reference_no }}</p>
                <p><span class="text-slate-400">Subject:</span> {{ file.subject || 'N/A' }}</p>
                <p><span class="text-slate-400">Status:</span> {{ file.status || 'N/A' }}</p>
                <p><span class="text-slate-400">Priority:</span> {{ file.priority || 'N/A' }}</p>
                <p><span class="text-slate-400">Receive department:</span> {{ file.current_department?.name || 'N/A' }}</p>
                <p><span class="text-slate-400">Current holder:</span> {{ file.current_holder?.name || 'Unassigned' }}</p>
                <p><span class="text-slate-400">Created by:</span> {{ file.creator?.name || 'System' }}</p>
                <p><span class="text-slate-400">Deadline:</span> {{ getDeadlineStatus(file.file_receive[0]?.deadline_at) }}</p>
                <p><span class="text-slate-400">Received date:</span> {{ file.file_receive[0].date_received}}</p>
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-900"></div>
            </div>

            <div class="relative mb-3">
                <svg class="size-16 transition-all duration-200 group-hover:scale-105" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 7V5C7 4.44772 7.44772 4 8 4H16C16.5523 4 17 4.44772 17 5V7" stroke="currentColor" stroke-width="1.5" :class="selectedIds.includes(file.uuid) ? 'text-blue-300' : 'text-slate-300'"/>
                    <path d="M2 9C2 7.89543 2.89543 7 4 7H10L12 9H20C21.1046 9 22 9.89543 22 11V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V9Z" fill="currentColor" :class="selectedIds.includes(file.uuid) ? 'text-blue-500' : 'text-amber-400'"/>
                    <path d="M2 12C2 10.8954 2.89543 10 4 10H20C21.1046 10 22 10.8954 22 12V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V12Z" fill="currentColor" fill-opacity="0.3" :class="selectedIds.includes(file.uuid) ? 'text-white' : 'text-amber-200'"/>
                </svg>
                <div v-if="selectedIds.includes(file.uuid)" class="absolute -top-1 -right-1 bg-blue-600 text-white rounded-full p-0.5 border-2 border-white shadow-sm">
                    <ShieldCheck class="size-3" />
                </div>
            </div>

            <div class="text-center w-full px-1">
                <p class="text-[12px] font-bold leading-tight truncate mb-0.5" :class="selectedIds.includes(file.uuid) ? 'text-blue-900' : 'text-slate-800'">
                    {{ file.temp_file_number }}
                </p>
                <p class="text-[10px] text-slate-500 truncate w-full uppercase font-medium">
                    {{ file.source_name }}
                </p>
            </div>

        </div>
    </div>
                    <div v-else class="py-20 text-center bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                                        <p class="text-slate-500">No temporary files found </p>
                    </div>

                    <div v-if="temp_files.links && temp_files.links.length > 3" class="mt-10 flex items-center justify-between border-t border-slate-100 pt-6">
                        <div class="text-[11px] text-slate-500 font-medium">
                            Showing <span class="text-slate-900 font-bold">{{ temp_files.from }}</span> to <span class="text-slate-900 font-bold">{{ temp_files.to }}</span> of <span class="text-slate-900 font-bold">{{ temp_files.total }}</span>
                        </div>

                        <nav class="flex items-center gap-1">
                            <template v-for="(link, k) in temp_files.links" :key="k">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1.5 text-[11px] font-bold rounded transition-all border"
                                    :class="link.active
                    ? 'bg-slate-900 text-white border-slate-900'
                    : 'bg-white text-slate-600 border-slate-200 hover:border-slate-400'"
                                    v-html="cleanLabel(link.label)"
                                    preserve-scroll
                                />
                                <span
                                    v-else
                                    class="px-3 py-1.5 text-[11px] text-slate-300 border border-slate-100 rounded cursor-not-allowed"
                                    v-html="cleanLabel(link.label)"
                                ></span>
                            </template>
                        </nav>
                    </div>

                </div>

    </ContextModuleLayout>
</template>

<!--<template>-->
<!--    <ContextModuleLayout>-->
<!--        <Head title="Temporary Files" />-->
<!--        <PageHeader title="Temporary Files" subtitle="System Registry Directory" />-->

<!--        <ActionBar :actions="RegistryActions(selectedUser)" />-->

<!--        <div class="space-y-6 mt-6">-->
<!--            <FormSearchFilter-->
<!--                v-model="search"-->
<!--                placeholder="Search directory..."-->
<!--            />-->

<!--            <div-->
<!--                v-if="temp_files.data.length > 0"-->
<!--                class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-x-2 gap-y-8"-->
<!--            >-->
<!--                <div-->
<!--                    v-for="file in temp_files.data"-->
<!--                    :key="file.id"-->
<!--                    @click="toggleSelection(file.id)"-->
<!--                    class="group flex flex-col items-center p-2 rounded-lg cursor-pointer transition-all select-none border border-transparent"-->
<!--                    :class="selectedIds.includes(file.id) ? 'bg-blue-100/60 shadow-sm border-blue-200' : 'hover:bg-slate-50'"-->
<!--                >-->
<!--                    <div class="relative mb-3">-->
<!--                        <svg class="size-16 transition-all duration-200 group-hover:scale-105" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                            <path d="M7 7V5C7 4.44772 7.44772 4 8 4H16C16.5523 4 17 4.44772 17 5V7" stroke="currentColor" stroke-width="1.5" :class="selectedIds.includes(file.id) ? 'text-blue-300' : 'text-slate-300'"/>-->
<!--                            <path d="M2 9C2 7.89543 2.89543 7 4 7H10L12 9H20C21.1046 9 22 9.89543 22 11V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V9Z" fill="currentColor" :class="selectedIds.includes(file.id) ? 'text-blue-500' : 'text-amber-400'"/>-->
<!--                            <path d="M2 12C2 10.8954 2.89543 10 4 10H20C21.1046 10 22 10.8954 22 12V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V12Z" fill="currentColor" fill-opacity="0.3" :class="selectedIds.includes(file.id) ? 'text-white' : 'text-amber-200'"/>-->
<!--                        </svg>-->

<!--                        <div v-if="selectedIds.includes(file.id)" class="absolute -top-1 -right-1 bg-blue-600 text-white rounded-full p-0.5 border-2 border-white shadow-sm">-->
<!--                            <ShieldCheck class="size-3" />-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="text-center w-full px-1">-->
<!--                        <p class="text-[12px] font-bold leading-tight truncate mb-0.5" :class="selectedIds.includes(file.id) ? 'text-blue-900' : 'text-slate-800'">-->
<!--                            {{ file.temp_file_number }}-->
<!--                        </p>-->
<!--                        <p class="text-[10px] text-slate-500 truncate w-full uppercase">-->
<!--                            {{ file.source_name }}-->
<!--                        </p>-->
<!--                    </div>-->

<!--                    <div v-if="file.priority === 'immediate'" class="mt-2 flex gap-0.5">-->
<!--                        <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div v-else class="flex flex-col items-center justify-center py-20 text-slate-400">-->
<!--                <p>No folders found.</p>-->
<!--            </div>-->


<!--            <div v-if="temp_files.links && temp_files.links.length > 3" class="mt-10 flex items-center justify-between border-t border-slate-100 pt-6">-->
<!--                <div class="text-xs text-slate-500 font-medium">-->
<!--                    Showing <span class="text-slate-900">{{ temp_files.from }}</span> to <span class="text-slate-900">{{ temp_files.to }}</span> of <span class="text-slate-900">{{ temp_files.total }}</span> folders-->
<!--                </div>-->

<!--                <nav class="flex items-center gap-1">-->
<!--                    <template v-for="(link, k) in temp_files.links" :key="k">-->
<!--                        <div v-if="link.url === null"-->
<!--                             class="px-3 py-2 text-xs text-slate-400 cursor-not-allowed"-->
<!--                             v-html="link.label"-->
<!--                        />-->

<!--                        <Link v-else-->
<!--                              :href="link.url"-->
<!--                              class="px-3 py-2 text-xs font-bold rounded-md transition-all"-->
<!--                              :class="link.active-->
<!--                      ? 'bg-primary-600 text-white shadow-md shadow-primary-200'-->
<!--                      : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"-->
<!--                              v-html="link.label"-->
<!--                              preserve-scroll-->
<!--                        />-->
<!--                    </template>-->
<!--                </nav>-->
<!--            </div>-->
<!--        </div>-->
<!--    </ContextModuleLayout>-->
<!--</template>-->

<!--<template>-->
<!--    <ContextModuleLayout>-->
<!--        <Head title="Temporary Files" />-->
<!--        <PageHeader title="Temporary Files" subtitle="System Registry Directory" />-->

<!--        <ActionBar :actions="RegistryActions(selectedUser)" />-->

<!--        <div class="space-y-6 mt-6">-->
<!--            <FormSearchFilter-->
<!--                v-model="search"-->
<!--                placeholder="Search directory..."-->
<!--            />-->



<!--            <div v-if="temp_files.data.length > 0"-->
<!--                 class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-x-2 gap-y-6"-->
<!--            >-->
<!--                <div-->
<!--                    v-for="file in temp_files.data"-->
<!--                    :key="file.id"-->
<!--                    @click="toggleSelection(file.id)"-->
<!--                    class="group flex flex-col items-center p-2 rounded-lg cursor-pointer transition-all select-none border border-transparent"-->
<!--                    :class="selectedIds.includes(file.id) ? 'bg-blue-100/60 shadow-sm border-blue-200' : 'hover:bg-slate-50'"-->
<!--                >-->
<!--                    <div class="relative mb-3">-->
<!--                        <svg-->
<!--                            class="size-16 transition-all duration-200 group-hover:scale-105"-->
<!--                            viewBox="0 0 24 24"-->
<!--                            fill="none"-->
<!--                            xmlns="http://www.w3.org/2000/svg"-->
<!--                        >-->
<!--                            <path d="M7 7V5C7 4.44772 7.44772 4 8 4H16C16.5523 4 17 4.44772 17 5V7" stroke="currentColor" stroke-width="1.5" :class="selectedIds.includes(file.id) ? 'text-blue-300' : 'text-slate-300'"/>-->
<!--                            <path d="M2 9C2 7.89543 2.89543 7 4 7H10L12 9H20C21.1046 9 22 9.89543 22 11V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V9Z" fill="currentColor" :class="selectedIds.includes(file.id) ? 'text-blue-500' : 'text-amber-400'"/>-->
<!--                            <path d="M2 12C2 10.8954 2.89543 10 4 10H20C21.1046 10 22 10.8954 22 12V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V12Z" fill="currentColor" fill-opacity="0.3" :class="selectedIds.includes(file.id) ? 'text-white' : 'text-amber-200'"/>-->
<!--                        </svg>-->

<!--                        <div v-if="selectedIds.includes(file.id)" class="absolute -top-1 -right-1 bg-blue-600 text-white rounded-full p-0.5 border-2 border-white shadow-sm">-->
<!--                            <ShieldCheck class="size-3" />-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="text-center w-full px-1">-->
<!--                        <p class="text-[12px] font-bold leading-tight truncate mb-0.5" :class="selectedIds.includes(file.id) ? 'text-blue-900' : 'text-slate-800'">-->
<!--                            {{ file.temp_file_number }}-->
<!--                        </p>-->
<!--                        <p class="text-[10px] text-slate-500 truncate w-full">-->
<!--                            {{ file.source_name }}-->
<!--                        </p>-->
<!--                    </div>-->

<!--                    <div v-if="file.priority === 'immediate'" class="mt-2 flex gap-0.5">-->
<!--                        <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>-->
<!--                        <span class="h-1.5 w-1.5 rounded-full bg-red-500/40"></span>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

<!--&lt;!&ndash;            <div&ndash;&gt;-->
<!--&lt;!&ndash;                v-if="temp_files.data.length > 0"&ndash;&gt;-->
<!--&lt;!&ndash;                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2"&ndash;&gt;-->
<!--&lt;!&ndash;            >&ndash;&gt;-->
<!--&lt;!&ndash;                <div&ndash;&gt;-->
<!--&lt;!&ndash;                    v-for="file in temp_files.data"&ndash;&gt;-->
<!--&lt;!&ndash;                    :key="file.id"&ndash;&gt;-->
<!--&lt;!&ndash;                    @click="toggleSelection(file.id)"&ndash;&gt;-->
<!--&lt;!&ndash;                    class="group flex flex-col items-center p-3 rounded-md cursor-pointer transition-all select-none border border-transparent"&ndash;&gt;-->
<!--&lt;!&ndash;                    :class="selectedIds.includes(file.id)&ndash;&gt;-->
<!--&lt;!&ndash;                        ? 'bg-blue-100/50 border-blue-200'&ndash;&gt;-->
<!--&lt;!&ndash;                        : 'hover:bg-slate-100'"&ndash;&gt;-->
<!--&lt;!&ndash;                >&ndash;&gt;-->
<!--&lt;!&ndash;                    <div class="relative mb-2">&ndash;&gt;-->
<!--&lt;!&ndash;                        <svg&ndash;&gt;-->
<!--&lt;!&ndash;                            class="size-14 transition-transform group-active:scale-95"&ndash;&gt;-->
<!--&lt;!&ndash;                            :class="selectedIds.includes(file.id) ? 'text-blue-500' : 'text-amber-400'"&ndash;&gt;-->
<!--&lt;!&ndash;                            viewBox="0 0 24 24"&ndash;&gt;-->
<!--&lt;!&ndash;                            fill="currentColor"&ndash;&gt;-->
<!--&lt;!&ndash;                        >&ndash;&gt;-->
<!--&lt;!&ndash;                            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" />&ndash;&gt;-->
<!--&lt;!&ndash;                        </svg>&ndash;&gt;-->

<!--&lt;!&ndash;                        <div&ndash;&gt;-->
<!--&lt;!&ndash;                            v-if="selectedIds.includes(file.id)"&ndash;&gt;-->
<!--&lt;!&ndash;                            class="absolute -top-1 -right-1 bg-blue-600 text-white rounded-full p-0.5 border-2 border-white"&ndash;&gt;-->
<!--&lt;!&ndash;                        >&ndash;&gt;-->
<!--&lt;!&ndash;                            <ShieldCheck class="size-3" />&ndash;&gt;-->
<!--&lt;!&ndash;                        </div>&ndash;&gt;-->
<!--&lt;!&ndash;                    </div>&ndash;&gt;-->

<!--&lt;!&ndash;                    <div class="text-center w-full">&ndash;&gt;-->
<!--&lt;!&ndash;                        <p&ndash;&gt;-->
<!--&lt;!&ndash;                            class="text-[13px] font-medium leading-tight break-all line-clamp-2"&ndash;&gt;-->
<!--&lt;!&ndash;                            :class="selectedIds.includes(file.id) ? 'text-blue-900' : 'text-slate-700'"&ndash;&gt;-->
<!--&lt;!&ndash;                        >&ndash;&gt;-->
<!--&lt;!&ndash;                            {{ file.temp_file_number }}&ndash;&gt;-->
<!--&lt;!&ndash;                        </p>&ndash;&gt;-->

<!--&lt;!&ndash;                        <p class="text-[10px] text-slate-400 mt-0.5 truncate uppercase">&ndash;&gt;-->
<!--&lt;!&ndash;                            {{ file.source_name }}&ndash;&gt;-->
<!--&lt;!&ndash;                        </p>&ndash;&gt;-->
<!--&lt;!&ndash;                    </div>&ndash;&gt;-->

<!--&lt;!&ndash;                    <div&ndash;&gt;-->
<!--&lt;!&ndash;                        v-if="file.priority === 'immediate'"&ndash;&gt;-->
<!--&lt;!&ndash;                        class="mt-1 h-1 w-4 bg-red-500 rounded-full"&ndash;&gt;-->
<!--&lt;!&ndash;                    ></div>&ndash;&gt;-->
<!--&lt;!&ndash;                </div>&ndash;&gt;-->
<!--&lt;!&ndash;            </div>&ndash;&gt;-->

<!--            <div v-else class="flex flex-col items-center justify-center py-20">-->
<!--                <svg class="size-20 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
<!--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />-->
<!--                </svg>-->
<!--                <p class="text-slate-400 text-sm">No folders found in this directory.</p>-->
<!--            </div>-->
<!--        </div>-->
<!--    </ContextModuleLayout>-->
<!--</template>-->


<!--<template>-->
<!--    <ContextModuleLayout>-->
<!--        <Head title="Temporary Files" />-->
<!--        <PageHeader title="Temporary Files" subtitle="Manage incoming files and digitizing queue." />-->

<!--        <ActionBar :actions="RegistryActions(selectedUser)" />-->

<!--        <div class="space-y-6 mt-6">-->
<!--            <FormSearchFilter-->
<!--                v-model="search"-->
<!--                placeholder="Search by file number, source or reference..."-->
<!--            />-->

<!--            <div v-if="selectedIds.length > 0" class="bg-primary-50 border border-primary-100 p-2 rounded-lg flex justify-between items-center px-4">-->
<!--                <span class="text-xs font-semibold text-primary-700">{{ selectedIds.length }} files selected</span>-->
<!--                <button @click="selectedIds = []" class="text-xs text-primary-600 hover:underline">Clear selection</button>-->
<!--            </div>-->

<!--            <div v-if="temp_files.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">-->
<!--                <div-->
<!--                    v-for="file in temp_files.data"-->
<!--                    :key="file.id"-->
<!--                    @click="toggleSelection(file.id)"-->
<!--                    class="relative group bg-white border rounded-xl p-4 transition-all cursor-pointer hover:border-primary-400 hover:shadow-md"-->
<!--                    :class="selectedIds.includes(file.id) ? 'border-primary-500 ring-1 ring-primary-500 bg-primary-50/20' : 'border-slate-200'"-->
<!--                >-->
<!--                    <div class="absolute top-3 right-3">-->
<!--                        <input-->
<!--                            type="checkbox"-->
<!--                            :checked="selectedIds.includes(file.id)"-->
<!--                            class="rounded border-slate-300 text-primary-600 focus:ring-primary-500 h-4 w-4"-->
<!--                        />-->
<!--                    </div>-->

<!--                    <div class="flex items-start gap-4">-->
<!--                        <div class="p-3 rounded-lg bg-slate-100 text-slate-500 group-hover:bg-primary-100 group-hover:text-primary-600 transition-colors">-->
<!--                            <Building2 v-if="file.source_name.includes('Hospital')" class="size-6" />-->
<!--                            <Mail v-else class="size-6" /> </div>-->

<!--                        <div class="flex-1 min-w-0">-->
<!--                            <h4 class="text-sm font-bold text-slate-900 truncate uppercase tracking-tight">-->
<!--                                {{ file.temp_file_number }}-->
<!--                            </h4>-->

<!--                            <p class="text-xs text-slate-500 font-medium truncate mb-2">-->
<!--                                {{ file.source_name }}-->
<!--                            </p>-->

<!--                            <div class="space-y-1.5 mt-3">-->
<!--                                <div class="flex items-center gap-2 text-[11px] text-slate-600">-->
<!--                                    <Fingerprint class="size-3.5 text-slate-400" />-->
<!--                                    <span class="font-mono">Ref: {{ file.source_reference_no }}</span>-->
<!--                                </div>-->
<!--                                <div class="flex items-center gap-2 text-[11px] text-slate-600">-->
<!--                                    <ShieldCheck class="size-3.5 text-slate-400" />-->
<!--                                    <span>Dept: {{ file.department?.name || 'Unassigned' }}</span>-->
<!--                                </div>-->
<!--                            </div>-->

<!--                            <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-3">-->
<!--                                <Badge variant="green" class="text-[9px]">Active</Badge>-->
<!--                                <span class="text-[10px] text-slate-400 italic">-->
<!--                                    {{ new Date(file.created_at).toLocaleDateString() }}-->
<!--                                </span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div v-else class="py-20 text-center bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">-->
<!--                <p class="text-slate-500">No temporary files found matching your search.</p>-->
<!--            </div>-->

<!--            <div class="mt-6 flex justify-center">-->
<!--            </div>-->
<!--        </div>-->
<!--    </ContextModuleLayout>-->
<!--</template>-->







<!--<template>-->
<!--    <ContextModuleLayout>-->
<!--        <Head title="User Management" />-->
<!--        <PageHeader title="Temporary File " subtitle="Manage system access and roles." />-->

<!--        <ActionBar :actions="RegistryActions(selectedUser)" />-->

<!--        <div class="space-y-6 mt-6">-->
<!--            <FormSearchFilter-->
<!--                v-model="search"-->
<!--                @keyup.enter="handleSearch"-->
<!--                placeholder="Search name, mobile no or staf id..."-->
<!--            />-->

<!--            <DataTable-->
<!--                v-model="selectedIds"-->
<!--                :items="temp_files.data"-->
<!--                :columns="tableColumns"-->
<!--                :pagination="temp_files"-->
<!--            >-->
<!--                <template #body>-->
<!--                    <DataTableRow-->
<!--                        v-for="file in temp_files.data"-->
<!--                        :key="temp_files.id"-->
<!--                        :item-id="temp_files.id"-->
<!--                        v-model="selectedIds"-->
<!--                    >-->
<!--                        <td class="px-6 py-4">-->
<!--                            <UserAvatarCell :name="temp_files.name"  />-->
<!--                        </td>-->

<!--                        <td class="px-6 py-4 text-xs text-slate-600">-->
<!--                            <span class="flex items-center gap-1"> {{ file.source_name }}</span>-->
<!--                        </td>-->

<!--                        <td class="px-6 py-4">-->
<!--                        <span class=" items-center gap-1">-->
<!--                            {{ file.source_reference_no }}-->
<!--                        </span>-->
<!--                        </td>-->

<!--                        <td class="px-6 py-4 text-sm text-slate-700">-->
<!--                         {{ file.temp_file_number }}-->
<!--                        </td>-->



<!--                        <td class="px-6 py-4">-->
<!--                            <Badge variant="green">Active</Badge>-->
<!--                        </td>-->
<!--                    </DataTableRow>-->
<!--                </template>-->
<!--            </DataTable>-->
<!--        </div>-->
<!--    </ContextModuleLayout>-->
<!--</template>-->
