<script setup>

import {Bars3Icon} from '@heroicons/vue/24/outline'
import {Link, usePage} from '@inertiajs/vue3'
import { computed } from 'vue'
import GlobalSearch from "@/Components/layout/GlobalSearch.vue";
import Notification from "@/Components/layout/Notification.vue";

// Accessing Inertia page props to get authenticated user data
const page = usePage()

// Computed property to reactively track the logged-in user
const user = computed(() => page.props.auth.user)
const role = computed(() => page.props.auth.role)

// Defining emits so the parent layout can handle the sidebar state
const emit = defineEmits(['toggle-sidebar'])


</script>

<template>
    <header class="bg-secondary sticky top-0 z-50 text-white shadow-md">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex h-14 items-center justify-between gap-3">

                <div class="flex items-center gap-2 flex-shrink-0">
                    <button
                        @click="$emit('toggle-sidebar')"
                        class="rounded-md p-1.5 hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-white/50"
                        aria-label="Toggle Sidebar"
                    >
                        <Bars3Icon class="size-6" />
                    </button>

                    <span class="font-bold text-sm sm:text-lg tracking-tight">FMC-EKITI</span>
                </div>

                <GlobalSearch/>

                <Notification :name="user.name" :role="role"/>

            </div>
        </div>
    </header>
</template>

