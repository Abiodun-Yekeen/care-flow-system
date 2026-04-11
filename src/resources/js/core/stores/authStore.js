import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        role: null,
        permissions: {} // The "Permission Matrix"
    }),

    getters: {
        isAuthenticated: (state) => !!state.user,
        // EMR Check: can(patient.create)
        can: (state) => (permission) => {
            const [module, action] = permission.split('.')
            return state.permissions[module]?.includes(action) || state.role === 'super-admin'
        }
    },

    actions: {
        setAuth(data) {
            this.user = data.user
            this.role = data.role
            this.permissions = data.permissions || {}
        }
    }
})
