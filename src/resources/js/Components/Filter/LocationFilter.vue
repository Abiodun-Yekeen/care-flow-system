<!-- resources/js/Components/Filter/LocationFilter.vue -->
<script setup>
import { ref, computed } from 'vue'
import { Search } from "lucide-vue-next"

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    locations: {
        type: Array,
        default: () => [
            { value: 'ward-a', label: 'Ward A', count: 45 },
            { value: 'ward-b', label: 'Ward B', count: 52 },
            { value: 'icu', label: 'ICU', count: 12 },
            { value: 'step-down', label: 'Step Down Unit', count: 23 },
            { value: 'ed', label: 'Emergency Dept', count: 34 }
        ]
    }
})

const emit = defineEmits(['update:modelValue'])
const searchTerm = ref('')

const filteredLocations = computed(() => {
    if (!searchTerm.value) return props.locations
    return props.locations.filter(l =>
        l.label.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
})

const toggleLocation = (value) => {
    const newValue = props.modelValue.includes(value)
        ? props.modelValue.filter(v => v !== value)
        : [...props.modelValue, value]
    emit('update:modelValue', newValue)
}
</script>

<template>
    <div class="space-y-2">
        <div class="relative">
            <Search class="w-3 h-3 absolute left-2 top-2 text-gray-400" />
            <input type="text"
                   v-model="searchTerm"
                   placeholder="Filter locations..."
                   class="w-full border rounded pl-7 pr-2 py-1 text-xs focus:ring-2 ring-blue-500 outline-none" />
        </div>
        <div class="space-y-1 max-h-40 overflow-y-auto">
            <div v-for="location in filteredLocations"
                 :key="location.value"
                 @click="toggleLocation(location.value)"
                 :class="[
                     'flex justify-between items-center p-2 rounded cursor-pointer text-sm',
                     modelValue.includes(location.value) ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50'
                 ]">
                <span>{{ location.label }}</span>
                <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">{{ location.count }}</span>
            </div>
            <div v-if="!filteredLocations.length" class="text-xs text-gray-400 text-center py-2">
                No locations found
            </div>
        </div>
    </div>
</template>
