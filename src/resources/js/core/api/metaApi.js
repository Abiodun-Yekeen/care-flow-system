import ApiService from "@/core/services/apiService.js";
import axios from "axios";

export const metaApi = {


    getMeta() {
        return ApiService.get(route('api.load-metadata'))
    },

    getSessionRefresh(){
        return ApiService.get(route('api.refresh-session'))
    },

    getFileToMerge(data){
        return ApiService.post(route('files.search-merge-file'),data)
    }

}
