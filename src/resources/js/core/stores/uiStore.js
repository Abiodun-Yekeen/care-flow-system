import {defineStore} from "pinia";

export const useUiStore = defineStore('ui', {

    state: () => ({
        sidebarOpen: true,
        loading: false
    }),

    actions: {

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen
        },

        // Loading actions
        startLoading() {
            this.loading = true
        },

        stopLoading() {
            this.loading = false
        },

        withLoading: async (callback) => {
            this.startLoading()
            try {
                await callback()
            } finally {
                this.stopLoading()
            }
        }

    }

})
