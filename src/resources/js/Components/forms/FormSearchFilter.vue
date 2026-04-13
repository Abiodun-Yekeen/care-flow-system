<script setup>
import { Search, Filter } from "lucide-vue-next"

const props = defineProps({
    modelValue: String,
    placeholder: { type: String, default: 'Search...' }
})

const emit = defineEmits(['update:modelValue', 'search', 'filter'])
</script>

<template>
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="relative w-full md:w-96">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
            <input
                :value="modelValue"
                @input="emit('update:modelValue', $event.target.value)"
                @keyup.enter="emit('search')"
                type="text"
                :placeholder="placeholder"
                class="w-full pl-10 pr-4 py-2 bg-slate-50 border-slate-200 rounded-lg focus:ring-[#06A3DA] focus:border-[#06A3DA] text-sm transition-all"
            />
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <button
                @click="emit('filter')"
                class="flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 transition"
            >
                <Filter :size="16" />
                Filter
            </button>
        </div>
    </div>
</template>
