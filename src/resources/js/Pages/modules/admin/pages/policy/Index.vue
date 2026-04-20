<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import PageHeader from "@/Components/layout/PageHeader.vue";
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import { ShieldCheckIcon, ListIcon, PlusCircleIcon } from 'lucide-vue-next';
import PolicyBuilder from "@/Pages/modules/admin/components/PolicyBuilder.vue";
import {useNotificationStore} from "@/core/stores/useNotificationStore.js";

defineProps({
    policies: Object,
})
const notify = useNotificationStore()

const form = useForm({
    name: '',
    description: '',
    statement: {
        version: '2026-01-01',
        statements: [
            { sid: 'DefaultStmt', effect: 'allow', actions: ['view'], resources: ['*'] },
        ],
    },
})

function submit() {
    form.post(route('policies.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            notify.success('Policy created successfully!');
        },
    })
}
</script>

<template>
    <ContextModuleLayout>
        <Head title="Policy Management" />

        <div class="h-full flex flex-col bg-white overflow-hidden">
            <PageHeader title="IAM Policies" subtitle="Define fine-grained JSON permissions for roles." />

            <div class="flex-1 overflow-hidden p-6">
                <div class="grid grid-cols-12 gap-6 h-full">

                    <div class="col-span-12 lg:col-span-5 flex flex-col space-y-4">
                        <div class="bg-white rounded-xl border shadow-sm flex-1 flex flex-col overflow-hidden">
                            <div class="p-4 border-b bg-slate-50/50 flex items-center gap-2 font-bold text-slate-700">
                                <ListIcon :size="18" /> Active Policies
                            </div>
                            <div class="flex-1 overflow-y-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="sticky top-0 bg-white border-b z-10">
                                    <tr>
                                        <th class="p-4 text-[10px] font-bold text-slate-400 uppercase">Policy Name</th>
                                        <th class="p-4 text-[10px] font-bold text-slate-400 uppercase text-center">Roles</th>
                                        <th class="p-4"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                    <tr v-for="policy in policies.data" :key="policy.id" class="hover:bg-slate-50 transition">
                                        <td class="p-4">
                                            <div class="font-bold text-slate-700 text-sm">{{ policy.name }}</div>
                                            <div class="text-[10px] text-slate-400 truncate max-w-[200px]">{{ policy.description }}</div>
                                        </td>
                                        <td class="p-4 text-center">
                                                <span class="px-2 py-0.5 bg-slate-100 rounded text-xs font-bold text-slate-600">
                                                    {{ policy.roles_count }}
                                                </span>
                                        </td>
                                        <td class="p-4 text-right">
                                            <Link :href="route('policies.show', policy.id)" class="text-[#06A3DA] font-bold text-xs uppercase hover:underline">
                                                Manage
                                            </Link>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-12 lg:col-span-7 flex flex-col">
                        <div class="bg-white rounded-xl border shadow-sm flex-1 flex flex-col overflow-hidden">
                            <div class="p-4 border-b bg-blue-50/50 flex items-center justify-between">
                                <div class="flex items-center gap-2 font-bold text-blue-700">
                                    <PlusCircleIcon :size="18" /> Policy Visual Builder
                                </div>
                                <span class="text-[10px] font-mono text-blue-400">Ver: {{ form.statement.version }}</span>
                            </div>

                            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Policy Name</label>
                                        <input v-model="form.name" class="w-full rounded-lg border-slate-200 text-sm" placeholder="e.g. PharmacyFullAccess" />
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description</label>
                                        <input v-model="form.description" class="w-full rounded-lg border-slate-200 text-sm" placeholder="What does this allow?" />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <PolicyBuilder v-model="form.statement" />
                                </div>
                            </div>

                            <div class="p-4 border-t bg-slate-50 flex justify-end">
                                <button @click="submit" :disabled="form.processing"
                                        class="px-8 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-lg hover:bg-blue-700 transition flex items-center gap-2">
                                    <ShieldCheckIcon :size="18" />
                                    {{ form.processing ? 'Saving...' : 'Deploy Policy' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ContextModuleLayout>
</template>
