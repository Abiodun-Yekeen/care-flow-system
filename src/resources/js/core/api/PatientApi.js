import ApiService from "@/core/services/apiService.js";

export const PatientApi = {

    savePatientData(data) {
        return ApiService.post(route('api.patient.store'), data)
    }

}
