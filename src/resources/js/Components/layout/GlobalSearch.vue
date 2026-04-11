<script setup>

import {MagnifyingGlassIcon} from "@heroicons/vue/24/outline";
</script>

<template>
    <div class="relative hidden md:block flex-1 max-w-md mx-4">
        <MagnifyingGlassIcon
            class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-gray-400"
        />
        <input
            type="search"
            placeholder="Search ..."
            class="w-full rounded-lg border-none bg-white pl-10 pr-4 py-1.5 text-sm text-black placeholder-black/70 focus:bg-white focus:text-gray-900 focus:ring-0 transition-all"
        />
    </div>
</template>


