<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import { ref, toRaw } from 'vue'
import axios from 'axios'
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import PageHeader from "@/Components/layout/PageHeader.vue";
import { PlayIcon, SaveIcon, ArrowLeftIcon, Loader2Icon } from 'lucide-vue-next';
import PolicyBuilder from "@/Pages/modules/admin/components/PolicyBuilder.vue";

const props = defineProps({
    policy: Object,
})

// 1. DATA NORMALIZER
const normalizeStatement = (raw) => {
    const data = JSON.parse(JSON.stringify(toRaw(raw || [])));
    const arr = Array.isArray(data) ? data : [];
    return {
        version: props.policy?.version || '2026-01-01',
        statements: arr.map(s => ({
            sid: s.Sid || s.sid || 'Stmt-' + Math.random().toString(36).substr(2, 4),
            effect: (s.Effect || s.effect || 'allow').toLowerCase(),
            actions: s.Action || s.actions || s.action || ['*'],
            resources: s.Resource || s.resources || s.resource || ['*']
        }))
    };
}

// 2. FORM INITIALIZATION
const form = useForm({
    name: props.policy?.name || '',
    description: props.policy?.description || '',
    statement: normalizeStatement(props.policy?.statement)
})

// 3. SIMULATION STATE
const simulation = ref({
    action: '',
    resource: '',
    result: null,
    matched_statements: [],
    loading: false
})

async function save() {
    form.transform((data) => ({
        ...data,
        statement: data.statement.statements.map(s => ({
            Sid: s.sid,
            Effect: s.effect.charAt(0).toUpperCase() + s.effect.slice(1),
            Action: s.actions,
            Resource: s.resources
        }))
    })).put(route('policies.update', props.policy.id), {
        preserveScroll: true
    })
}

async function simulate() {
    if(!simulation.value.action) return;
    simulation.value.loading = true;
    try {
        const { data } = await axios.post(route('policies.simulate', props.policy.id), {
            action: simulation.value.action,
            resource: simulation.value.resource,
            statement: form.statement.statements
        })
        simulation.value.result = data.result
        simulation.value.matched_statements = data.matched_statements
    } finally {
        simulation.value.loading = false;
    }
}
</script>

<template>
    <ContextModuleLayout v-if="props.policy">
        <Head :title="'IAM: ' + form.name" />

        <div class="h-full flex flex-col bg-white">
            <PageHeader :title="form.name" :subtitle="form.description">
                <template #actions>
                    <Link :href="route('policies.index')" class="text-slate-400 hover:text-slate-600 transition">
                        <ArrowLeftIcon :size="20" />
                    </Link>
                </template>
            </PageHeader>

            <div class="flex-1 overflow-y-auto p-6 space-y-8">
                <div class="grid grid-cols-12 gap-8 items-start">

                    <section class="col-span-12 lg:col-span-8">
                        <div class="mb-4">
                            <h2 class="text-sm font-black uppercase tracking-widest text-slate-800">Policy Editor</h2>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Policy Name</label>
                                <input v-model="form.name" class="w-full rounded-lg border-slate-200 text-sm font-bold" />
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Description</label>
                                <input v-model="form.description" class="w-full rounded-lg border-slate-200 text-sm" />
                            </div>
                        </div>

                        <PolicyBuilder v-if="form.statement" v-model="form.statement" />

                        <div class="mt-8 flex justify-end">
                            <button class="bg-blue-600 text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg disabled:opacity-50"
                                    @click="save" :disabled="form.processing">
                                {{ form.processing ? 'Deploying...' : 'Deploy Changes' }}
                            </button>
                        </div>
                    </section>

                    <aside class="col-span-12 lg:col-span-4 lg:sticky lg:top-6">
                        <div class="rounded-xl border border-slate-200 bg-slate-900 p-5 shadow-2xl">
                            <div class="mb-4 flex items-center justify-between border-b border-slate-800 pb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                    Live JSON Document
                </span>
                                <div class="flex gap-1">
                                    <div class="w-2 h-2 rounded-full bg-red-500/50"></div>
                                    <div class="w-2 h-2 rounded-full bg-yellow-500/50"></div>
                                    <div class="w-2 h-2 rounded-full bg-green-500/50"></div>
                                </div>
                            </div>

                            <pre class="overflow-auto text-[11px] font-mono text-blue-300 leading-relaxed max-h-[calc(100vh-250px)] custom-scrollbar">
{{ JSON.stringify(form.statement, null, 2) }}
            </pre>

                            <div class="mt-4 pt-3 border-t border-slate-800">
                                <p class="text-[9px] text-slate-500 font-mono italic">
                                    // schema version: {{ form.statement.version }}
                                </p>
                            </div>
                        </div>
                    </aside>

                </div>

                <hr class="border-slate-100" />

                <section v-if="simulation" class="max-w-4xl bg-slate-50 rounded-2xl p-6 border border-slate-100 mb-20">
                    <div class="mb-6">
                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-800 flex items-center gap-2">
                            <PlayIcon :size="16" class="text-green-500" /> Logic Simulator
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase">Action</label>
                            <input v-model="simulation.action" class="w-full rounded-lg border-slate-200 text-xs font-mono" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase">Resource ARN</label>
                            <input v-model="simulation.resource" class="w-full rounded-lg border-slate-200 text-xs font-mono" />
                        </div>
                    </div>

                    <button class="mt-4 bg-slate-800 text-white px-6 py-2 rounded-lg text-xs font-bold flex items-center gap-2"
                            @click="simulate" :disabled="simulation.loading">
                        <Loader2Icon v-if="simulation.loading" :size="14" class="animate-spin" />
                        {{ simulation.loading ? 'Evaluating...' : 'Run Simulation' }}
                    </button>

                    <div v-if="simulation.result" class="mt-6 p-4 rounded-xl border bg-white shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xs font-bold text-slate-500 uppercase">Decision:</span>
                            <span :class="simulation.result === 'allow' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'"
                                  class="px-3 py-0.5 rounded-full text-[10px] font-black uppercase border border-current">
                                {{ simulation.result }}
                            </span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </ContextModuleLayout>
</template>
