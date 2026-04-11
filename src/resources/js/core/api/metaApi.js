import ApiService from "@/core/services/apiService.js";

export const metaApi = {

    getMeta() {
        return ApiService.get(route('api.load-metadata'))
    },

    getSessionRefresh(){
        return ApiService.get(route('api.refresh-session'))
    }

}
