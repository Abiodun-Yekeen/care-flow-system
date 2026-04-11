import { defineStore } from 'pinia'
import {metaApi} from "@/core/api/metaApi.js";

export const useMetaStore = defineStore('meta', {

    state: () => ({
        loaded: false,
        gender: [],
        title: [],
        state: [],
        lga: [],
        blood_group: [],
        genotype: [],
        marital_status: [],
        severity: [],
        relationship: [],
        department:[],
        encounter_type:[],
    }),

    actions: {

        async loadMeta() {

            if (this.loaded) return

            const res = await metaApi.getMeta()
            const data = res.data.data

            this.gender = data.gender
            this.title = data.title
            this.state = data.state
            this.lga = data.lga
            this.blood_group = data.blood_group
            this.genotype = data.genotype
            this.marital_status = data.marital_status
            this.severity = data.severity
            this.relationship = data.relationship
            this.department = data.department
            this.encounter_type = data.encounter_type

            this.loaded = true
        }




    }

})
