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
import FormInput from "@/Components/forms/FormInput.vue";
import FormTextarea from "@/Components/forms/FormTextarea.vue";

// Define the user prop sent from Controller
const props = defineProps({
    role: Object
});

const metaStore = useMetaStore()
const notify = useNotificationStore()

// Initialize form with actual user data
const form = useForm({
    name: props.role.name,
    display_name: props.role.display_name,
    description: props.role.description,
});
const submit = () => {
    form.clearErrors();
    form.put(route('roles.update', props.role.id), {
        preserveScroll: true,
        onSuccess: () => {
            notify.success("Role updated successfully");
        },
    });
};
</script>

<template>
    <ContextModuleLayout>
        <Head :title="'Edit User: ' + props.role.name"/>

        <div class="h-full flex flex-col bg-white overflow-hidden">
            <PageHeader
                :title="'Edit User: ' + props.role.name"
                subtitle="Update account details and permissions."
            />

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-1">
                <main class="col-span-4 lg:col-span-3">
                    <div class="max-w-2xl bg-white overflow-hidden flex flex-col">
                        <div>
                            <ValidationErrors :errors="form.errors" class="mb-6" />

                            <div class="col-span-12 md:col-span-6 mb-4">
                                <FormInput
                                    id="display_name"
                                    v-model="form.display_name"
                                    label="Display Name"
                                    required
                                    :error="form.errors.display_name"
                                    placeholder="e.g. Head of Department"
                                />
                            </div>
                            <div class="col-span-12 md:col-span-6 mb-4">
                                <FormInput
                                    id="name"
                                    v-model="form.name"
                                    label="System Name (ID)"
                                    required
                                    :error="form.errors.name"
                                    placeholder="e.g. dept_manager"
                                />
                                <p class="mt-1 text-[10px] text-slate-400 italic uppercase">
                                    This is used for internal policy matching and cannot contain spaces.
                                </p>
                            </div>
                            <div class="col-span-12 md:col-span-6">
                                <FormTextarea
                                    id="description"
                                    v-model="form.description"
                                    label="Description"
                                    :rows="2"
                                    :error="form.errors.description"
                                    placeholder="What access does this role represent?"
                                />
                            </div>

                        </div>
                    </div>

                    <div class="p-4 flex flex-row flex-nowrap gap-2">
                        <LoadButton :loading="form.processing" @click="submit">
                            {{ form.processing ? 'Updating...' : 'Save Changes' }}
                        </LoadButton>
                    </div>
                </main>
            </div>
        </div>
    </ContextModuleLayout>
</template>
