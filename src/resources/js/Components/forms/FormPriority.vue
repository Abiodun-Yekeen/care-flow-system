<script setup>
defineProps({
    modelValue: String, // Accepts the form.priority value
    error: String       // Accepts form.errors.priority
});

const emit = defineEmits(['update:modelValue']);

const setPriority = (value) => {
    emit('update:modelValue', value);
};
</script>

<template>
    <div class="col-span-12">
        <label class="block text-xs font-semibold text-gray-600 capitalize tracking-wider   mb-3 ">
            Submission Priority
        </label>

        <div class="grid grid-cols-3 gap-3">
            <button type="button" @click="setPriority('normal')"
                    :class="['flex flex-col items-center p-3 rounded-xl border-2 transition-all',
                modelValue === 'normal' ? 'border-blue-500 bg-blue-50' : 'border-slate-100 bg-white hover:border-slate-200']">
                <div :class="['w-2 h-2 rounded-full mb-2', modelValue === 'normal' ? 'bg-blue-500' : 'bg-slate-300']"></div>
                <span :class="['text-xs font-bold uppercase', modelValue === 'normal' ? 'text-blue-700' : 'text-slate-500']">normal</span>
            </button>

            <button type="button" @click="setPriority('urgent')"
                    :class="['flex flex-col items-center p-3 rounded-xl border-2 transition-all',
                modelValue === 'urgent' ? 'border-amber-500 bg-amber-50' : 'border-slate-100 bg-white hover:border-slate-200']">
                <div :class="['w-2 h-2 rounded-full mb-2', modelValue === 'urgent' ? 'bg-amber-500' : 'bg-slate-300']"></div>
                <span :class="['text-xs font-bold uppercase', modelValue === 'urgent' ? 'text-amber-700' : 'text-slate-500']">Urgent</span>
            </button>

            <button type="button" @click="setPriority('immediate')"
                    :class="['flex flex-col items-center p-3 rounded-xl border-2 transition-all',
                modelValue === 'immediate' ? 'border-red-500 bg-red-50' : 'border-slate-100 bg-white hover:border-slate-200']">
                <div :class="['w-2 h-2 rounded-full animate-pulse mb-2', modelValue === 'immediate' ? 'bg-red-500' : 'bg-slate-300']"></div>
                <span :class="['text-xs font-bold uppercase', modelValue === 'immediate' ? 'text-red-700' : 'text-slate-500']">Immediate</span>
            </button>
        </div>

        <div v-if="error" class="text-red-500 text-xs mt-2 font-medium">
            {{ error }}
        </div>
    </div>
</template>
