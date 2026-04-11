

<script setup>
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { HelpCircle } from "lucide-vue-next";
import {iconRegistry} from "@/config/Icon.js";
import { storeToRefs } from "pinia"
import { useNavigationStore } from "@/core/stores/useNavigationStore.js"
import {Link} from "@inertiajs/vue3";




const navigationStore = useNavigationStore()
const { modules } = storeToRefs(navigationStore)

defineProps({
    open: Boolean,
})
defineEmits(['close'])

</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-40" @close="$emit('close')">

            <TransitionChild
                as="template"
                enter="transition-opacity duration-200"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="transition-opacity duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/30 sm:hidden" />
            </TransitionChild>

            <div class="fixed inset-x-0 top-[56px] flex">
                <TransitionChild
                    as="template"
                    enter="transition transform duration-300"
                    enter-from="-translate-x-full"
                    enter-to="translate-x-0"
                    leave="transition transform duration-300"
                    leave-from="translate-x-0"
                    leave-to="-translate-x-full"
                >
                    <DialogPanel
                        class="w-64 bg-white border-r border-gray-200 min-h-[calc(100vh-56px)] p-4 shadow-xl"
                    >
                        <div class="flex items-center justify-between mb-6 sm:hidden">
                            <span class="font-bold text-gray-900">Hospital Modules</span>
                            <button @click="$emit('close')">
                                <XMarkIcon class="size-5 text-gray-500" />
                            </button>
                        </div>

                        <nav class="space-y-2">
                            <Link
                                v-for="item in modules"
                                :key="item.key"
                                :href="item.route"
                                class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-indigo-50 hover:text-secondary"
                            >
                                <component
                                    :is="iconRegistry[item.icon] || HelpCircle"
                                    class="mr-3 size-5 text-gray-400 group-hover:text-secondary"
                                />
                                {{ item.label }}
                            </Link>
                        </nav>
                    </DialogPanel>
                </TransitionChild>
            </div>

        </Dialog>
    </TransitionRoot>
</template>
