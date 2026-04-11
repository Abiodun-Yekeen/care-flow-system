
<template>
    <ContextModuleLayout>
        <Head title= "Patient"/>
        <div v-if="loading" class="p-6 text-gray-500">
            Loading patients...
        </div>

        <ActionBar :actions="patientActions(patient)"/>

        <div class="h-full flex flex-col bg-white overflow-hidden">

            <header class="bg-white p-4 border-b shrink-0 z-10">
                <h1 class="text-2xl font-bold text-[#091E3E]">Receive & Register File</h1>
                <p class="text-sm text-slate-500">Search for an existing file or create a new registry entry.</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-1">

                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-800 tracking-widest">Quick Search</label>
                            <div class="relative">
                                <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                                <input type="text" placeholder=" File No/Subject" class="w-full border-slate-200 rounded-xl pl-9 p-2.5 text-sm outline-none " />
                            </div>
                        </div>

                        <div class="space-y-4 overflow-auto" >
                            <FilterSection title="File Summary" :count="activeFilterCount">
                                <StatusFilter v-model="selectedStatuses" :options="statusOptions" />
                            </FilterSection>




                        </div>


                    </div>
                </div>

                <main class="col-span-1 lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase text-slate-500">File Number / Temp No</label>
                                <input type="text" class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]" placeholder="FETH/ADM/102">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase text-slate-500">Source (Mailing/Unit)</label>
                                <select class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]">
                                    <option>External Mail</option>
                                    <option>Internal Memo</option>
                                    <option>Walk-in Registry</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-xs font-bold uppercase text-slate-500">Subject / Title</label>
                                <input type="text" class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]" placeholder="Brief description of file contents">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase text-slate-500">Date Received</label>
                                <input type="date" class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]">
                            </div>

                            <div class="md:col-span-2 space-y-1">
                                <label class="text-xs font-bold uppercase text-slate-500">Registry Minute / Remarks</label>
                                <textarea rows="3" class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]" placeholder="Add any initial observations..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border-2 border-dashed border-slate-300 p-8 text-center hover:border-[#06A3DA] transition-colors group cursor-pointer">
                        <div class="mx-auto w-12 h-12 bg-blue-50 text-[#06A3DA] rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#091E3E]">Upload Scanned Document</h3>
                        <p class="text-xs text-slate-400 mt-1">Drag and drop PDF or JPG here, or click to browse</p>
                    </div>

                </main>

            </div>
        </div>
    </ContextModuleLayout>
</template>


<script setup>
import { ref, computed } from 'vue'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import FilterSection from '@/Components/Filter/FilterSection.vue'
import StatusFilter from '@/Components/Filter/StatusFilter.vue'
import RiskFilter from '@/Components/Filter/RiskFilter.vue'
import SafetyFilter from '@/Components/Filter/SafetyFilter.vue'
import LocationFilter from '@/Components/Filter/LocationFilter.vue'
import {
    Plus,
    Download,
    Search,
    ChevronLeft,
    ChevronRight,
    Users
} from 'lucide-vue-next'
import {Head, router} from "@inertiajs/vue3";
import ActionBar from "@/Components/forms/ActionBar.vue";
import {patientActions} from "@/Components/ui/useActions.js";
import PatientRow from "@/Components/patient/PatientRow.vue";

// Mock data with clinical context
const patients = ref([
    {
        id: 1,
        name: 'DOE, JONATHAN',
        initials: 'JD',
        mrn: '882931',
        nhs: 'NHS-123-456',
        status: 'Inpatient',
        statusClass: 'bg-blue-50 border-blue-200 text-blue-700',
        isolation: 'Contact',
        fallRisk: true,
        allergies: ['Penicillin'],
        latestBP: '128/82',
        latestHR: 88,
        abnormalVitals: false,
        activeEncounter: true,
        activeEncounterType: 'Emergency',
        lastVisit: 'Today, 09:15',
        location: 'Ward A - Bed 12'
    },
    {
        id: 2,
        name: 'Samuel, Paul',
        initials: 'SP',
        mrn: '882931',
        nhs: 'NHS-123-456',
        status: 'Inpatient',
        statusClass: 'bg-blue-50 border-blue-200 text-blue-700',
        isolation: 'Contact',
        fallRisk: true,
        allergies: ['Penicillin'],
        latestBP: '128/82',
        latestHR: 88,
        abnormalVitals: false,
        activeEncounter: true,
        activeEncounterType: 'Emergency',
        lastVisit: 'Today, 09:15',
        location: 'Ward A - Bed 12'
    },
    {
        id: 3,
        name: 'Ahmed, Smith',
        initials: 'AS',
        mrn: '882931',
        nhs: 'NHS-123-456',
        status: 'Inpatient',
        statusClass: 'bg-blue-50 border-blue-200 text-blue-700',
        isolation: 'Contact',
        fallRisk: true,
        allergies: ['Penicillin'],
        latestBP: '128/82',
        latestHR: 88,
        abnormalVitals: false,
        activeEncounter: true,
        activeEncounterType: 'Emergency',
        lastVisit: 'Today, 09:15',
        location: 'Ward A - Bed 12'
    },
    {
        id: 4,
        name: 'Yusuf, Peter',
        initials: 'YP',
        mrn: '882931',
        nhs: 'NHS-123-456',
        status: 'Inpatient',
        statusClass: 'bg-blue-50 border-blue-200 text-blue-700',
        isolation: 'Contact',
        fallRisk: true,
        allergies: ['Penicillin'],
        latestBP: '128/82',
        latestHR: 88,
        abnormalVitals: false,
        activeEncounter: true,
        activeEncounterType: 'Emergency',
        lastVisit: 'Today, 09:15',
        location: 'Ward A - Bed 12'
    },
    // ... more patients
])

const selectedPatients = ref([])
const selectedStatuses = ref([])
const selectedRisks = ref([])
const selectedAlerts = ref([])
const selectedLocations = ref([])

const totalPatients = computed(() => patients.value.length)
const activePatients = computed(() => patients.value.filter(p => p.activeEncounter).length)
const activeFilterCount = computed(() => selectedStatuses.value.length)
const riskFilterCount = computed(() => selectedRisks.value.length)
const totalActiveFilters = computed(() =>
    selectedStatuses.value.length +
    selectedRisks.value.length +
    selectedAlerts.value.length +
    selectedLocations.value.length
)

const paginatedPatients = computed(() => patients.value) // Add pagination logic

const handlePatientSelect = (patientId) => {
    // Handle selection
}
const isMobileFilterOpen = ref(false)
const openClinical = (patientId) => {
    router.get(`/clinical/patients/${patientId}`)
}
</script>

