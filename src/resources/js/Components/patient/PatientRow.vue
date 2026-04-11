<!-- resources/js/Components/Patient/PatientRow.vue -->
<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import {
    AlertCircle,
    Activity,
    Pill,
    FileText,
    Clock,
    ChevronRight,
    AlertTriangle,
    Shield,
    HeartPulse
} from "lucide-vue-next"

const props = defineProps({
    patient: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['select', 'open-chart'])

const safetyIcons = computed(() => {
    const icons = []
    if (props.patient.isolation) icons.push({ icon: Shield, color: 'text-yellow-600', bg: 'bg-yellow-100', tooltip: props.patient.isolation })
    if (props.patient.fallRisk) icons.push({ icon: AlertTriangle, color: 'text-orange-600', bg: 'bg-orange-100', tooltip: 'Fall Risk' })
    if (props.patient.allergies?.length) icons.push({ icon: AlertCircle, color: 'text-red-600', bg: 'bg-red-100', tooltip: props.patient.allergies.join(', ') })
    return icons
})

const vitalsStatus = computed(() => {
    if (props.patient.abnormalVitals) return 'text-red-600 font-bold'
    return 'text-gray-600'
})
</script>

<template>
    <tr class="hover:bg-blue-50/30 transition-colors group">
        <td class="p-4 pl-6">
            <input type="checkbox"
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                   @change="$emit('select', patient.id)" />
        </td>

        <td class="p-4">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    {{ patient.initials }}
                </div>
                <div>
                    <div class="font-bold text-sm text-gray-900 group-hover:text-blue-600 cursor-pointer flex items-center"
                         @click="$emit('open-chart', patient.id)">
                        {{ patient.name }}
                        <ChevronRight class="w-3 h-3 ml-1 opacity-0 group-hover:opacity-100 transition" />
                    </div>
                    <div class="flex items-center space-x-2 text-[10px] text-gray-400 font-mono">
                        <span>MRN: {{ patient.mrn }}</span>
                        <span class="text-gray-300">|</span>
                        <span>NHS: {{ patient.nhs }}</span>
                    </div>
                    <div class="text-[10px] text-gray-500 mt-0.5">
                        {{ patient.location || 'Location not set' }}
                    </div>
                </div>
            </div>
        </td>

        <td class="p-4">
            <span :class="['px-2 py-1 rounded-full text-[10px] font-bold border', patient.statusClass]">
                {{ patient.status }}
            </span>
            <div v-if="patient.activeEncounter" class="flex items-center mt-1 text-[10px] text-emerald-600">
                <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full mr-1 animate-pulse"></span>
                {{ patient.activeEncounterType }}
            </div>
        </td>

        <td class="p-4">
            <div class="flex space-x-1">
                <div v-for="(item, index) in safetyIcons" :key="index"
                     :class="[item.bg, 'p-1 rounded-full group relative']"
                     :title="item.tooltip">
                    <component :is="item.icon" :class="['w-3 h-3', item.color]" />
                    <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1 px-2 py-0.5 bg-gray-800 text-white text-[8px] rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10">
                        {{ item.tooltip }}
                    </span>
                </div>
                <span v-if="!safetyIcons.length" class="text-[10px] text-gray-300">None</span>
            </div>
        </td>

        <td class="p-4">
            <div class="space-y-1">
                <div class="flex items-center text-xs" :class="vitalsStatus">
                    <HeartPulse class="w-3 h-3 mr-1" />
                    <span>{{ patient.latestBP || '--' }} / {{ patient.latestHR || '--' }}</span>
                </div>
                <div v-if="patient.medications?.length" class="flex items-center text-[10px] text-gray-500">
                    <Pill class="w-3 h-3 mr-1" />
                    <span>{{ patient.medications.length }} active meds</span>
                </div>
            </div>
        </td>

        <td class="p-4">
            <div class="flex items-center text-xs text-gray-600">
                <Clock class="w-3 h-3 mr-1 text-gray-400" />
                {{ patient.lastVisit }}
            </div>
        </td>

        <td class="p-4 text-right">
            <button @click="$emit('open-chart', patient.id)"
                    class="text-blue-600 font-bold text-xs hover:underline flex items-center justify-end ml-auto">
                Open Chart
                <ChevronRight class="w-3 h-3 ml-1" />
            </button>
        </td>
    </tr>
</template>
