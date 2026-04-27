import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useAuthStore } from "@/core/stores/authStore.js";
import { useNavigationStore } from "@/core/stores/useNavigationStore.js";
import { useNotificationStore } from "@/core/stores/useNotificationStore.js";
import {useMetaStore} from "@/core/stores/metaStore.js";

export function useInertiaSync() {
    // DO NOT call usePage() or useStore() here at the top level

    let isInitialized = false;

    const sync = () => {
        // CALL THEM HERE: Inside the function called by setup()
        const page = usePage();
        const authStore = useAuthStore();
        const navStore = useNavigationStore();
        const notify = useNotificationStore();
        const meta = useMetaStore();
        watch(
            [() => page.props, () => page.url],
            async ([newProps, newUrl]) => {
                if (!newProps) return;

                // Sync Auth
                if (newProps.auth?.user) {
                    authStore.setAuth({
                        user: newProps.auth.user,
                        role: newProps.auth.role,
                        permissions: newProps.permissions || authStore.permissions
                    });

                    // Initialize Notifications (Only once)
                    if (!isInitialized) {
                        // Load Meta Data (Departments, Roles)
                        await meta.loadMeta();
                        notify.fetchNotifications();
                        notify.listenForNotifications(newProps.auth.user.id);
                        notify.listenForPushNotifications();
                        isInitialized = true;
                    }
                }

                // Sync Navigation
                if (newUrl) {
                    navStore.updateNavigation({
                        navigation: newProps.navigation,
                        breadcrumb: newProps.breadcrumb
                    }, newUrl);
                }
            },
            { immediate: true, deep: true }
        );
    };

    return { sync };
}
