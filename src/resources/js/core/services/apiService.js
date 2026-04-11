import apiClient from "./apiClient"

class ApiService {

    get(url, params = {}) {
        return apiClient.get(url, { params })
    }

    post(url, data = {}) {
        return apiClient.post(url, data)
    }

    put(url, data = {}) {
        return apiClient.put(url, data)
    }

    patch(url, data = {}) {
        return apiClient.patch(url, data)
    }

    delete(url) {
        return apiClient.delete(url)
    }

}

export default new ApiService()
