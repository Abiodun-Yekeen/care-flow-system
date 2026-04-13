<script setup>
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import {Head, router, useForm} from "@inertiajs/vue3";
import { userForm } from "@/Pages/modules/admin/services/adminFormData.js";
import PageHeader from "@/Components/layout/PageHeader.vue";
import ValidationErrors from "@/Components/forms/ValidationErrors.vue";
import LoadButton from "@/Components/ui/LoadButton.vue";
import CreateNewUser from "@/Pages/modules/admin/components/CreateNewUser.vue";
import {storeToRefs} from "pinia";
import {useNotificationStore} from "@/core/stores/useNotificationStore.js";
import {useMetaStore} from "@/core/stores/metaStore.js";
import FormSelect from "@/Components/forms/FormSelect.vue";


const form = useForm({
    excel_file: null, // New field for the file
});

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.excel_file = file;
    }
};

const upload = () => {

    if(!form.excel_file) notify.info("Select Excel File")
    form.post(route('users.import-user-data'), {
        forceFormData: true, // Crucial for file uploads
        onSuccess: () => notify.success("Users imported successfully"),
    });
};

const notify = useNotificationStore()


</script>


<template>
    <ContextModuleLayout>
        <Head title="Import User Data" />

        <div class="h-full flex flex-col bg-white overflow-hidden">
            <PageHeader
                title="Import New User Data"
                subtitle="Upload an Excel or CSV file to bulk-create users."
            />

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-2">
                <main class="col-span-4 lg:col-span-3">
                    <div class="max-w-2xl bg-white overflow-hidden flex flex-col">

                    <ValidationErrors :errors="form.errors" />


                    <div class="space-y-2 ">
                        <label class="text-sm font-semibold text-slate-700">Excel or CSV File</label>

                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-44 border-2 border-slate-300 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mb-2 text-sm text-slate-500">
                                        <span class="font-semibold text-[#06A3DA]">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-xs text-slate-400">XLSX, XLS, or CSV (Max. 10MB)</p>

                                    <p v-if="form.excel_file" class="mt-2 text-sm font-medium text-[#06A3DA]">
                                        Selected: {{ form.excel_file.name }}
                                    </p>
                                </div>
                                <input
                                    id="dropzone-file"
                                    type="file"
                                    class="hidden"
                                    accept=".xlsx, .xls, .csv"
                                    @change="onFileChange"
                                />
                            </label>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-100 flex items-center gap-3">
                        <LoadButton
                            :loading="form.processing"
                            @click="upload"
                            :disabled="!form.excel_file"
                            class="px-8"
                        >
                            {{ form.processing ? 'Processing...' : 'Start Import' }}
                        </LoadButton>

<!--                        <a href="/templates/user_import_template.xlsx" class="text-xs text-slate-500 hover:text-[#06A3DA] underline">-->
<!--                            Download Sample Template-->
<!--                        </a>-->
                    </div>
                    </div>
                </main>
            </div>
        </div>

    </ContextModuleLayout>
</template>
