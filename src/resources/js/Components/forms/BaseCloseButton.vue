<template>
    <div class="flex items-center">
        <button
            type="button"
            @click="handleClose"
            class="group flex items-center justify-center p-2 text-gray-400 transition-all duration-200 rounded-md hover:text-red-600 hover:bg-red-50"
        >
            <span class="text-xl leading-none font-light group-hover:rotate-90 transition-transform duration-300">
        &times;
      </span>
        </button>
    </div>
</template>

<script setup>

const emit = defineEmits(['close']);

const handleClose = () => {
    emit('close');
};

</script>
