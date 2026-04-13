<script setup>
import {computed} from "vue";

const props = defineProps({
    itemId: { type: [Number, String], required: true },
    modelValue: { type: Array, required: true }, // The selectedIds array
    columns: { type: Number, default: 1 }      // For colspan if needed
})

const emit = defineEmits(['update:modelValue'])

// Check if this specific row is selected
const isSelected = computed(() => props.modelValue.includes(props.itemId))

const toggleSelection = () => {
    let updatedSelection = [...props.modelValue]
    if (isSelected.value) {
        updatedSelection = updatedSelection.filter(id => id !== props.itemId)
    } else {
        updatedSelection.push(props.itemId)
    }
    emit('update:modelValue', updatedSelection)
}
</script>

<template>
    <tr
        :class="{'bg-blue-50/50': isSelected}"
        class="hover:bg-slate-50/50 transition border-b border-slate-100 group"
    >
        <td class="px-6 py-4 w-10">
            <input
                type="checkbox"
                :checked="isSelected"
                @change="toggleSelection"
                class="rounded border-slate-300 text-[#06A3DA] focus:ring-[#06A3DA] cursor-pointer"
            />
        </td>

        <slot />
    </tr>
</template>
