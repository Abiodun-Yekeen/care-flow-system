
<template>
    <ContextModuleLayout>
        <Head title= "Patient"/>
        <div v-if="loading" class="p-6 text-gray-500">
            Loading patients...
        </div>

        <ActionBar :actions="patientActions(patient)"/>

        <div class="h-full flex flex-col bg-white overflow-hidden">

            <header class="bg-white p-4 border-b shrink-0 z-10">
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Master Patient Index</h1>
                <p class="text-xs text-slate-500 font-medium">{{ totalPatients }} patients registered</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-1">

                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-800 tracking-widest">Quick Search</label>
                            <div class="relative">
                                <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                                <input type="text" placeholder="Search..." class="w-full border-slate-200 rounded-xl pl-9 p-2.5 text-sm outline-none focus:ring-2 ring-green-500/20" />
                            </div>
                        </div>

                        <div class="space-y-4 overflow-auto" >
                            <FilterSection title="Patient Status" :count="activeFilterCount">
                                <StatusFilter v-model="selectedStatuses" :options="statusOptions" />
                            </FilterSection>

                            <FilterSection title="Clinical Risk" :count="riskFilterCount">
                                <RiskFilter v-model="selectedRisks" />
                            </FilterSection>

                            <FilterSection title="Safety Alerts">
                                <SafetyFilter v-model="selectedAlerts" />
                            </FilterSection>

                            <FilterSection title="Location">
                                <LocationFilter v-model="selectedLocations" :locations="locations" />
                            </FilterSection>
                        </div>


                        <button class="w-full py-2.5 bg-slate-50 text-slate-500 text-[10px] font-black uppercase rounded-xl hover:bg-slate-100 transition-colors">
                            Clear All Filters
                        </button>
                    </div>
                </div>

                <main class="col-span-1 lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                        <div class="p-4 border-b bg-slate-50/30 flex justify-between items-center">
                            <span class="text-[10px] font-black text-black uppercase tracking-widest">Patient Directory</span>
                            <div class="flex items-center gap-2">
                                <button class="p-1.5 bg-white border rounded-lg shadow-xs hover:bg-slate-50"><ChevronLeft class="w-4 h-4 text-slate-500"/></button>
                                <button class="p-1.5 bg-white border rounded-lg shadow-xs hover:bg-slate-50"><ChevronRight class="w-4 h-4 text-slate-500"/></button>
                            </div>
                        </div>

                        <div class="overflow-auto">
                            <table class="w-full text-left border-collapse min-w-[700px]">
                                <thead class="bg-slate-50/50">
                                <tr class="text-[10px] uppercase text-slate-400 font-black tracking-widest border-b border-slate-100">
                                    <th class="p-4 pl-6">Patient Details</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Clinical Info</th>
                                    <th class="p-4 text-right pr-6">Action</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                <PatientRow v-for="p in paginatedPatients" :key="p.id" :patient="p" @open-chart="openClinical" />
                                </tbody>
                            </table>
                        </div>

                        <div v-if="!paginatedPatients.length" class="p-20 text-center">
                            <Users class="w-12 h-12 text-slate-200 mx-auto mb-2" />
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-tight">No Patients Found</p>
                        </div>
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

