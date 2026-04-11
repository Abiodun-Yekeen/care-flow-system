<script setup>
import {usePage, router, Link} from '@inertiajs/vue3'
import {useNavigationStore} from "@/core/stores/useNavigationStore.js";
import {storeToRefs} from "pinia";
const navStore = useNavigationStore()
const {breadcrumb} = storeToRefs(navStore)
</script>

<template>
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
        <template v-for="(item, index) in breadcrumb" :key="item.route">
            <Link :href="item.route" class="hover:underline">
                {{ item.label }}
            </Link>
            <span v-if="index < breadcrumb.length - 1">
            >
        </span>
        </template>
    </div>
</template>

<style scoped>

</style>
