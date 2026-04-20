<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import PageHeader from "@/Components/layout/PageHeader.vue";
import LoadButton from "@/Components/ui/LoadButton.vue";
import FormTextarea from "@/Components/forms/FormTextarea.vue";
import FormInput from "@/Components/forms/FormInput.vue";
import ValidationErrors from "@/Components/forms/ValidationErrors.vue"; // Assuming you have a loading button component

const form = useForm({
    name: '',         // System Name (slug)
    display_name: '',  // Friendly Name
    description: '',
});

function submit() {
    form.post(route('roles.store'));
}
</script>

<template>
    <ContextModuleLayout>
        <Head title="Create IAM Role" />

        <div class="h-full flex flex-col bg-white overflow-hidden">
            <PageHeader
                title="Create New Role"
                subtitle="Define a new job function or security group."
            />

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 overflow-y-auto flex-1">
                <main class="col-span-4 lg:col-span-3">
                    <div class="max-w-2xl bg-white overflow-hidden flex flex-col p-4 ">
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
                            <div class="col-span-12 md:col-span-6">
                                <FormInput
                                    id="name"
                                    v-model="form.name"
                                    label="System Name (ID)"
                                    required
                                    :error="form.errors.staff_id"
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
                            {{ form.processing ? 'Creating...' : 'Create' }}
                        </LoadButton>
                    </div>
                </main>
            </div>
        </div>
    </ContextModuleLayout>
</template>
