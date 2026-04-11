export async function submitForm(request, form) {

    try {
        const res = await request()
        return res

    } catch (errors) {
        if (typeof errors === "object") {
            form.clearErrors()
            Object.entries(errors).forEach(([field, messages]) => {
                form.setError(field, messages[0])
            })
            return null
        }

        throw errors
    }

}
