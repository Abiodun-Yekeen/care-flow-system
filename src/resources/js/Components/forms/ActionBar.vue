
<script setup>
import { computed } from 'vue'
import { usePermissions } from '@/Composables/auth/usePermissions.js'
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    actions: {
        type: Array,
        required: true
    }
})

const { can } = usePermissions()

const visibleActions = computed(() => {
    return props.actions.filter(action => {

        if (!action.permission) return true

        const { module, action: act } = action.permission

        return can(module, act)
    })
})
</script>

<template>
    <div
        v-if="visibleActions.length > 0"
        class="flex items-center space-x-6 p-3 bg-white border-b border-gray-200"
    >
        <Link
            v-for="action in visibleActions"
            :key="action.label"
            @click="action.onClick"
            preserve-state
            preserve-scroll
            class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-primary  transition-colors group"
        >
            <component
                :is="action.icon"
                v-if="action.icon"
                class="w-4 h-4 text-gray-400 group-hover:text-primary"
            />
            <span>{{ action.label }}</span>
        </Link>
    </div>
</template>
