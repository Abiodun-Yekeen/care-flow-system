


<!--&lt;!&ndash; resources/js/Pages/Clinical/Patient/Registration.vue &ndash;&gt;-->
<!--<script setup>-->
<!--import { ref, computed, watch, onMounted } from 'vue'-->
<!--import { useForm, router } from '@inertiajs/vue3'-->
<!--import FormSection from '@/Components/Form/FormSection.vue'-->
<!--import FormInput from '@/Components/Form/FormInput.vue'-->
<!--import FormSelect from '@/Components/Form/FormSelect.vue'-->
<!--import FormCheckbox from '@/Components/Form/FormCheckbox.vue'-->
<!--import FormDatePicker from '@/Components/Form/FormDatePicker.vue'-->
<!--import AllergyManager from '@/Pages/Clinical/Patient/Components/AllergyManager.vue'-->
<!--import LoadingButton from '@/Components/UI/LoadingButton.vue'-->
<!--import FormDivider from '@/Components/Form/FormDivider.vue'-->
<!--import ValidationErrors from '@/Components/Form/ValidationErrors.vue'-->
<!--import InfoTooltip from '@/Components/UI/InfoTooltip.vue'-->

<!--// Icons-->
<!--import {-->
<!--    User,-->
<!--    Mail,-->
<!--    Phone,-->
<!--    MapPin,-->
<!--    Calendar,-->
<!--    Heart,-->
<!--    Users,-->
<!--    Briefcase,-->
<!--    AlertCircle,-->
<!--    Save,-->
<!--    X,-->
<!--    ChevronDown,-->
<!--    ChevronUp,-->
<!--    UserPlus,-->
<!--    Fingerprint,-->
<!--    Droplets,-->
<!--    Activity,-->
<!--    Shield,-->
<!--    Clock,-->
<!--    CheckCircle,-->
<!--    AlertTriangle,-->
<!--    Loader-->
<!--} from "lucide-vue-next"-->
<!--import ContextModuleLayout from "@/Layouts/ContextModuleLayout.vue";-->

<!--const props = defineProps({-->
<!--    patient: {-->
<!--        type: Object,-->
<!--        default: null-->
<!--    },-->
<!--    states: {-->
<!--        type: Array,-->
<!--        default: () => []-->
<!--    },-->
<!--    lgas: {-->
<!--        type: Array,-->
<!--        default: () => []-->
<!--    }-->
<!--})-->

<!--const isEditing = computed(() => !!props.patient)-->

<!--// Generate hospital number-->
<!--const generateHospitalNo = () => {-->
<!--    const prefix = 'MRN'-->
<!--    const year = new Date().getFullYear()-->
<!--    const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0')-->
<!--    return `${prefix}-${year}-${random}`-->
<!--}-->

<!--// Form state with validation-->
<!--const form = useForm({-->
<!--    // Identification-->
<!--    id: props.patient?.id || null,-->
<!--    hospital_no: props.patient?.hospital_no || generateHospitalNo(),-->

<!--    // Personal Information-->
<!--    first_name: props.patient?.first_name || '',-->
<!--    middle_name: props.patient?.middle_name || '',-->
<!--    surname: props.patient?.surname || '',-->
<!--    gender: props.patient?.gender || '',-->
<!--    date_of_birth: props.patient?.date_of_birth || '',-->
<!--    marital_status: props.patient?.marital_status || '',-->

<!--    // Contact Information-->
<!--    phone_number: props.patient?.phone_number || '',-->
<!--    alternative_phone: props.patient?.alternative_phone || '',-->
<!--    email: props.patient?.email || '',-->
<!--    address: props.patient?.address || '',-->
<!--    city: props.patient?.city || '',-->
<!--    state_of_origin: props.patient?.state_of_origin || '',-->
<!--    lga: props.patient?.lga || '',-->
<!--    postal_code: props.patient?.postal_code || '',-->
<!--    country: props.patient?.country || 'Nigeria',-->

<!--    // Additional Information-->
<!--    religion: props.patient?.religion || '',-->
<!--    occupation: props.patient?.occupation || '',-->
<!--    blood_group: props.patient?.blood_group || '',-->
<!--    genotype: props.patient?.genotype || '',-->

<!--    // Next of Kin-->
<!--    next_of_kin_name: props.patient?.next_of_kin_name || '',-->
<!--    next_of_kin_phone: props.patient?.next_of_kin_phone || '',-->
<!--    next_of_kin_relationship: props.patient?.next_of_kin_relationship || '',-->
<!--    next_of_kin_address: props.patient?.next_of_kin_address || '',-->

<!--    // Clinical Information-->
<!--    allergies: props.patient?.allergies || [],-->
<!--    chronic_conditions: props.patient?.chronic_conditions || [],-->
<!--    code_status: props.patient?.code_status || 'Full Code',-->
<!--    organ_donor: props.patient?.organ_donor || false,-->

<!--    // Emergency Contact (if different from NOK)-->
<!--    emergency_contact_name: props.patient?.emergency_contact_name || '',-->
<!--    emergency_contact_phone: props.patient?.emergency_contact_phone || '',-->
<!--    emergency_contact_relationship: props.patient?.emergency_contact_relationship || '',-->

<!--    // Insurance Information-->
<!--    insurance_provider: props.patient?.insurance_provider || '',-->
<!--    insurance_number: props.patient?.insurance_number || '',-->
<!--    insurance_expiry: props.patient?.insurance_expiry || '',-->

<!--    // Consent & Privacy-->
<!--    consent_sms: props.patient?.consent_sms ?? true,-->
<!--    consent_email: props.patient?.consent_email ?? true,-->
<!--    consent_phone: props.patient?.consent_phone ?? true,-->
<!--    consent_research: props.patient?.consent_research ?? false,-->

<!--    // Metadata-->
<!--    created_by: props.patient?.created_by || null,-->
<!--    updated_by: props.patient?.updated_by || null-->
<!--})-->

<!--// UI State-->
<!--const activeSection = ref('demographics')-->
<!--const sections = {-->
<!--    demographics: { icon: User, label: 'Demographics', completed: false },-->
<!--    contact: { icon: Phone, label: 'Contact', completed: false },-->
<!--    clinical: { icon: Heart, label: 'Clinical', completed: false },-->
<!--    nextOfKin: { icon: Users, label: 'Next of Kin', completed: false },-->
<!--    emergency: { icon: AlertCircle, label: 'Emergency', completed: false },-->
<!--    insurance: { icon: Shield, label: 'Insurance', completed: false }-->
<!--}-->

<!--// Computed-->
<!--const fullName = computed(() => {-->
<!--    return `${form.surname} ${form.first_name} ${form.middle_name || ''}`.trim()-->
<!--})-->

<!--const age = computed(() => {-->
<!--    if (!form.date_of_birth) return null-->
<!--    const today = new Date()-->
<!--    const birthDate = new Date(form.date_of_birth)-->
<!--    let age = today.getFullYear() - birthDate.getFullYear()-->
<!--    const m = today.getMonth() - birthDate.getMonth()-->
<!--    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age&#45;&#45;-->
<!--    return age-->
<!--})-->

<!--const filteredLgas = computed(() => {-->
<!--    if (!form.state_of_origin) return []-->
<!--    return props.lgas.filter(lga => lga.state_id === form.state_of_origin)-->
<!--})-->

<!--// Watch for state changes to reset LGA-->
<!--watch(() => form.state_of_origin, () => {-->
<!--    form.lga = ''-->
<!--})-->

<!--// Validation rules-->
<!--const validateSection = (section) => {-->
<!--    switch(section) {-->
<!--        case 'demographics':-->
<!--            return form.first_name && form.surname && form.gender && form.date_of_birth-->
<!--        case 'contact':-->
<!--            return form.phone_number && form.address-->
<!--        case 'nextOfKin':-->
<!--            return form.next_of_kin_name && form.next_of_kin_phone-->
<!--        default:-->
<!--            return true-->
<!--    }-->
<!--}-->

<!--// Check section completion-->
<!--Object.keys(sections).forEach(key => {-->
<!--    sections[key].completed = computed(() => validateSection(key))-->
<!--})-->

<!--// Form submission-->
<!--const submit = () => {-->
<!--    const url = isEditing.value-->
<!--        ? route('patients.update', form.id)-->
<!--        : route('patients.store')-->

<!--    const method = isEditing.value ? 'put' : 'post'-->

<!--    form.post(url, {-->
<!--        preserveScroll: true,-->
<!--        onSuccess: () => {-->
<!--            // Show success notification-->
<!--            router.visit(route('patients.show', form.id))-->
<!--        },-->
<!--        onError: (errors) => {-->
<!--            // Scroll to first error-->
<!--            const firstErrorField = Object.keys(errors)[0]-->
<!--            document.getElementById(firstErrorField)?.scrollIntoView({ behavior: 'smooth', block: 'center' })-->
<!--        }-->
<!--    })-->
<!--}-->

<!--// Cancel and go back-->
<!--const cancel = () => {-->
<!--    if (form.isDirty) {-->
<!--        if (confirm('You have unsaved changes. Are you sure you want to leave?')) {-->
<!--            router.visit(route('patients.index'))-->
<!--        }-->
<!--    } else {-->
<!--        router.visit(route('patients.index'))-->
<!--    }-->
<!--}-->

<!--// Calculate form completion percentage-->
<!--const completionPercentage = computed(() => {-->
<!--    const requiredFields = [-->
<!--        'first_name', 'surname', 'gender', 'date_of_birth',-->
<!--        'phone_number', 'address', 'next_of_kin_name', 'next_of_kin_phone'-->
<!--    ]-->
<!--    const completed = requiredFields.filter(field => form[field]).length-->
<!--    return Math.round((completed / requiredFields.length) * 100)-->
<!--})-->

<!--// Auto-save draft-->
<!--let autoSaveTimer-->
<!--watch(() => form.data(), (newData) => {-->
<!--    clearTimeout(autoSaveTimer)-->
<!--    autoSaveTimer = setTimeout(() => {-->
<!--        if (form.isDirty && !isEditing.value) {-->
<!--            localStorage.setItem('patient_registration_draft', JSON.stringify(newData))-->
<!--        }-->
<!--    }, 2000)-->
<!--}, { deep: true })-->

<!--// Load draft on mount-->
<!--onMounted(() => {-->
<!--    if (!isEditing.value) {-->
<!--        const draft = localStorage.getItem('patient_registration_draft')-->
<!--        if (draft) {-->
<!--            const shouldRestore = confirm('You have a saved draft. Would you like to restore it?')-->
<!--            if (shouldRestore) {-->
<!--                Object.assign(form, JSON.parse(draft))-->
<!--            } else {-->
<!--                localStorage.removeItem('patient_registration_draft')-->
<!--            }-->
<!--        }-->
<!--    }-->
<!--})-->
<!--</script>-->

<!--<template>-->
<!--    <ContextModuleLayout>-->
<!--        <div class="patient-registration min-h-screen bg-white ">-->
<!--            &lt;!&ndash; Header &ndash;&gt;-->
<!--            <div class="bg-white border-b sticky top-0 z-10 shadow-sm ">-->
<!--                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">-->
<!--                    <div class="flex justify-between items-center py-4">-->
<!--                        <div>-->
<!--                            <h1 class="text-xl font-bold text-gray-900 flex items-center">-->
<!--                                <UserPlus class="w-5 h-5 mr-2 text-blue-600" />-->
<!--                                {{ isEditing ? 'Edit Patient' : 'Register New Patient' }}-->
<!--                                <span v-if="isEditing" class="ml-2 text-sm font-normal text-gray-500">-->
<!--                                    MRN: {{ form.hospital_no }}-->
<!--                                </span>-->
<!--                            </h1>-->
<!--                            <div class="flex items-center mt-1 space-x-4">-->
<!--                                <div class="flex items-center">-->
<!--                                    <div class="w-32 h-1.5 bg-gray-200 rounded-full overflow-hidden">-->
<!--                                        <div class="h-full bg-green-500 transition-all duration-300"-->
<!--                                             :style="{ width: completionPercentage + '%' }"></div>-->
<!--                                    </div>-->
<!--                                    <span class="ml-2 text-xs text-gray-500">{{ completionPercentage }}% complete</span>-->
<!--                                </div>-->
<!--                                <span v-if="form.isDirty" class="text-xs text-amber-600 flex items-center">-->
<!--                                    <Clock class="w-3 h-3 mr-1" /> Unsaved changes-->
<!--                                </span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="flex space-x-3">-->
<!--                            <button @click="cancel"-->
<!--                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition flex items-center">-->
<!--                                <X class="w-4 h-4 mr-2" />-->
<!--                                Cancel-->
<!--                            </button>-->
<!--                            <LoadingButton :loading="form.processing"-->
<!--                                           @click="submit"-->
<!--                                           class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition flex items-center">-->
<!--                                <Save class="w-4 h-4 mr-2" />-->
<!--                                {{ isEditing ? 'Update Patient' : 'Register Patient' }}-->
<!--                            </LoadingButton>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

<!--            &lt;!&ndash; Validation Errors &ndash;&gt;-->
<!--            <ValidationErrors :errors="form.errors" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" />-->

<!--            &lt;!&ndash; Main Form &ndash;&gt;-->
<!--            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">-->
<!--                <div class="flex gap-6">-->
<!--                    &lt;!&ndash; Section Navigation &ndash;&gt;-->
<!--                    <div class="w-64 flex-shrink-0">-->
<!--                        <div class="bg-white rounded-lg border shadow-sm sticky top-24">-->
<!--                            <div class="p-4">-->
<!--                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Registration Progress</h3>-->
<!--                                <nav class="space-y-1">-->
<!--                                    <button v-for="(section, key) in sections"-->
<!--                                            :key="key"-->
<!--                                            @click="activeSection = key"-->
<!--                                            :class="[-->
<!--                                                'w-full flex items-center px-3 py-2 rounded-lg text-sm transition',-->
<!--                                                activeSection === key-->
<!--                                                    ? 'bg-blue-50 text-blue-700 font-medium'-->
<!--                                                    : 'hover:bg-gray-50 text-gray-600',-->
<!--                                                section.completed ? 'border-l-2 border-green-500' : ''-->
<!--                                            ]">-->
<!--                                        <component :is="section.icon" class="w-4 h-4 mr-3" />-->
<!--                                        <span class="flex-1 text-left">{{ section.label }}</span>-->
<!--                                        <span v-if="section.completed" class="text-green-500">-->
<!--                                            <CheckCircle class="w-4 h-4" />-->
<!--                                        </span>-->
<!--                                    </button>-->
<!--                                </nav>-->
<!--                            </div>-->

<!--                            &lt;!&ndash; Patient Summary Preview &ndash;&gt;-->
<!--                            <div v-if="fullName" class="border-t p-4 bg-gray-50">-->
<!--                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Patient Preview</h4>-->
<!--                                <div class="flex items-center space-x-3">-->
<!--                                    <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">-->
<!--                                        {{ form.first_name?.charAt(0) }}{{ form.surname?.charAt(0) }}-->
<!--                                    </div>-->
<!--                                    <div class="flex-1 min-w-0">-->
<!--                                        <p class="text-sm font-bold text-gray-900 truncate">{{ fullName }}</p>-->
<!--                                        <p class="text-xs text-gray-500">{{ form.hospital_no }}</p>-->
<!--                                        <p v-if="age" class="text-xs text-gray-500">{{ age }} years, {{ form.gender || 'Gender not set' }}</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    &lt;!&ndash; Form Sections &ndash;&gt;-->
<!--                    <div class="flex-1 space-y-6">-->
<!--                        &lt;!&ndash; Demographics Section &ndash;&gt;-->
<!--                        <FormSection v-show="activeSection === 'demographics'"-->
<!--                                     title="Demographics Information"-->
<!--                                     subtitle="Basic patient identification details"-->
<!--                                     icon="User">-->
<!--                            <div class="grid grid-cols-12 gap-4">-->
<!--                                &lt;!&ndash; Hospital Number (Read-only) &ndash;&gt;-->
<!--                                <div class="col-span-4">-->
<!--                                    <FormInput-->
<!--                                        id="hospital_no"-->
<!--                                        v-model="form.hospital_no"-->
<!--                                        label="Hospital Number (MRN)"-->
<!--                                        :error="form.errors.hospital_no"-->
<!--                                        readonly-->
<!--                                        required-->
<!--                                        icon="Fingerprint">-->
<!--                                        <template #hint>Auto-generated unique identifier</template>-->
<!--                                    </FormInput>-->
<!--                                </div>-->

<!--                                &lt;!&ndash; Name Fields &ndash;&gt;-->
<!--                                <div class="col-span-8">-->
<!--                                    <div class="grid grid-cols-3 gap-3">-->
<!--                                        <FormInput-->
<!--                                            id="surname"-->
<!--                                            v-model="form.surname"-->
<!--                                            label="Surname"-->
<!--                                            :error="form.errors.surname"-->
<!--                                            required-->
<!--                                            placeholder="Last name"-->
<!--                                            autocomplete="family-name" />-->

<!--                                        <FormInput-->
<!--                                            id="first_name"-->
<!--                                            v-model="form.first_name"-->
<!--                                            label="First Name"-->
<!--                                            :error="form.errors.first_name"-->
<!--                                            required-->
<!--                                            placeholder="First name"-->
<!--                                            autocomplete="given-name" />-->

<!--                                        <FormInput-->
<!--                                            id="middle_name"-->
<!--                                            v-model="form.middle_name"-->
<!--                                            label="Middle Name"-->
<!--                                            :error="form.errors.middle_name"-->
<!--                                            placeholder="Middle name (optional)"-->
<!--                                            autocomplete="additional-name" />-->
<!--                                    </div>-->
<!--                                </div>-->

<!--                                &lt;!&ndash; Demographics Grid &ndash;&gt;-->
<!--                                <div class="col-span-4">-->
<!--                                    <FormSelect-->
<!--                                        id="gender"-->
<!--                                        v-model="form.gender"-->
<!--                                        label="Gender"-->
<!--                                        :error="form.errors.gender"-->
<!--                                        required>-->
<!--                                        <option value="">Select gender</option>-->
<!--                                        <option value="male">Male</option>-->
<!--                                        <option value="female">Female</option>-->
<!--                                        <option value="other">Other</option>-->
<!--                                    </FormSelect>-->
<!--                                </div>-->

<!--                                <div class="col-span-4">-->
<!--                                    <FormDatePicker-->
<!--                                        id="date_of_birth"-->
<!--                                        v-model="form.date_of_birth"-->
<!--                                        label="Date of Birth"-->
<!--                                        :error="form.errors.date_of_birth"-->
<!--                                        required-->
<!--                                        max="today"-->
<!--                                        show-age>-->
<!--                                        <template #suffix>-->
<!--                                            <span v-if="age" class="text-xs text-gray-500">({{ age }} years)</span>-->
<!--                                        </template>-->
<!--                                    </FormDatePicker>-->
<!--                                </div>-->

<!--                                <div class="col-span-4">-->
<!--                                    <FormSelect-->
<!--                                        id="marital_status"-->
<!--                                        v-model="form.marital_status"-->
<!--                                        label="Marital Status"-->
<!--                                        :error="form.errors.marital_status">-->
<!--                                        <option value="">Select status</option>-->
<!--                                        <option value="single">Single</option>-->
<!--                                        <option value="married">Married</option>-->
<!--                                        <option value="divorced">Divorced</option>-->
<!--                                        <option value="widowed">Widowed</option>-->
<!--                                        <option value="separated">Separated</option>-->
<!--                                    </FormSelect>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </FormSection>-->

<!--                        &lt;!&ndash; Contact Information &ndash;&gt;-->
<!--                        <FormSection v-show="activeSection === 'contact'"-->
<!--                                     title="Contact Information"-->
<!--                                     subtitle="Address and communication details"-->
<!--                                     icon="Phone">-->
<!--                            <div class="grid grid-cols-12 gap-4">-->
<!--                                &lt;!&ndash; Phone Numbers &ndash;&gt;-->
<!--                                <div class="col-span-6">-->
<!--                                    <FormInput-->
<!--                                        id="phone_number"-->
<!--                                        v-model="form.phone_number"-->
<!--                                        label="Primary Phone Number"-->
<!--                                        :error="form.errors.phone_number"-->
<!--                                        required-->
<!--                                        type="tel"-->
<!--                                        placeholder="0803 123 4567"-->
<!--                                        icon="Phone">-->
<!--                                        <template #hint>Primary contact for appointments</template>-->
<!--                                    </FormInput>-->
<!--                                </div>-->

<!--                                <div class="col-span-6">-->
<!--                                    <FormInput-->
<!--                                        id="alternative_phone"-->
<!--                                        v-model="form.alternative_phone"-->
<!--                                        label="Alternative Phone"-->
<!--                                        :error="form.errors.alternative_phone"-->
<!--                                        type="tel"-->
<!--                                        placeholder="0803 123 4567"-->
<!--                                        icon="Phone" />-->
<!--                                </div>-->

<!--                                <div class="col-span-6">-->
<!--                                    <FormInput-->
<!--                                        id="email"-->
<!--                                        v-model="form.email"-->
<!--                                        label="Email Address"-->
<!--                                        :error="form.errors.email"-->
<!--                                        type="email"-->
<!--                                        placeholder="patient@example.com"-->
<!--                                        icon="Mail" />-->
<!--                                </div>-->

<!--                                <div class="col-span-6">-->
<!--                                    <FormInput-->
<!--                                        id="address"-->
<!--                                        v-model="form.address"-->
<!--                                        label="Residential Address"-->
<!--                                        :error="form.errors.address"-->
<!--                                        required-->
<!--                                        placeholder="Street address, building, apartment"-->
<!--                                        icon="MapPin" />-->
<!--                                </div>-->

<!--                                <div class="col-span-3">-->
<!--                                    <FormInput-->
<!--                                        id="city"-->
<!--                                        v-model="form.city"-->
<!--                                        label="City"-->
<!--                                        :error="form.errors.city"-->
<!--                                        placeholder="City" />-->
<!--                                </div>-->

<!--                                <div class="col-span-3">-->
<!--                                    <FormSelect-->
<!--                                        id="state_of_origin"-->
<!--                                        v-model="form.state_of_origin"-->
<!--                                        label="State"-->
<!--                                        :error="form.errors.state_of_origin"-->
<!--                                        :options="states"-->
<!--                                        option-value="id"-->
<!--                                        option-label="name"-->
<!--                                        placeholder="Select state" />-->
<!--                                </div>-->

<!--                                <div class="col-span-3">-->
<!--                                    <FormSelect-->
<!--                                        id="lga"-->
<!--                                        v-model="form.lga"-->
<!--                                        label="LGA"-->
<!--                                        :error="form.errors.lga"-->
<!--                                        :options="filteredLgas"-->
<!--                                        option-value="id"-->
<!--                                        option-label="name"-->
<!--                                        placeholder="Select LGA"-->
<!--                                        :disabled="!form.state_of_origin" />-->
<!--                                </div>-->

<!--                                <div class="col-span-3">-->
<!--                                    <FormInput-->
<!--                                        id="postal_code"-->
<!--                                        v-model="form.postal_code"-->
<!--                                        label="Postal Code"-->
<!--                                        :error="form.errors.postal_code"-->
<!--                                        placeholder="Postal code" />-->
<!--                                </div>-->

<!--                                &lt;!&ndash; Communication Consent &ndash;&gt;-->
<!--                                <div class="col-span-12">-->
<!--                                    <div class="bg-gray-50 p-4 rounded-lg">-->
<!--                                        <label class="text-xs font-bold text-gray-400 uppercase block mb-2">Communication Preferences</label>-->
<!--                                        <div class="grid grid-cols-4 gap-4">-->
<!--                                            <FormCheckbox-->
<!--                                                v-model="form.consent_sms"-->
<!--                                                label="SMS Notifications"-->
<!--                                                description="Appointment reminders via SMS" />-->

<!--                                            <FormCheckbox-->
<!--                                                v-model="form.consent_email"-->
<!--                                                label="Email Notifications"-->
<!--                                                description="Results and updates via email" />-->

<!--                                            <FormCheckbox-->
<!--                                                v-model="form.consent_phone"-->
<!--                                                label="Phone Calls"-->
<!--                                                description="Allow phone contact" />-->

<!--                                            <FormCheckbox-->
<!--                                                v-model="form.consent_research"-->
<!--                                                label="Research Participation"-->
<!--                                                description="Contact for research studies" />-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </FormSection>-->

<!--                        &lt;!&ndash; Clinical Information &ndash;&gt;-->
<!--                        <FormSection v-show="activeSection === 'clinical'"-->
<!--                                     title="Clinical Information"-->
<!--                                     subtitle="Medical history and alerts"-->
<!--                                     icon="Heart">-->
<!--                            <div class="grid grid-cols-12 gap-4">-->
<!--                                &lt;!&ndash; Blood Group & Genotype &ndash;&gt;-->
<!--                                <div class="col-span-3">-->
<!--                                    <FormSelect-->
<!--                                        id="blood_group"-->
<!--                                        v-model="form.blood_group"-->
<!--                                        label="Blood Group"-->
<!--                                        :error="form.errors.blood_group">-->
<!--                                        <option value="">Select</option>-->
<!--                                        <option value="A+">A+</option>-->
<!--                                        <option value="A-">A-</option>-->
<!--                                        <option value="B+">B+</option>-->
<!--                                        <option value="B-">B-</option>-->
<!--                                        <option value="O+">O+</option>-->
<!--                                        <option value="O-">O-</option>-->
<!--                                        <option value="AB+">AB+</option>-->
<!--                                        <option value="AB-">AB-</option>-->
<!--                                    </FormSelect>-->
<!--                                </div>-->

<!--                                <div class="col-span-3">-->
<!--                                    <FormSelect-->
<!--                                        id="genotype"-->
<!--                                        v-model="form.genotype"-->
<!--                                        label="Genotype"-->
<!--                                        :error="form.errors.genotype">-->
<!--                                        <option value="">Select</option>-->
<!--                                        <option value="AA">AA</option>-->
<!--                                        <option value="AS">AS</option>-->
<!--                                        <option value="SS">SS</option>-->
<!--                                        <option value="AC">AC</option>-->
<!--                                        <option value="SC">SC</option>-->
<!--                                    </FormSelect>-->
<!--                                </div>-->

<!--                                <div class="col-span-3">-->
<!--                                    <FormSelect-->
<!--                                        id="code_status"-->
<!--                                        v-model="form.code_status"-->
<!--                                        label="Code Status"-->
<!--                                        :error="form.errors.code_status">-->
<!--                                        <option value="Full Code">Full Code</option>-->
<!--                                        <option value="DNR">DNR (Do Not Resuscitate)</option>-->
<!--                                        <option value="DNI">DNI (Do Not Intubate)</option>-->
<!--                                        <option value="Comfort Care">Comfort Care Only</option>-->
<!--                                    </FormSelect>-->
<!--                                </div>-->

<!--                                <div class="col-span-3">-->
<!--                                    <FormCheckbox-->
<!--                                        v-model="form.organ_donor"-->
<!--                                        label="Organ Donor"-->
<!--                                        description="Patient is registered as organ donor"-->
<!--                                        class="mt-8" />-->
<!--                                </div>-->

<!--                                &lt;!&ndash; Allergies Manager &ndash;&gt;-->
<!--                                <div class="col-span-12">-->
<!--                                    <AllergyManager-->
<!--                                        v-model="form.allergies"-->
<!--                                        :error="form.errors.allergies"-->
<!--                                        label="Allergies & Reactions"-->
<!--                                        description="Document all known allergies and adverse reactions" />-->
<!--                                </div>-->

<!--                                &lt;!&ndash; Chronic Conditions &ndash;&gt;-->
<!--                                <div class="col-span-12">-->
<!--                                    <div class="border rounded-lg p-4">-->
<!--                                        <label class="text-xs font-bold text-gray-400 uppercase block mb-2">Chronic Conditions</label>-->
<!--                                        <div class="grid grid-cols-3 gap-3">-->
<!--                                            <FormCheckbox-->
<!--                                                v-for="condition in ['Hypertension', 'Diabetes', 'Asthma', 'Heart Disease', 'Arthritis', 'Cancer']"-->
<!--                                                :key="condition"-->
<!--                                                v-model="form.chronic_conditions"-->
<!--                                                :value="condition"-->
<!--                                                :label="condition" />-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </FormSection>-->

<!--                        &lt;!&ndash; Next of Kin &ndash;&gt;-->
<!--                        <FormSection v-show="activeSection === 'nextOfKin'"-->
<!--                                     title="Next of Kin"-->
<!--                                     subtitle="Emergency contact person"-->
<!--                                     icon="Users">-->
<!--                            <div class="grid grid-cols-12 gap-4">-->
<!--                                <div class="col-span-6">-->
<!--                                    <FormInput-->
<!--                                        id="next_of_kin_name"-->
<!--                                        v-model="form.next_of_kin_name"-->
<!--                                        label="Full Name"-->
<!--                                        :error="form.errors.next_of_kin_name"-->
<!--                                        required-->
<!--                                        placeholder="Contact person's full name"-->
<!--                                        icon="User" />-->
<!--                                </div>-->

<!--                                <div class="col-span-6">-->
<!--                                    <FormInput-->
<!--                                        id="next_of_kin_phone"-->
<!--                                        v-model="form.next_of_kin_phone"-->
<!--                                        label="Phone Number"-->
<!--                                        :error="form.errors.next_of_kin_phone"-->
<!--                                        required-->
<!--                                        type="tel"-->
<!--                                        placeholder="0803 123 4567"-->
<!--                                        icon="Phone" />-->
<!--                                </div>-->

<!--                                <div class="col-span-6">-->
<!--                                    <FormSelect-->
<!--                                        id="next_of_kin_relationship"-->
<!--                                        v-model="form.next_of_kin_relationship"-->
<!--                                        label="Relationship"-->
<!--                                        :error="form.errors.next_of_kin_relationship">-->
<!--                                        <option value="">Select relationship</option>-->
<!--                                        <option value="spouse">Spouse</option>-->
<!--                                        <option value="parent">Parent</option>-->
<!--                                        <option value="child">Child</option>-->
<!--                                        <option value="sibling">Sibling</option>-->
<!--                                        <option value="relative">Other Relative</option>-->
<!--                                        <option value="friend">Friend</option>-->
<!--                                        <option value="guardian">Legal Guardian</option>-->
<!--                                    </FormSelect>-->
<!--                                </div>-->

<!--                                <div class="col-span-6">-->
<!--                                    <FormInput-->
<!--                                        id="next_of_kin_address"-->
<!--                                        v-model="form.next_of_kin_address"-->
<!--                                        label="Address"-->
<!--                                        :error="form.errors.next_of_kin_address"-->
<!--                                        placeholder="Contact address"-->
<!--                                        icon="MapPin" />-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </FormSection>-->

<!--                        &lt;!&ndash; Emergency Contact &ndash;&gt;-->
<!--                        <FormSection v-show="activeSection === 'emergency'"-->
<!--                                     title="Emergency Contact"-->
<!--                                     subtitle="Additional emergency contact (if different from NOK)"-->
<!--                                     icon="AlertCircle">-->
<!--                            <div class="grid grid-cols-12 gap-4">-->
<!--                                <div class="col-span-6">-->
<!--                                    <FormInput-->
<!--                                        id="emergency_contact_name"-->
<!--                                        v-model="form.emergency_contact_name"-->
<!--                                        label="Full Name"-->
<!--                                        placeholder="Emergency contact name"-->
<!--                                        icon="User" />-->
<!--                                </div>-->

<!--                                <div class="col-span-6">-->
<!--                                    <FormInput-->
<!--                                        id="emergency_contact_phone"-->
<!--                                        v-model="form.emergency_contact_phone"-->
<!--                                        label="Phone Number"-->
<!--                                        type="tel"-->
<!--                                        placeholder="0803 123 4567"-->
<!--                                        icon="Phone" />-->
<!--                                </div>-->

<!--                                <div class="col-span-6">-->
<!--                                    <FormSelect-->
<!--                                        id="emergency_contact_relationship"-->
<!--                                        v-model="form.emergency_contact_relationship"-->
<!--                                        label="Relationship">-->
<!--                                        <option value="">Select relationship</option>-->
<!--                                        <option value="spouse">Spouse</option>-->
<!--                                        <option value="parent">Parent</option>-->
<!--                                        <option value="child">Child</option>-->
<!--                                        <option value="sibling">Sibling</option>-->
<!--                                        <option value="other">Other</option>-->
<!--                                    </FormSelect>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </FormSection>-->

<!--                        &lt;!&ndash; Insurance Information &ndash;&gt;-->
<!--                        <FormSection v-show="activeSection === 'insurance'"-->
<!--                                     title="Insurance Information"-->
<!--                                     subtitle="Health coverage details"-->
<!--                                     icon="Shield">-->
<!--                            <div class="grid grid-cols-12 gap-4">-->
<!--                                <div class="col-span-4">-->
<!--                                    <FormInput-->
<!--                                        id="insurance_provider"-->
<!--                                        v-model="form.insurance_provider"-->
<!--                                        label="Insurance Provider"-->
<!--                                        placeholder="e.g., NHIS, Private Insurance"-->
<!--                                        icon="Shield" />-->
<!--                                </div>-->

<!--                                <div class="col-span-4">-->
<!--                                    <FormInput-->
<!--                                        id="insurance_number"-->
<!--                                        v-model="form.insurance_number"-->
<!--                                        label="Insurance Number"-->
<!--                                        placeholder="Policy number"-->
<!--                                        icon="Fingerprint" />-->
<!--                                </div>-->

<!--                                <div class="col-span-4">-->
<!--                                    <FormDatePicker-->
<!--                                        id="insurance_expiry"-->
<!--                                        v-model="form.insurance_expiry"-->
<!--                                        label="Expiry Date"-->
<!--                                        min="today" />-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </FormSection>-->

<!--                        &lt;!&ndash; Navigation Buttons &ndash;&gt;-->
<!--                        <div class="flex justify-between pt-6">-->
<!--                            <button v-if="activeSection !== 'demographics'"-->
<!--                                    @click="activeSection = Object.keys(sections)[Object.keys(sections).indexOf(activeSection) - 1]"-->
<!--                                    class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">-->
<!--                                Previous Section-->
<!--                            </button>-->

<!--                            <button v-if="activeSection !== 'insurance'"-->
<!--                                    @click="activeSection = Object.keys(sections)[Object.keys(sections).indexOf(activeSection) + 1]"-->
<!--                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 ml-auto">-->
<!--                                Next Section-->
<!--                            </button>-->

<!--                            <button v-else-->
<!--                                    @click="submit"-->
<!--                                    class="px-6 py-2 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 ml-auto"-->
<!--                                    :disabled="form.processing">-->
<!--                                <span v-if="form.processing" class="flex items-center">-->
<!--                                    <Loader class="w-4 h-4 mr-2 animate-spin" />-->
<!--                                    Processing...-->
<!--                                </span>-->
<!--                                <span v-else class="flex items-center">-->
<!--                                    <CheckCircle class="w-4 h-4 mr-2" />-->
<!--                                    Complete Registration-->
<!--                                </span>-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </ContextModuleLayout>-->
<!--</template>-->

<!--<style scoped>-->
<!--.patient-registration {-->
<!--    /* Smooth scrolling between sections */-->
<!--    scroll-behavior: smooth;-->
<!--}-->

<!--/* Animate section transitions */-->
<!--.form-section-enter-active,-->
<!--.form-section-leave-active {-->
<!--    transition: opacity 0.3s ease;-->
<!--}-->

<!--.form-section-enter-from,-->
<!--.form-section-leave-to {-->
<!--    opacity: 0;-->
<!--}-->

<!--/* Progress bar animation */-->
<!--.bg-green-500 {-->
<!--    transition: width 0.3s ease-in-out;-->
<!--}-->
<!--</style>-->


<script setup>
import { ref } from 'vue'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import VisitModal from "@/Pages/modules/patient/components/VisitModal.vue";
import {Head} from "@inertiajs/vue3";

const showVisitModal = ref(false)
</script>

<template>
    <ContextModuleLayout>
        <Head title= "Patient Information"/>
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Patient Summary</h1>
                <p class="text-gray-500">Hospital No: 000234</p>
            </div>

            <button
                @click="showVisitModal = true"
                class="bg-green-600 text-white px-4 py-2 rounded"
            >
                + Start New Visit
            </button>
        </div>

        <!-- Active Encounter Card -->
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-semibold">No Active Encounter</h3>
        </div>

        <VisitModal
            v-if="showVisitModal"
            @close="showVisitModal = false"
        />

    </div>
    </ContextModuleLayout>

</template>
