<script setup>
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import {Head, router, useForm} from "@inertiajs/vue3";
import CreateTemporaryFile from "@/Pages/modules/registry/components/CreateTemporaryFile.vue";
import {formData} from "@/Pages/modules/registry/services/formData.js";
import PageHeader from "@/Components/layout/PageHeader.vue";
import ValidationErrors from "@/Components/forms/ValidationErrors.vue";
import LoadButton from "@/Components/ui/LoadButton.vue";


const form = useForm(formData)
const submit = (isDraft = false) => {
    form.is_draft = isDraft; // Pass this to your Laravel controller
    form.post(route('registry.store'));
};
</script>
<template>
    <ContextModuleLayout>
        <Head title= "Create Temporary File"/>

        <div class="h-full flex flex-col bg-white overflow-hidden">
            <PageHeader
                title="Receive & Register File"
                subtitle="Create a new Temporary File."
            />

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-1">

                <main class="col-span-4 lg:col-span-3">
                    <div class=" max-w-2xl bg-white   overflow-hidden flex flex-col">

                        <div class=" ">
                            <ValidationErrors :errors="form.errors" class="mb-6" />
                            <CreateTemporaryFile :form="form"/>
                        </div>
                    </div>

                    <div class="p-4 flex flex-row flex-nowrap  gap-2">
                        <LoadButton
                            variant="secondary"
                            :loading="form.processing"
                            @click="submit(true)">
                            {{ form.processing ? 'Saving...' : 'Save Draft' }}
                        </LoadButton>

                        <LoadButton :loading="form.processing" @click="submit(false)">
                            {{ form.processing ? 'Submitting...' : 'Submit' }}
                        </LoadButton>
                    </div>

                </main>

            </div>

        </div>
    </ContextModuleLayout>
</template>




