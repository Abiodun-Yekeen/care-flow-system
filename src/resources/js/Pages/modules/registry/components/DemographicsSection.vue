<script setup>
import { computed } from 'vue'
import FormSection from '@/Components/forms/FormSection.vue'
import FormInput from '@/Components/forms/FormInput.vue'
import FormSelect from '@/Components/forms/FormSelect.vue'
import FormDatePicker from '@/Components/forms/FormDatePicker.vue'
import { Fingerprint } from "lucide-vue-next"
import {usePage} from "@inertiajs/vue3";
import PhotoUploader from "@/Pages/modules/patient/components/PhotoUploader.vue";

const props = defineProps(
    {
        form: Object,
        gender: Object,
        maritalStatus:Object,
        title:Object,
        triedNext: Boolean,
    })


const age = computed(() => {
    if (!props.form.date_of_birth) return null
    const birthDate = new Date(props.form.date_of_birth)
    const today = new Date()
    let age = today.getFullYear() - birthDate.getFullYear()
    const m = today.getMonth() - birthDate.getMonth()
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--
    return age
})
</script>

<template>


    <div class="grid grid-cols-12 gap-5">

        <div class="col-span-12">
            <FormSelect
                id="title"
                v-model="form.title"
                label="Title"
                required
                :options="title"
            />
        </div>

        <div class="col-span-12 md:col-span-6">
            <FormInput
                id="surname"
                v-model="form.surname"
                label="Surname"
                required
                :error="form.errors.surname"
                :tried-next="triedNext"
            />
        </div>

        <div class="col-span-12 md:col-span-6">
            <FormInput
                id="first_name"
                v-model="form.first_name"
                label="First Name"
                required
                :error="form.errors.first_name"
            />
        </div>

        <div class="col-span-12 md:col-span-6">
            <FormInput
                id="middle_name"
                v-model="form.middle_name"
                label="Middle Name"
                :error="form.errors.middle_name"
            />
        </div>

        <div class="col-span-12 md:col-span-6">
            <FormSelect
                id="gender"
                v-model="form.gender"
                label="Gender"
                required
                :options="gender"
            />
        </div>

        <div class="col-span-12 md:col-span-6">
            <FormDatePicker
                id="date_of_birth"
                v-model="form.date_of_birth"
                label="Date of Birth"
                required
                :error="form.errors.date_of_birth"
            />
            <p v-if="age !== null" class="mt-1 text-xs text-green-600 font-medium">
                Calculated Age: {{ age }} years
            </p>
        </div>

        <div class="col-span-12 md:col-span-6">
            <FormSelect
                id="marital_status"
                v-model="form.marital_status"
                label="Marital Status"
                required
                :options="maritalStatus"
            />
        </div>
    </div>
<!--    </FormSection>-->
</template>
