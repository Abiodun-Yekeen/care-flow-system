import axios from "axios"


import {useNotificationStore} from "@/core/stores/useNotificationStore.js";
import {useUiStore} from "@/core/stores/uiStore.js";

const apiClient = axios.create({
    baseURL: "/",
    withCredentials: true,
    headers: {
        "X-Requested-With": "XMLHttpRequest"
    }
})

/*
Request interceptor
*/
apiClient.interceptors.request.use((config) => {

    const ui = useUiStore()
    ui.startLoading()

    const token = document.querySelector('meta[name="csrf-token"]')

    if (token) {
        config.headers["X-CSRF-TOKEN"] = token.content
    }

    return config
})

/*
Response interceptor
*/
apiClient.interceptors.response.use(

    (response) => {
        const ui = useUiStore()
        ui.stopLoading()
        return response
    },

    (error) => {

        const notify = useNotificationStore()
        const ui = useUiStore()

        ui.stopLoading()

        if (error.response) {

            const status = error.response.status

            // Validation errors → just return them
            if (status === 422) {
                return Promise.reject(error.response.data.errors)
            }

            switch (status) {

                case 419 || 401:
                    window.location.href = '/login';
                    break

                case 403:
                    notify.error("Permission denied")
                    break

                case 404:
                    notify.error("Resource not found")
                    break

                case 500:
                    notify.error("Server error occurred")
                    break

                default:
                    notify.error("Something went wrong")
            }

        } else {
            notify.error("Network error")
        }

        return Promise.reject(error)

    }

)
export default apiClient
