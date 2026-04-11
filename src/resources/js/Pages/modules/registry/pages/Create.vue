<script setup>

import {Head} from "@inertiajs/vue3";
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
</script>


<template>
    <ContextModuleLayout >
        <Head title= "Register Files"/>

            <div class="space-y-6 max-w-6xl mx-auto">
                <div class="flex items-center justify-between border-b pb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-[#091E3E]">Receive & Register File</h1>
                        <p class="text-sm text-slate-500">Search for an existing file or create a new registry entry.</p>
                    </div>
                    <div class="flex gap-3">
                        <button class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
                            Save Draft
                        </button>
                        <button class="px-4 py-2 text-sm font-medium text-white bg-[#06A3DA] rounded-lg hover:bg-blue-600 shadow-sm">
                            Register & Submit to HOD
                        </button>
                    </div>
                </div>

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Search Existing File Number / Temp Number / Subject..."
                        class="block w-full pl-11 pr-4 py-4 border-2 border-slate-200 rounded-2xl focus:border-[#06A3DA] focus:ring-0 transition-all text-lg"
                    />
                    <div class="absolute inset-y-0 right-4 flex items-center">
                        <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">ESC TO CLEAR</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                                <h2 class="font-bold text-[#091E3E]">File Details</h2>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold uppercase text-slate-500">File Number / Temp No</label>
                                    <input type="text" class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]" placeholder="e.g. FETH/ADM/102">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold uppercase text-slate-500">Source (Mailing/Unit)</label>
                                    <select class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]">
                                        <option>External Mail</option>
                                        <option>Internal Memo</option>
                                        <option>Walk-in Registry</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-xs font-bold uppercase text-slate-500">Subject / Title</label>
                                    <input type="text" class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]" placeholder="Brief description of file contents">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold uppercase text-slate-500">Date Received</label>
                                    <input type="date" class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold uppercase text-slate-500">Priority Level</label>
                                    <div class="flex gap-2">
                                        <button class="flex-1 py-2 text-xs font-bold border rounded-lg border-slate-200 hover:bg-slate-50 text-slate-600">ROUTINE</button>
                                        <button class="flex-1 py-2 text-xs font-bold border rounded-lg border-amber-200 text-amber-600 hover:bg-amber-50">URGENT</button>
                                        <button class="flex-1 py-2 text-xs font-bold border rounded-lg border-red-200 text-red-600 hover:bg-red-50">CRITICAL</button>
                                    </div>
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-xs font-bold uppercase text-slate-500">Registry Minute / Remarks</label>
                                    <textarea rows="3" class="w-full border-slate-200 rounded-lg focus:border-[#06A3DA]" placeholder="Add any initial observations..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border-2 border-dashed border-slate-300 p-8 text-center hover:border-[#06A3DA] transition-colors group cursor-pointer">
                            <div class="mx-auto w-12 h-12 bg-blue-50 text-[#06A3DA] rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-[#091E3E]">Upload Scanned Document</h3>
                            <p class="text-xs text-slate-400 mt-1">Drag and drop PDF or JPG here, or click to browse</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-[#091E3E] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                            <div class="relative z-10">
                                <h2 class="text-xs font-bold text-blue-300 uppercase tracking-widest mb-4">Existing File Summary</h2>

                                <div class="space-y-4">
                                    <div class="flex justify-between border-b border-white/10 pb-3">
                                        <span class="text-sm text-white/60">Current Holder</span>
                                        <span class="text-sm font-medium">HOD Admin</span>
                                    </div>
                                    <div class="flex justify-between border-b border-white/10 pb-3">
                                        <span class="text-sm text-white/60">Current Location</span>
                                        <span class="text-sm font-medium">Registry Room B</span>
                                    </div>
                                    <div class="flex justify-between border-b border-white/10 pb-3">
                                        <span class="text-sm text-white/60">Last Movement</span>
                                        <span class="text-sm font-medium">10 Apr 2026</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-white/60">File Status</span>
                                        <span class="bg-blue-500 text-white text-[10px] px-2 py-0.5 rounded uppercase font-bold">In-Transit</span>
                                    </div>
                                </div>

                                <button class="w-full mt-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-bold transition">
                                    VIEW MOVEMENT HISTORY
                                </button>
                            </div>
                            <div class="absolute -right-8 -bottom-8 text-white/5 opacity-20">
                                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M13 9V3.5L18.5 9M6 2c-1.11 0-2 .89-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6z"/></svg>
                            </div>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                            <div class="flex gap-3">
                                <div class="text-amber-600">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-amber-800">Duplicate Prevention</h4>
                                    <p class="text-[11px] text-amber-700 mt-1 leading-relaxed">The system will check for existing volumes of this file number to prevent duplicate registration.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!--        <div class="h-full flex flex-col bg-white overflow-hidden">-->
<!--            <header class="bg-white  border-b shrink-0 z-10">-->
<!--                <h1 class="text-xl font-black text-slate-800 tracking-tight">Patient Registration</h1>-->
<!--                <p class="text-xs text-slate-500 font-medium truncate">-->
<!--                    Currently Registering: {{ form.first_name || 'New' }} {{ form.surname || 'Patient' }}-->
<!--                </p>-->
<!--            </header>-->

<!--            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 overflow-y-auto flex-1 ">-->

<!--                <div class="lg:col-span-1">-->

<!--                </div>-->

<!--                <main class="col-span-4 lg:col-span-3">-->
<!--                    <div class=" max-w-2xl bg-white   overflow-hidden flex flex-col">-->


<!--                    </div>-->
<!--                </main>-->

<!--            </div>-->
<!--        </div>-->
    </ContextModuleLayout>
</template>


