<script setup>
import Checkbox from '@/Components/forms/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import FormInput from "@/Components/forms/FormInput.vue";
import { registerForPush } from "@/core/services/push"; // push Notification firebase

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    mobile_no: '',
    password: '',
    remember: false,
});

const submit = async () => {
    form.post(route('login'), {
        onSuccess: async () => {
            // only run AFTER login finishes
            await registerForPush();
        },
        onFinish: async () => {
            form.reset('password');
        }
    });
};
</script>





<template>
<GuestLayout>
    <Head title="Login" />
        <h2 class="mt-8 text-2xl font-semibold tracking-tight text-gray-900">
          Sign in to your account
        </h2>


        <!-- Form -->
        <form class="mt-8 space-y-6" @submit.prevent="submit">
              <FormInput
                  id="mobile_no"
                  v-model="form.mobile_no"
                  label="Mobile Number"
                  required
                  :error="form.errors.mobile_no"
              />

            <FormInput
                id="password"
                type="password"
                v-model="form.password"
                label="Password"
                required
                :error="form.errors.password"
            />

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600">
              <input
                v-model="form.remember"
                type="checkbox"
                class="rounded border-gray-300 text-hospital focus:ring-primary"
              />
              Remember me
            </label>

             <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline
                     hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>
          </div>

          <button
            type="submit"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            class="flex w-full justify-center rounded-md
             bg-primary px-4 py-2 text-sm font-semibold text-white
              hover:bg-primary-500 focus:outline-none focus:ring-2
               focus:ring-primary-600 focus:ring-offset-2 disabled:opacity-50"
          >
            {{ form.processing ? 'Signing in…' : 'Sign in' }}
          </button>


           <!-- <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton> -->
        </form>

</GuestLayout>

</template>




<!--







<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>



-->











