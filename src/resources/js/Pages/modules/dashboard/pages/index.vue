<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BreadCrumb from "@/Components/layout/BreadCrumb.vue";
import DashboardHeader from "@/Pages/modules/dashboard/components/DashboardHeader.vue";
import StatsCard from "@/Pages/modules/dashboard/components/StatsCard.vue";
import QuickActionsPanel from "@/Pages/modules/dashboard/components/QuickActionsPanel.vue";
import ActivityFeed from "@/Pages/modules/dashboard/components/ActivityFeed.vue";
import WorkloadPanel from "@/Pages/modules/dashboard/components/WorkloadPanel.vue";
import AnnouncementsPanel from "@/Pages/modules/dashboard/components/AnnouncementsPanel.vue";
// ... (your existing imports)

const props = defineProps({
    // Global counts
    summary: {
        type: Object,
        default: () => ({ active: 0, pending: 0, completed: 0, overdue: 0 })
    },
    // Activity Feed from FileMovements
    activities: {
        type: Array,
        default: () => []
    },
    // Workload data by department
    workload: {
        type: Array,
        default: () => []
    }
});

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <BreadCrumb/>
        <div class="min-h-screen bg-white p-4 sm:p-6">
            <div class="mx-auto max-w-7xl space-y-6">

                <!-- Header -->
                <DashboardHeader :name="user.name" :role="user.role_name"/>

                <!-- Global Summary (Real Data) -->
                <section class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <StatsCard title="Active Files" :value="summary.active" />
                    <StatsCard title="Pending Actions" :value="summary.pending" />
                    <StatsCard title="Completed Today" :value="summary.completed" />
                    <StatsCard title="Overdue Files" :value="summary.overdue" />
                </section>

                <!-- Middle -->
                <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <QuickActionsPanel />
                    <AnnouncementsPanel />
                </section>
                <!-- Bottom -->
                <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                    <!-- maps the 'text' property to our activity 'description' -->
                    <ActivityFeed :items="activities" />
                    <WorkloadPanel :departments="workload" />
                </section>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
