import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function usePermissions() {
    const page = usePage()

    // Grab the permissions object from props
    const perms = computed(() => page.props.permissions ?? {})

    const can = (permissionName) => {
        return Object.values(perms.value).flat().includes(permissionName)
    }

    return { can }
}
