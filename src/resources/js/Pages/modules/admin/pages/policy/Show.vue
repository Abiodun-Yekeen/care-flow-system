<script setup>
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import { Head, useForm } from "@inertiajs/vue3";
import PageHeader from "@/Components/layout/PageHeader.vue";
import ValidationErrors from "@/Components/forms/ValidationErrors.vue";
import LoadButton from "@/Components/ui/LoadButton.vue";
import CreateNewUser from "@/Pages/modules/admin/components/CreateNewUser.vue";
import { storeToRefs } from "pinia";
import { useNotificationStore } from "@/core/stores/useNotificationStore.js";
import { useMetaStore } from "@/core/stores/metaStore.js";

// Define the user prop sent from Controller
const props = defineProps({
    user: Object
});

const metaStore = useMetaStore()
const notify = useNotificationStore()

// Initialize form with actual user data
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    mobile_no: props.user.mobile_no,
    staff_id: props.user.staff_id,
    department: props.user.department_id,
    role: props.user.roles.map(r => r.id), // Extracting IDs for the multiselect/checkboxes
});

</script>

<template>
    <ContextModuleLayout>
        <Head :title="'Edit User: ' + props.user.name"/>

        <div class="h-full flex flex-col bg-white overflow-hidden">
            <PageHeader
                :title="'Edit User: ' + props.user.name"
                subtitle="Update account details and permissions."
            />

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-1">
                <main class="col-span-4 lg:col-span-3">
                    <div class="max-w-2xl bg-white overflow-hidden flex flex-col">
                        <div>
                            <ValidationErrors :errors="form.errors" class="mb-6" />
                            <CreateNewUser
                                :form="form"
                                :department="metaStore.department"
                                :role="metaStore.role"
                            />
                        </div>
                    </div>

                </main>
            </div>
        </div>
    </ContextModuleLayout>
</template>
