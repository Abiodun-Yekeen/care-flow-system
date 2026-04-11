import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import {useAuthStore} from "@/core/stores/authStore.js";
import {useNavigationStore} from "@/core/stores/useNavigationStore.js";


export function useInertiaSync() {
    const page = usePage()


    const sync = () => {
        const authStore = useAuthStore()
        const navStore = useNavigationStore()
        watch(() => page.props, (newProps) => {
            // Check if newProps actually exists first
            if (!newProps) return;
            // Sync Auth & Permissions
            if (newProps.auth) {
                authStore.setAuth({
                    user: newProps.auth.user,
                    role: newProps.auth.role,
                    // Only update permissions if they are actually sent (Inertia lazy props)
                    permissions: newProps.permissions || authStore.permissions
                })
            }

            //  Sync Navigation & Layout State
            navStore.updateNavigation({
                navigation: newProps.navigation,
                subNavigation: newProps.subNavigation,
                breadcrumb: newProps.breadcrumb
            })
        }, { immediate: true, deep: true })
    }

    return { sync }
}
