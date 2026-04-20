
<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import PageHeader from "@/Components/layout/PageHeader.vue";
import { XIcon, ShieldCheckIcon, BuildingIcon, UserIcon } from 'lucide-vue-next';
import LoadButton from "@/Components/ui/LoadButton.vue";

const props = defineProps({
    user: Object,
    roles: Array,
    effectiveAccess: Array,
})

const roleForm = useForm({
    role_ids: [],
})
    const assignRoles = () => {
    roleForm.post(route('users.roles.assign', props.user.id), {
        preserveScroll: true,
        onSuccess: () => roleForm.reset(),
    })
}

const removeRole = (roleId) => {
    if (!confirm('Are you sure you want to revoke this role?')) return

    roleForm.delete(route('users.roles.remove', [props.user.id, roleId]), {
        preserveScroll: true,
    })
}
</script>

<template>
    <ContextModuleLayout>
        <Head :title="'Security Profile: ' + props.user.name" />

        <div class="flex flex-col h-full bg-slate-50">
            <PageHeader
                :title="props.user.name"
                subtitle="IAM Security Profile & Effective Permissions"
            >
                <template #actions>
                    <Link :href="route('users.edit', user.id)" class="px-4 py-2 bg-white border rounded-lg text-sm font-bold shadow-sm hover:bg-slate-50">
                        Edit Profile
                    </Link>
                </template>
            </PageHeader>

            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl border p-5 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 text-slate-800 font-bold">
                            <UserIcon :size="18" class="text-slate-400" />
                            Account Identity
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between border-b pb-2"><span class="text-slate-500">Email:</span> <span class="font-medium">{{ user.email }}</span></div>
                            <div class="flex justify-between border-b pb-2"><span class="text-slate-500">Staff ID:</span> <span class="font-medium">{{ user.staff_id }}</span></div>
                            <div class="flex justify-between pb-1"><span class="text-slate-500">Mobile:</span> <span class="font-medium">{{ user.mobile_no }}</span></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border p-5 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 text-slate-800 font-bold">
                            <BuildingIcon :size="18" class="text-slate-400" />
                            Departmental Scope
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="dept in user.departments" :key="dept.id"
                                  class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold border border-blue-100">
                                {{ dept.name }}
                            </span>
                            <span v-if="!user.departments?.length" class="text-slate-400 italic text-sm">No departments assigned.</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-4 leading-tight">
                            * Permissions are automatically scoped to these departments via ARN resolution.
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border shadow-sm">
                    <div class="p-5 border-b flex items-center gap-2 font-bold text-slate-800">
                        <ShieldCheckIcon :size="18" class="text-slate-400" />
                        Role Management
                    </div>
                    <div class="p-6">
                        <div class="mb-6 flex flex-wrap gap-2">
                            <div v-for="role in user.roles" :key="role.id"
                                 class="flex items-center gap-2 rounded-lg bg-slate-100 border border-slate-200 px-3 py-1.5"
                            >
                                <span class="text-sm font-medium text-slate-700">{{ role.name }}</span>
                                <button class="text-slate-400 hover:text-red-600 transition" @click="removeRole(role.id)">
                                    <XIcon :size="14" />
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-3">
                                <select v-model="roleForm.role_ids" multiple class="min-h-[120px] w-full rounded-lg border-slate-300 text-sm">
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.display_name || role.name }}
                                    </option>
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1 italic">Hold Ctrl/Cmd to select multiple roles.</p>
                            </div>
                            <div class="flex items-end">
                                <LoadButton
                                    class="w-full h-10 rounded-lg bg-[#06A3DA] px-4 text-white font-bold text-sm shadow-sm hover:bg-[#058dc0] transition disabled:opacity-50"
                                    @click="assignRoles"
                                    :disabled="roleForm.role_ids.length === 0 || roleForm.processing"
                                >
                                    {{ roleForm.processing ? 'Syncing...' : 'Assign Roles' }}
                                </LoadButton>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
                    <div class="p-5 border-b bg-slate-50/50 flex justify-between items-center">
                        <h2 class="font-bold text-slate-800">Effective Access Matrix</h2>
                        <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Live Policy Resolution</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase">Resource ARN</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase">Action</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase">Result</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-500 uppercase">Logic Source</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                            <template v-for="(actions, moduleName) in effectiveAccess" :key="moduleName">
                                <tr v-for="action in actions" :key="moduleName + action" class="hover:bg-slate-50/50 transition">

                                    <td class="px-5 py-4 text-xs font-mono text-blue-600">
                                        {{ moduleName }}
                                    </td>

                                    <td class="px-5 py-4 text-sm font-semibold text-slate-700">
                                        {{ action }}
                                    </td>

                                    <td class="px-5 py-4">
                                    <span class="bg-green-50 text-green-700 border-green-100 rounded px-2 py-0.5 text-[10px] font-bold uppercase border">
                                        allowed
                                    </span>
                                    </td>

                                    <td class="px-5 py-4 text-xs text-slate-400 italic">
                                        Computed via IamService
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="!effectiveAccess?.length">
                                <td colspan="4" class="p-10 text-center text-slate-400 text-sm">No effective permissions found for this user profile.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </ContextModuleLayout>
</template>
