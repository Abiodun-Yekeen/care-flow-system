<script setup>
import ApplicationLogo from '../assests/logo.jpeg'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen flex bg-white">
    <div class="flex flex-1 flex-col justify-center px-6 py-12 lg:px-20 xl:px-24 bg-white">
      <div class="mx-auto w-full max-w-sm">

        <!-- Logo -->
        <div class="flex items-center gap-3 mb-6">
          <img
            :src="ApplicationLogo"
            alt="Federal Medical Centre Ikole-Ekiti"
            class="h-10 w-auto"
          />
          <span class="text-lg font-semibold text-gray-900">
            CareFlow System
          </span>
        </div>


 <slot />



      </div>
    </div>
  </div>
</template>
