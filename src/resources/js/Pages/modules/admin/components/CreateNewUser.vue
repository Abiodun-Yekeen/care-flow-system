<script setup>

import FormInput from "@/Components/forms/FormInput.vue";
import FormTextarea from "@/Components/forms/FormTextarea.vue";
import FormFileUpload from "@/Components/forms/FormFileUpload.vue";
import FormDatePicker from "@/Components/forms/FormDatePicker.vue";
import FormSelect from "@/Components/forms/FormSelect.vue";

const props = defineProps(
    {
        form: Object,
         role: Object,
        department: Object
    })

</script>

<template>


    <div class="grid grid-cols-12 gap-5">

        <div class="col-span-12 md:col-span-6">
            <FormInput
                id="name"
                v-model="form.name"
                label="Full Name"
                required
                :error="form.errors.name"
            />
        </div>

        <div class="col-span-12 md:col-span-6">
            <FormInput
                id="staff_id"
                v-model="form.staff_id"
                label="Staff ID"
                required
                :error="form.errors.staff_id"
            />
        </div>
        <div class="col-span-12 md:col-span-6">
            <FormInput
                id="mobile_no"
                v-model="form.mobile_no"
                label="Mobile Number"
                required
                :error="form.errors.mobile_no"
            />
        </div>
        <div class="col-span-12 md:col-span-6">
            <FormInput
                id="email"
                v-model="form.email"
                label="Email Address"
                :error="form.errors.email"
            />
        </div>
        <div class="col-span-12 md:col-span-6">
            <FormSelect
                id="department"
                v-model="form.department"
                label="Department"
                required
                :options="department"
                :error="form.errors.department"
            />
        </div>



    </div>


</template>
