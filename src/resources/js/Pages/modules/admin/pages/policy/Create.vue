<script setup>
import { useForm } from '@inertiajs/vue3';
import FormInput from "@/Components/forms/FormInput.vue";
import FormTextarea from "@/Components/forms/FormTextarea.vue";
import FormSelect from "@/Components/forms/FormSelect.vue";
import LoadButton from "@/Components/ui/LoadButton.vue";

const form = useForm({
    name: '',
    description: '',
    statements: [{ sid: 'Stmt1', effect: 'Allow', actions: [], resources: ['*'], actions_string: '', resources_string: '*' }]
});

const addStatement = () => form.statements.push({ sid: 'Stmt' + (form.statements.length + 1), effect: 'Allow', actions: [], resources: ['*'], actions_string: '', resources_string: '*' });
const submit = () => form.post(route('policies.store'));
</script>
<template>
    <div class="p-6 max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Create IAM Policy</h1>

        <div class="space-y-4 bg-white p-6 rounded-xl border">
            <FormInput v-model="form.name" label="Policy Name (e.g. RegistryFullAccess)" />
            <FormTextarea v-model="form.description" label="Description"  id="description"/>

            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-700">Policy Statements</h3>
                    <button @click="addStatement" type="button" class="text-sm text-blue-600">+ Add Statement</button>
                </div>

                <div v-for="(stmt, index) in form.statements" :key="index" class="p-4 border rounded-lg mb-4 bg-slate-50 relative">
                    <button @click="form.statements.splice(index, 1)" class="absolute top-2 right-2 text-red-400">×</button>

                    <div class="grid grid-cols-2 gap-4">
                        <FormSelect v-model="stmt.effect" :options="['Allow', 'Deny']" label="Effect" />
                        <FormInput v-model="stmt.sid" label="Statement ID (Optional)" />
                    </div>

                    <div class="mt-4">
                        <label class="text-xs font-bold uppercase text-slate-500">Actions (comma separated)</label>
                        <input v-model="stmt.actions_string" @blur="stmt.actions = stmt.actions_string.split(',')"
                               class="w-full border-slate-300 rounded-md" placeholder="e.g. view, create, *">
                    </div>

                    <div class="mt-4">
                        <label class="text-xs font-bold uppercase text-slate-500">Resource ARNs</label>
                        <input v-model="stmt.resources_string" @blur="stmt.resources = stmt.resources_string.split(',')"
                               class="w-full border-slate-300 rounded-md" placeholder="arn:cf:office_files:registry:*">
                    </div>
                </div>
            </div>

            <LoadButton @click="submit" :loading="form.processing">Create Policy</LoadButton>
        </div>
    </div>
</template>


