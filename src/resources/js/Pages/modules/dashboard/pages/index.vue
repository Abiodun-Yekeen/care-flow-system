<script setup>

import {Head, usePage} from "@inertiajs/vue3";
import DashboardHeader from "@/Pages/modules/dashboard/components/DashboardHeader.vue";
import StatsCard from "@/Pages/modules/dashboard/components/StatsCard.vue";
import QuickActionsPanel from "@/Pages/modules/dashboard/components/QuickActionsPanel.vue";
import ActivityFeed from "@/Pages/modules/dashboard/components/ActivityFeed.vue";
import AnnouncementsPanel from "@/Pages/modules/dashboard/components/AnnouncementsPanel.vue";
import WorkloadPanel from "@/Pages/modules/dashboard/components/WorkloadPanel.vue";
import BreadCrumb from "@/Components/layout/BreadCrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const user = usePage().props.auth.user;
const navigation =  usePage().props.navigation;
// Dummy data (replace with API later)
const summary = {
    active: 248,
    pending: 36,
    completed: 18,
    overdue: 9,
}

const myWork = {
    assigned: 12,
    awaiting: 7,
    submitted: 21,
    returned: 3,
}

const activities = [
    { text: 'File routed to Finance Department', time: '10 mins ago' },
    { text: 'Temporary file created', time: '25 mins ago' },
    { text: 'File returned to HOD', time: '1 hr ago' },
]

const workload = [
    { name: 'Finance', count: 24 },
    { name: 'HR', count: 18 },
    { name: 'Admin', count: 12 },
]
</script>


<template>
    <Head title="Dashboard" />


    <AuthenticatedLayout>
        <BreadCrumb/>
    <div class="min-h-screen bg-white p-4 sm:p-6">
        <div class="mx-auto max-w-7xl space-y-6">

            <!-- Header -->
            <DashboardHeader :name="user.name"  :role="user.role_name"/>

            <!-- Global Summary -->
            <section class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <StatsCard title="Active Files" :value="summary.active" />
                <StatsCard title="Pending Actions" :value="summary.pending" />
                <StatsCard title="Completed Today" :value="summary.completed" />
                <StatsCard title="Overdue Files" :value="summary.overdue" />
            </section>

            <!-- My Work -->
            <section class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <StatsCard title="Assigned to Me" :value="myWork.assigned" />
                <StatsCard title="Awaiting My Action" :value="myWork.awaiting" />
                <StatsCard title="Submitted by Me" :value="myWork.submitted" />
                <StatsCard title="Returned to Me" :value="myWork.returned" />
            </section>

            <!-- Middle -->
            <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <QuickActionsPanel />
                <AnnouncementsPanel />
            </section>

            <!-- Bottom -->
            <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <ActivityFeed :items="activities" />
                <WorkloadPanel :departments="workload" />
            </section>

        </div>
    </div>
    </AuthenticatedLayout>

</template>

