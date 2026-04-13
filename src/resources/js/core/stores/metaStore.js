import { defineStore } from 'pinia'
import {metaApi} from "@/core/api/metaApi.js";

export const useMetaStore = defineStore('meta', {

    state: () => ({
        loaded: false,
        department:[],
        role:[],
    }),

    actions: {

        async loadMeta() {

            if (this.loaded) return

            const res = await metaApi.getMeta()
            const data = res.data.data
            this.department = data.departments
            this.role = data.roles

            this.loaded = true
        }




    }

})
