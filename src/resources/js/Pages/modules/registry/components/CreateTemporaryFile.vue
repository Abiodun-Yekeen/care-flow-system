<script setup>

import FormInput from "@/Components/forms/FormInput.vue";
import FormTextarea from "@/Components/forms/FormTextarea.vue";
import FormFileUpload from "@/Components/forms/FormFileUpload.vue";
import FormDatePicker from "@/Components/forms/FormDatePicker.vue";

const props = defineProps(
    {
        form: Object,
    })

</script>

<template>


    <div class="grid grid-cols-12 gap-5">

        <div class="col-span-12 md:col-span-12">
            <FormInput
                id="subject"
                v-model="form.subject"
                label="Subject/Title"
                required
                :error="form.errors.subject"
            />
        </div>

        <div class="col-span-12 md:col-span-12">
            <FormDatePicker
                id="date_receive"
                v-model="form.date_receive"
                label="Date Received"
                required
                :error="form.errors.date_receive"
            />
        </div>

        <div class="col-span-12 md:col-span-12">
            <FormTextarea
                id="remark"
                v-model="form.remark"
                label="Remark"
                :error="form.errors.remark"
                :rows="2"
            />

        </div>

    </div>

        <div class="col-span-12 md:col-span-6">
            <FormFileUpload
                v-model="form.scanned_file"
                :maxFiles="5"
                accept=".jpg,.jpeg"
            />
            <div v-if="form.errors.scanned_file" class="text-red-500 text-xs mt-1">
                {{ form.errors.scanned_file }}
            </div>
        </div>

<!--    </FormSection>-->
</template>
