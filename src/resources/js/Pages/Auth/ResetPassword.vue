<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/forms/InputError.vue';
import InputLabel from '@/Components/forms/InputLabel.vue';
import PrimaryButton from '@/Components/forms/PrimaryButton.vue';
import TextInput from '@/Components/forms/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import FormInput from "@/Components/forms/FormInput.vue";
import LoadButton from "@/Components/ui/LoadButton.vue";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <form @submit.prevent="submit">
            <div>

                <FormInput
                    id="mobile_no"
                    type="number"
                    label="Mobile Number"
                    v-model="form.mobile_no"
                    required
                    disabled
                />
            </div>

            <div class="mt-4">

                <FormInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <FormInput
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

            </div>

            <div class="mt-4 flex items-center justify-end">
                <LoadButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                </LoadButton>
            </div>
        </form>
    </GuestLayout>
</template>
