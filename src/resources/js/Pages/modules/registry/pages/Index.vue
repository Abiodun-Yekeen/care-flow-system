<script setup>
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import CreateTemporaryFile from "@/Pages/modules/registry/components/CreateTemporaryFile.vue";
import {formData} from "@/Pages/modules/registry/services/formData.js";
import PageHeader from "@/Components/layout/PageHeader.vue";
import ValidationErrors from "@/Components/forms/ValidationErrors.vue";
import LoadButton from "@/Components/ui/LoadButton.vue";
import {useNotificationStore} from "@/core/stores/useNotificationStore.js";
import {computed} from "vue";

const notify = useNotificationStore()

const minDate = computed(() => {
    const currentYear = new Date().getFullYear();
    return `${currentYear}-01-01`;
});

const form = useForm(formData)
const submit = (isDraft = false) => {
    form.clearErrors();
    form.is_draft = isDraft; // Pass this to your Laravel controller
    form.post(route('register.store') , {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    });
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
                                <CreateTemporaryFile :form="form" :min="minDate"/>
                            </div>
                        </div>

                        <div class="p-4 flex flex-row flex-nowrap gap-2">
                            <LoadButton
                                type="button"
                                variant="secondary"
                                :loading="form.processing && form.is_draft"
                                :disabled="form.processing"
                                @click="submit(true)">
                                {{ form.processing && form.is_draft ? 'Saving Draft...' : 'Save Draft' }}
                            </LoadButton>

                            <LoadButton
                                type="button"
                                :loading="form.processing && !form.is_draft"
                                :disabled="form.processing"
                                @click="submit(false)">
                                {{ form.processing && !form.is_draft ? 'Submitting...' : 'Submit' }}
                            </LoadButton>
                        </div>

                    </main>

                </div>

            </div>
    </ContextModuleLayout>
</template>




