import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function usePermissions() {

    const page = usePage()

    const permissions = computed(() => page.props.permissions ?? {})

    const can = (module, action = 'view') => {
        const perms = permissions.value

        if (!perms) return false

        // Global wildcard (Developer / Super Admin)
        if (perms['*']?.includes('*')) return true

        //  Module wildcard
        if (perms[module]?.includes('*')) return true

        // Specific action
        return perms[module]?.includes(action) ?? false
    }

    return { can }
}


