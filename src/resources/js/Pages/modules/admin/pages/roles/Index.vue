<script setup>

import {ref, computed, watch} from 'vue'
import { Head, router } from '@inertiajs/vue3'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import PageHeader from "@/Components/layout/PageHeader.vue"
import ActionBar from "@/Components/forms/ActionBar.vue"
import DataTable from "@/Components/tables/DataTable.vue"
import { Building2, Mail, ShieldCheck, Phone, Fingerprint } from "lucide-vue-next"
import {RoleActions, UserActions} from "@/Pages/modules/admin/Composables/useActions.js"
import FormSearchFilter from "@/Components/forms/FormSearchFilter.vue";
import DataTableRow from "@/Components/tables/DataTableRow.vue";
import UserAvatarCell from "@/Components/tables/UserAvatarCell.vue";
import Badge from "@/Components/tables/Badge.vue";
import debounce from 'lodash/debounce' // Recommended to prevent too many server requests

const props = defineProps({
    roles: Object,
    filters: Object
})

// --- SELECTION LOGIC ---
const selectedIds = ref([])

// Get the actual user object for the first selected item
const selectedRole = computed(() => {
    if (selectedIds.value.length !== 1) return null;
    return props.roles.data.find(u => u.id === selectedIds.value[0]);

})

// Check/Uncheck all
const toggleAll = (e) => {
    selectedIds.value = e.target.checked ? props.roles.data.map(u => u.id) : [];
}

const search = ref(props.filters?.search || '')

// Added a empty first column for the checkbox
const tableColumns = [
    { label: 'Role Name' },
    { label: 'Display Name' },
    { label: 'Policies' },
    { label: 'Parents' },
    { label: 'Users' },
    { label: 'Created At' },
]

const handleSearch = () => {
    router.get(route('roles.index'), {
        search: search.value
    }, {
        preserveState: true, // Crucial: keeps your checkboxes selected
        replace: true,       // Keeps browser history clean
        preserveScroll: true // Prevents the page from jumping to top
    })
}

    // watch the search variable
    watch(search, debounce((value) => {
        handleSearch()
    }, 500))


</script>

<template>
    <ContextModuleLayout>
        <Head title="Roles Management" />
        <PageHeader title="Roles" subtitle="Manage system access and roles." />

        <ActionBar :actions="RoleActions(selectedRole)" />

        <div class="space-y-6 mt-6">
            <FormSearchFilter
                v-model="search"
                @keyup.enter="handleSearch"
                placeholder="Search name ..."
            />

            <DataTable
                v-model="selectedIds"
                :items="roles.data"
                :columns="tableColumns"
                :pagination="roles"
            >
                <template #body>
                    <DataTableRow
                        v-for="role in roles.data"
                        :key="role.id"
                        :item-id="role.id"
                        v-model="selectedIds"
                    >
                        <td class="px-6 py-4">
                            <UserAvatarCell :name="role.name"  />
                        </td>

                        <td class="px-6 py-4 text-xs text-slate-600">
                            <span class="flex items-center gap-1"> {{ role.display_name }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ role.policies_count }}
                        </td>
                                <td>
                                <span v-for="parent in role.parents" :key="parent.id" class="rounded bg-gray-100 px-2 py-1 text-sm">
                                 {{ parent.name }}
                                </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                          {{ role.users_count }}
                    </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ role.created_at }}
                        </td>

                    </DataTableRow>
                </template>
            </DataTable>
        </div>
    </ContextModuleLayout>
</template>

