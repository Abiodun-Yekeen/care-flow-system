<script setup>
import { PlusIcon, TrashIcon, ShieldAlertIcon } from 'lucide-vue-next';

const props = defineProps({
    modelValue: Object // Structure: { version: string, statements: [] }
});
const emit = defineEmits(['update:modelValue']);

const addStatement = () => {
    const newValue = JSON.parse(JSON.stringify(props.modelValue));
    newValue.statements.push({
        sid: 'Stmt' + (newValue.statements.length + 1),
        effect: 'allow',
        actions: ['*'],
        resources: ['*']
    });
    emit('update:modelValue', newValue);
};

const removeStatement = (index) => {
    const newValue = JSON.parse(JSON.stringify(props.modelValue));
    newValue.statements.splice(index, 1);
    emit('update:modelValue', newValue);
};

const updateField = (index, field, value) => {
    const newValue = JSON.parse(JSON.stringify(props.modelValue));
    newValue.statements[index][field] = value.split(',')
        .map(s => s.trim())
        .filter(s => s !== '');
    emit('update:modelValue', newValue);
};
</script>

<template>
    <div class="space-y-4">
        <div v-for="(stmt, index) in modelValue?.statements || []" :key="index"
             class="p-5 border rounded-xl bg-slate-50 relative group transition hover:border-blue-200">

            <button @click="removeStatement(index)"
                    class="absolute top-3 right-3 text-slate-300 hover:text-red-500 transition">
                <TrashIcon :size="16" />
            </button>

            <div class="grid grid-cols-12 gap-5">
                <div class="col-span-12 md:col-span-4">
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 tracking-tighter">Effect</label>
                    <select v-model="stmt.effect"
                            :class="stmt.effect === 'deny' ? 'text-red-600 border-red-200 bg-red-50' : 'text-green-600 border-green-200 bg-green-50'"
                            class="w-full text-xs font-bold rounded-lg h-9">
                        <option value="allow">ALLOW</option>
                        <option value="deny">DENY</option>
                    </select>
                </div>

                <div class="col-span-12 md:col-span-8">
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 tracking-tighter">Statement ID (SID)</label>
                    <input v-model="stmt.sid" type="text" class="w-full text-xs font-mono rounded-lg border-slate-200 h-9" />
                </div>

                <div class="col-span-12">
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 tracking-tighter">Actions (Comma Separated)</label>
                    <input :value="(stmt.actions || []).join(', ')"
                           @input="updateField(index, 'actions', $event.target.value)"
                           type="text" class="w-full text-xs font-mono rounded-lg border-slate-200" placeholder="view, create, *" />
                </div>

                <div class="col-span-12">
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 tracking-tighter">Resource ARNs</label>
                    <input :value="(stmt.resources || []).join(', ')"
                           @input="updateField(index, 'resources', $event.target.value)"
                           type="text" class="w-full text-xs font-mono rounded-lg border-slate-200" placeholder="arn:cf:module:resource:*" />
                </div>
            </div>
        </div>

        <button @click="addStatement" type="button"
                class="w-full py-3 border-2 border-dashed border-slate-200 rounded-xl text-slate-400 text-xs font-black uppercase hover:bg-slate-100 hover:border-slate-300 transition flex items-center justify-center gap-2">
            <PlusIcon :size="14" /> Add New Statement
        </button>
    </div>
</template>
