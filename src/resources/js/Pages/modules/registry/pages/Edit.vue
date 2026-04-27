<script setup xmlns="http://www.w3.org/1999/html">
import { ref } from 'vue';
import { XIcon } from 'lucide-vue-next';
import CreateTemporaryFile from "@/Pages/modules/registry/components/CreateTemporaryFile.vue";
import FormFilePreviewCard from "@/Components/forms/FormFilePreviewCard.vue";
import {Head, useForm} from "@inertiajs/vue3";
import {EditformData} from "@/Pages/modules/registry/services/formData.js";
import ContextModuleLayout from "@/Components/layout/ContextModuleLayout.vue";
import PageHeader from "@/Components/layout/PageHeader.vue";
import ValidationErrors from "@/Components/forms/ValidationErrors.vue";
import LoadButton from "@/Components/ui/LoadButton.vue";

const props = defineProps({ file_receive: Object });
const form = useForm(EditformData(props))

const uuid = props.file_receive.uuid

// Reactivity for the modal
const previewUrl = ref(null);
const isModalOpen = ref(false);

const openModal = (url) => {
    previewUrl.value = url;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    previewUrl.value = null;
};

const submit = () => {
    form.clearErrors();

    form.put(route('register.update',uuid) , {
        preserveScroll: true,
    });
};
</script>

<template>
    <ContextModuleLayout>
                <Head title= "Edit Temporary File"/>
        <div class="h-full flex flex-col bg-white overflow-hidden">
            <PageHeader
                :title="`Edit ${file_receive.file.temp_file_number}`"
                :subtitle="`Updating details for registry record #${file_receive.id}`"
            />

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 pt-2 h-full overflow-hidden">

                <aside class="lg:col-span-1 space-y-4 overflow-y-auto pr-2 border-r border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Attached Files</h3>
                        <span class="bg-slate-100 text-slate-600 text-[10px] px-2 py-0.5 rounded-full font-bold">
                         {{ file_receive.file.documents?.length || 0 }}
                    </span>
                    </div>

                    <div class="flex flex-col gap-3">
                        <FormFilePreviewCard
                            v-for="doc in file_receive.file.documents"
                            :key="doc.id"
                            :file-path="doc.file_path"
                            :file-name="doc.original_name"
                            @preview="openModal"
                        />
                    </div>

                    <p class="text-[10px] text-slate-400 italic mt-4 text-center">
                        Click a card to view document
                    </p>
                </aside>

                <main class="lg:col-span-3 overflow-y-auto">
                    <div class="max-w-2xl bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                        <ValidationErrors :errors="form.errors" class="mb-6" />

                        <CreateTemporaryFile :form="form"/>
                    </div>
                    <div class="mt-2 justify-center">
                        <LoadButton
                            type="button"
                            :loading="form.processing "
                            :disabled="form.processing"
                            @click="submit">
                            {{ form.processing && !form.is_draft ? 'Submitting...' : 'Submit' }}
                        </LoadButton>
                    </div>

                </main>

                <Teleport to="body">
                    <div v-if="isModalOpen"
                         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-sm"
                         @click.self="closeModal"
                    >
                        <button @click="closeModal" class="absolute top-6 right-6 text-white hover:bg-white/20 p-2 rounded-full transition-all">
                            <XIcon class="w-8 h-8" />
                        </button>

                        <div class="max-w-5xl w-full h-[90vh] flex items-center justify-center">
                            <img v-if="previewUrl.match(/\.(jpg|jpeg|png|webp)$/i)"
                                 :src="previewUrl"
                                 class="max-w-full max-h-full object-contain rounded-lg shadow-2xl shadow-black"
                            />

                            <iframe v-else-if="previewUrl.endsWith('.pdf')"
                                    :src="previewUrl"
                                    class="w-full h-full rounded-lg border-0"
                            ></iframe>

                            <div v-else class="text-white text-center">
                                <p>Preview not available for this file type.</p>
                                <a :href="previewUrl" download class="mt-4 inline-block bg-blue-600 px-4 py-2 rounded">Download Instead</a>
                            </div>
                        </div>
                    </div>
                </Teleport>
            </div>




        </div>
    </ContextModuleLayout>
</template>

