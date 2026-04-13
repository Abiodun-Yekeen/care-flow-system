<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3' // Import Link for SPA navigation

const props = defineProps({
    items: { type: Array, default: () => [] },
    columns: Array,
    modelValue: Array,
    pagination: Object // The full paginator object from Laravel
})

const emit = defineEmits(['update:modelValue'])

const isAllSelected = computed({
    get: () => props.items.length > 0 && props.modelValue.length === props.items.length,
    set: (value) => {
        emit('update:modelValue', value ? props.items.map(item => item.id) : [])
    }
})
</script>

<template>
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 w-10">
                        <input
                            type="checkbox"
                            v-model="isAllSelected"
                            class="rounded border-slate-300 text-[#06A3DA] focus:ring-[#06A3DA] cursor-pointer"
                        />
                    </th>
                    <th v-for="col in columns" :key="col.label" class="px-6 py-4">
                        {{ col.label }}
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                <slot name="body" />

                <tr v-if="items.length === 0">
                    <td :colspan="columns.length + 1" class="px-6 py-10 text-center text-slate-400">
                        No records found.
                    </td>
                </tr>
                </tbody>
            </table>

            <div
                v-if="pagination && pagination.total > 0"
                class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between"
            >
                <div class="text-xs text-slate-500">
                    Showing <b>{{ pagination.from || 0 }}</b> to <b>{{ pagination.to || 0 }}</b> of <b>{{ pagination.total }}</b> results
                </div>

                <div v-if="pagination.links && pagination.links.length > 3" class="flex items-center gap-1">
                    <template v-for="(link, k) in pagination.links" :key="k">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1 text-xs font-bold rounded border transition-all"
                            :class="link.active
                    ? 'bg-[#06A3DA] text-white border-[#06A3DA]'
                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                            preserve-scroll
                        />
                        <span
                            v-else
                            v-html="link.label"
                            class="px-6 py-1 text-xs text-slate-400 border border-slate-100 cursor-not-allowed"
                        />
                    </template>
                </div>

            </div>
        </div>
    </div>
</template>
