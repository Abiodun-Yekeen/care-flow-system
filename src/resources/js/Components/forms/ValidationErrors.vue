<!-- resources/js/Components/Form/ValidationErrors.vue -->
<script setup>
import { AlertCircle, X } from "lucide-vue-next"
import { ref, computed } from 'vue'

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({})
    }
})

const show = ref(true)

const errorCount = computed(() => Object.keys(props.errors).length)
</script>

<template>


    <div v-if="errorCount > 0 && show"
         class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <AlertCircle class="h-5 w-5 text-red-400" />
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-bold text-red-800">
                    There {{ errorCount === 1 ? 'was' : 'were' }} {{ errorCount }} {{ errorCount === 1 ? 'error' : 'errors' }} with your submission
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li v-for="(error, field) in errors" :key="field">
<!--                            <span class="font-medium">{{ field }}:</span>-->
                            {{ Array.isArray(error) ? error[0] : error }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button @click="show = false"
                        class="inline-flex text-red-400 hover:text-red-600">
                    <X class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</template>
