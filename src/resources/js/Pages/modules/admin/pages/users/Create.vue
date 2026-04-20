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


const form = useForm(userForm)

const metaStore = useMetaStore()
const notify = useNotificationStore()

const {department} = storeToRefs(metaStore)

const submit = () => {
    form.clearErrors();
    form.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            notify.success("User created and roles assigned successfully.");
        },

    });
};
</script>
<template>
    <ContextModuleLayout>
        <Head title= "Create Temporary File"/>

        <div class="h-full flex flex-col bg-white overflow-hidden">
            <PageHeader
                title="Create New User"
                subtitle="Create a new user to access the system."
            />

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-1">

                <main class="col-span-4 lg:col-span-3">
                    <div class=" max-w-2xl bg-white   overflow-hidden flex flex-col">

                        <div class=" ">
                            <ValidationErrors :errors="form.errors" class="mb-6" />
                            <CreateNewUser
                                :form="form"
                                :department="department"
                            />
                        </div>
                    </div>

                    <div class="p-4 flex flex-row flex-nowrap  gap-2">
                        <LoadButton :loading="form.processing" @click="submit">
                            {{ form.processing ? 'Saving...' : 'Register' }}
                        </LoadButton>
                    </div>

                </main>

            </div>

        </div>
    </ContextModuleLayout>
</template>




