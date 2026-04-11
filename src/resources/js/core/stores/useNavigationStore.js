import { defineStore } from 'pinia'

export const useNavigationStore = defineStore('navigation', {

    state: () => ({
        modules: [],
        sidebarModules: [],
        subNavigation: [],
        breadcrumb: []
    }),

    actions: {

        setModules(data) {
            this.modules = data
        },

        updateNavigation(props) {
            this.sidebarModules = props.navigation || []
            this.subNavigation = props.subNavigation || []
            this.breadcrumb = props.breadcrumb || []
        }

    }

})
