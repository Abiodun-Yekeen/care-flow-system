<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import PageHeader from "@/Components/layout/PageHeader.vue";
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import { XIcon, ShieldIcon, LayersIcon, ActivityIcon } from 'lucide-vue-next';

const props = defineProps({
    role: Object,
    allPolicies: Array,
    allRoles: Array,
    effectiveAccess: Array,
})

console.log(props.effectiveAccess)
const policyForm = useForm({ policy_ids: [] })
const parentForm = useForm({ parent_ids: [] })

function attachPolicies() {
    policyForm.post(route('roles.policies.attach', props.role.id), {
        preserveScroll: true,
        onSuccess: () => policyForm.reset(),
    })
}

function detachPolicy(policyId) {
    if(confirm('Detach this policy?')) {
        policyForm.delete(route('roles.policies.detach', [props.role.id, policyId]), { preserveScroll: true })
    }
}

function attachParents() {
    parentForm.post(route('roles.parents.attach', props.role.id), {
        preserveScroll: true,
        onSuccess: () => parentForm.reset(),
    })
}

function detachParent(parentId) {
    if(confirm('Remove this parent inheritance?')) {
        parentForm.delete(route('roles.parents.detach', [props.role.id, parentId]), { preserveScroll: true })
    }
}
</script>

<template>
    <ContextModuleLayout>
        <Head :title="'Role: ' + props.role.name"/>

        <div class="h-full flex flex-col bg-slate-50 overflow-hidden">
            <PageHeader
                :title="'Role: ' + props.role.name"
                subtitle="Manage policies and inheritance hierarchy."
            />

            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                <div class="bg-white rounded-xl border p-4 shadow-sm">
                    <div class="text-sm text-slate-500 mb-1 font-bold uppercase tracking-wider">Description</div>
                    <div class="text-slate-700">{{ role.description || 'No description provided.' }}</div>
                    <div v-if="role.is_system" class="mt-3 inline-flex items-center px-2 py-1 rounded bg-amber-50 text-amber-700 text-xs font-bold border border-amber-100">
                        SYSTEM PROTECTED ROLE
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl border shadow-sm flex flex-col">
                        <div class="p-4 border-b font-bold flex items-center gap-2"><ShieldIcon :size="18"/> Attached Policies</div>
                        <div class="p-4 flex-1">
                            <div class="mb-4 flex flex-wrap gap-2">
                                <span v-for="policy in role.policies" :key="policy.id" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 border border-blue-100 px-3 py-1 text-sm text-blue-700">
                                    {{ policy.name }}
                                    <button @click="detachPolicy(policy.id)" class="hover:text-red-500"><XIcon :size="14"/></button>
                                </span>
                            </div>
                            <select v-model="policyForm.policy_ids" multiple class="min-h-[100px] w-full rounded-lg border-slate-200 text-sm mb-3">
                                <option v-for="policy in allPolicies" :key="policy.id" :value="policy.id">{{ policy.name }}</option>
                            </select>
                            <button class="w-full rounded-lg bg-blue-600 py-2 text-white font-bold text-sm" @click="attachPolicies">Attach Selected</button>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border shadow-sm flex flex-col">
                        <div class="p-4 border-b font-bold flex items-center gap-2"><LayersIcon :size="18"/> Inherits From</div>
                        <div class="p-4 flex-1">
                            <div class="mb-4 flex flex-wrap gap-2">
                                <span v-for="parent in role.parents" :key="parent.id" class="inline-flex items-center gap-2 rounded-lg bg-slate-100 border border-slate-200 px-3 py-1 text-sm text-slate-700">
                                    {{ parent.name }}
                                    <button @click="detachParent(parent.id)" class="hover:text-red-500"><XIcon :size="14"/></button>
                                </span>
                            </div>
                            <select v-model="parentForm.parent_ids" multiple class="min-h-[100px] w-full rounded-lg border-slate-200 text-sm mb-3">
                                <option v-for="parent in allRoles" :key="parent.id" :value="parent.id">{{ parent.name }}</option>
                            </select>
                            <button class="w-full rounded-lg bg-slate-800 py-2 text-white font-bold text-sm" @click="attachParents">Apply Inheritance</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
                    <div class="p-4 border-b bg-slate-50 font-bold flex items-center gap-2"><ActivityIcon :size="18"/> Effective Permissions Matrix</div>
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-[10px]">Resource</th>
                            <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-[10px]">Action</th>
                            <th class="px-4 py-3 text-left font-bold text-slate-500 uppercase text-[10px]">Status</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        <tr v-for="item in effectiveAccess" :key="item.resource + item.action" class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-blue-600">{{ item.resource }}</td>
                            <td class="px-4 py-3 font-medium">{{ item.action }}</td>
                            <td class="px-4 py-3">
                                    <span :class="item.result === 'denied' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'" class="rounded-full px-3 py-0.5 text-[10px] font-bold uppercase border">
                                        {{ item.result }}
                                    </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </ContextModuleLayout>
</template>
