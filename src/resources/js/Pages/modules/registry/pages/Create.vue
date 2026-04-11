<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import {useForm, router, usePage, Head} from '@inertiajs/vue3'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import ValidationErrors from '@/Components/forms/ValidationErrors.vue'
import {formData,sections} from "@/Pages/modules/patient/services/formData.js";
// Section Components
import DemographicsSection from '../components/DemographicsSection.vue'
import ContactSection from '../components/ContactSection.vue'
import ClinicalSection from '../components/ClinicalSection.vue'
import NextOfKinSection from '../components/NextOfKinSection.vue'
import RegistrationSidebar from "../components/RegistrationSidebar.vue"
// Icons
import { UserPlus, Save, X, SaveIcon, CheckCircle, ChevronLeft, ChevronRight, Menu } from "lucide-vue-next"
import {useMetaStore} from "@/core/stores/metaStore.js";
import {storeToRefs} from "pinia";
import {useNotificationStore} from "@/core/stores/useNotificationStore.js";
import LoadButton from "@/Components/ui/LoadButton.vue";

const metaStore = useMetaStore()
const notify = useNotificationStore()

const {title,gender,state,blood_group,genotype,
    marital_status,severity,relationship,lga
} = storeToRefs(metaStore)


const form = useForm(formData)

const activeSection = ref('demographics')
const isMobileMenuOpen = ref(false)
const triedNext = ref(false)

const navKeys = Object.keys(sections)

// Validation Logic
const isSectionComplete = (sectionId) => {
    const required = sections[sectionId]?.required || []
    return required.every(field => form[field] && form[field].toString().trim() !== '')
}

const canNavigateTo = (targetKey) => {
    const targetIdx = navKeys.indexOf(targetKey)
    const currentIdx = navKeys.indexOf(activeSection.value)

    if (targetIdx <= currentIdx) return true // Always allow going back

    // Check if all previous sections are complete before going forward
    for (let i = 0; i < targetIdx; i++) {
        if (!isSectionComplete(navKeys[i])) return false
    }
    return true
}

// Navigation Handlers
const handleNav = (key) => {
    // We set this to true so that if navigation is blocked,
    // the user immediately sees WHY (via inline red text)
    triedNext.value = true

    if (canNavigateTo(key)) {
        activeSection.value = key
        isMobileMenuOpen.value = false
        // Reset for the new section so it's "clean" until they try to leave it
        triedNext.value = false
    }
}

const nextSection = () => {
    triedNext.value = true

    if (isSectionComplete(activeSection.value)) {
        const idx = navKeys.indexOf(activeSection.value)
        if (idx < navKeys.length - 1) {
            activeSection.value = navKeys[idx + 1]
            // Reset for the new section
            triedNext.value = false
            window.scrollTo({ top: 0, behavior: 'smooth' })
        }
    } else {
        // Optional: Focus the first empty field
        const firstRequired = sections[activeSection.value].required.find(field => !form[field])
        if (firstRequired) {
            document.getElementById(firstRequired)?.focus()
        }
    }
}

const submit = () => {
    form.clearErrors();
    form.post(route('patients.store'), {
        preserveScroll: true,
        onSuccess: () => {
            notify.success("Patient registered successfully");
        },

    });
};
// const submit = async () => {
//
//     const res = await submitForm(
//         () => PatientApi.savePatientData(form.data()),
//         form
//     )
//     if (!res) return
//
//     localStorage.removeItem('patient_reg_draft')
//
//     const uuid = res.data.data.uuid
//
//     // Pass the success message into the "state" of the next page
//     router.visit(route('patients.show', uuid), {
//         onSuccess: () => {
//             // This runs AFTER the new page is loaded
//             notify.success("Patient registered successfully")
//         }
//     })
//
// }
</script>


<template>
    <ContextModuleLayout >
        <Head title= "Register Patient"/>
        <div class="h-full flex flex-col bg-white overflow-hidden">
            <header class="bg-white  border-b shrink-0 z-10">
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Patient Registration</h1>
                <p class="text-xs text-slate-500 font-medium truncate">
                    Currently Registering: {{ form.first_name || 'New' }} {{ form.surname || 'Patient' }}
                </p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-1 ">

                <div class="lg:col-span-1">
                    <RegistrationSidebar
                        :sections="sections"
                        :active-section="activeSection"
                        :is-section-complete="isSectionComplete"
                        :full-name="`${form.first_name} ${form.surname}`"
                        :hospital-no="form.hospital_no"
                        :is-mobile-open="true"
                        @update:active-section="handleNav"
                        :form="form"
                    />
                </div>

                <main class="col-span-4 lg:col-span-3">
                    <div class=" max-w-2xl bg-white   overflow-hidden flex flex-col">

                        <div class=" ">
                            <ValidationErrors :errors="form.errors" class="mb-6" />

                            <transition name="fade" mode="out-in">
                                <div :key="activeSection">
                                    <DemographicsSection v-if="activeSection === 'demographics'"
                                                         :form="form"
                                                         :tried-next="triedNext"
                                                         :gender="gender"
                                                         :marital-status="marital_status"
                                                         :title="title"
                                    />
                                    <ContactSection v-if="activeSection === 'contact'"
                                                    :form="form"
                                                    :states="state"
                                                    :lgas="lga"
                                                    :tried-next="triedNext"
                                    />
                                    <ClinicalSection v-if="activeSection === 'clinical'"
                                                     :form="form"
                                                     :tried-next="triedNext"
                                                     :blood-group="blood_group"
                                                     :genotype="genotype"
                                                     :Severity="severity"
                                    />
                                    <NextOfKinSection v-if="activeSection === 'nextOfKin'"
                                                      :form="form"
                                                      :tried-next="triedNext"
                                                      :relationship="relationship"
                                    />
                                </div>
                            </transition>
                        </div>

                        <div class="p-4 lg:p-6  flex flex-col sm:flex-row justify-between items-center gap-4">
                            <LoadButton
                                @click="activeSection = navKeys[navKeys.indexOf(activeSection)-1]"
                                :disabled="activeSection === 'demographics'"
                                class="bg-blue-50"
                            >
                                <ChevronLeft class="w-5 h-5 mr-2" /> Back
                            </LoadButton>

                            <div class="w-full sm:w-auto">
                                <LoadButton v-if="activeSection !== 'nextOfKin'"
                                        @click="nextSection"
                                >
                                    Next  <ChevronRight class="w-5 h-5 ml-2" />
                                </LoadButton>

                                <LoadButton v-else
                                            :loading="form.processing"
                                            @click="submit"
                                >
                                    {{ form.processing ? 'Saving...' : 'Submit' }}
                                </LoadButton>

                            </div>
                        </div>
                    </div>
                </main>

            </div>
        </div>
    </ContextModuleLayout>
</template>


