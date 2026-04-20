<script setup>
import {ref, computed, watch} from 'vue'
import { Head, router } from '@inertiajs/vue3'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue"
import PageHeader from "@/Components/layout/PageHeader.vue"
import ActionBar from "@/Components/forms/ActionBar.vue"
import DataTable from "@/Components/tables/DataTable.vue"
import { Building2, Mail, ShieldCheck, Phone, Fingerprint } from "lucide-vue-next"
import { UserActions } from "@/Pages/modules/admin/Composables/useActions.js"
import FormSearchFilter from "@/Components/forms/FormSearchFilter.vue";
import DataTableRow from "@/Components/tables/DataTableRow.vue";
import UserAvatarCell from "@/Components/tables/UserAvatarCell.vue";
import Badge from "@/Components/tables/Badge.vue";
import debounce from 'lodash/debounce' // Recommended to prevent too many server requests

const props = defineProps({
    users: Object,
    filters: Object
})

// --- SELECTION LOGIC ---
const selectedIds = ref([])

// Get the actual user object for the first selected item
const selectedUser = computed(() => {
    if (selectedIds.value.length !== 1) return null;
    return props.users.data.find(u => u.id === selectedIds.value[0]);

})

// Check/Uncheck all
const toggleAll = (e) => {
    selectedIds.value = e.target.checked ? props.users.data.map(u => u.id) : [];
}

const search = ref(props.filters?.search || '')

// Added a empty first column for the checkbox
const tableColumns = [
    { label: 'Fullname' },
    { label: 'Mobile No' },
    { label: 'Staff Id' },
    { label: 'Department' },
    { label: 'Roles' },
    { label: 'Status' },
]
const handleSearch = () => {
    router.get(route('users.index'), {
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
        <Head title="User Management" />
        <PageHeader title="Users" subtitle="Manage system access and roles." />

        <ActionBar :actions="UserActions(selectedUser)" />

        <div class="space-y-6 mt-6">
            <FormSearchFilter
                v-model="search"
                @keyup.enter="handleSearch"
                placeholder="Search name, mobile no or staf id..."
            />

            <DataTable
                v-model="selectedIds"
                :items="users.data"
                :columns="tableColumns"
                :pagination="users"
            >
                <template #body>
                    <DataTableRow
                        v-for="user in users.data"
                        :key="user.id"
                        :item-id="user.id"
                        v-model="selectedIds"
                    >
                        <td class="px-6 py-4">
                            <UserAvatarCell :name="user.name" :email="user.email" />
                        </td>

                        <td class="px-6 py-4 text-xs text-slate-600">
                            <span class="flex items-center gap-1"> {{ user.mobile_no }}</span>
                        </td>

                        <td class="px-6 py-4">
                        <span class=" items-center gap-1">
                            {{ user.staff_id }}
                        </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-700">
                         {{ user.primary_department?.name || 'Unassigned' }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                <Badge v-for="role in user.roles.slice(0, 2)" :key="role.id " variant="white">
                                    {{ role.name }}
                                </Badge>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <Badge variant="green">Active</Badge>
                        </td>
                    </DataTableRow>
                </template>
            </DataTable>
        </div>
    </ContextModuleLayout>
</template>
