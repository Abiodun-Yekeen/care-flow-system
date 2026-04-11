<script setup>
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import {Head, router, useForm} from "@inertiajs/vue3";
import ActionBar from "@/Components/forms/ActionBar.vue";
import {patientActions} from "@/Components/ui/useActions.js";
import CreateTemporaryFile from "@/Pages/modules/registry/components/CreateTemporaryFile.vue";
import {formData} from "@/Pages/modules/registry/services/formData.js";


const form = useForm(formData)
</script>
<template>
    <ContextModuleLayout>
        <Head title= "Create Temporary File"/>

        <div class="h-full flex flex-col bg-white overflow-hidden">

            <header class="bg-white p-4 border-b shrink-0 z-10">
                <h1 class="text-2xl font-bold text-[#091E3E]">Receive & Register File</h1>
                <p class="text-sm text-slate-500">create a new registry entry.</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-1">

                <main class="col-span-1 lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                            <CreateTemporaryFile :form="form"/>
                        </div>
                    </div>



                </main>

            </div>
        </div>
    </ContextModuleLayout>
</template>




