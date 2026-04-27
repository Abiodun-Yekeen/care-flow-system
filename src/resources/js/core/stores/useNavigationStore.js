import { defineStore } from 'pinia'

export const useNavigationStore = defineStore('navigation', {

    state: () => ({
        sidebarModules: [], // This holds the main structure from Laravel
        subNavigation: [],  // This holds children of the active module
        breadcrumb: []
    }),

    actions: {
        updateNavigation(props, currentUrl) {
            this.sidebarModules = props.navigation || [];
            this.breadcrumb = props.breadcrumb || [];

            if (!currentUrl || typeof currentUrl !== 'string') return;

            const segments = currentUrl.split('/').filter(Boolean);
            let activeModuleKey = segments[0];

            // Find module (handling hyphen vs underscore mismatch)
            const activeModule = this.sidebarModules.find(m => {
                // Check exact match OR match where underscores are replaced with hyphens
                return m.key === activeModuleKey || m.key.replace(/_/g, '-') === activeModuleKey;
            });

            // Set sub-navigation
            this.subNavigation = activeModule ? activeModule.children : [];


        },

        // Use this name to match your AuthenticatedLayout call
        setModules(data) {
            this.sidebarModules = data;
        }
    }
})
