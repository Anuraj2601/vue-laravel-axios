<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500/75 hidden"
    :id="modalId"
    tabindex="-1"
    aria-labelledby="modalLabel"
    aria-hidden="true"
    ref="modalRef"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
      <div class="flex justify-between items-center p-4 border-b">
        <h5 class="text-lg font-semibold text-gray-900" id="modalLabel">{{ title }}</h5>
        <button 
          type="button" 
          class="text-gray-400 hover:text-gray-600 focus:outline-none"
          @click="closeModal"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <div class="p-6 text-lg text-red-500">
        {{ message }}
      </div>
      <div class="p-4 border-t flex justify-end">
        <button 
          type="button" 
          class="bg-gray-300 text-gray-700 hover:bg-gray-400 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          @click="closeModal"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineExpose } from 'vue';

const props = defineProps({
  title: String,
  message: String,
  modalId: {
    type: String,
    default: 'baseModal'
  }
});

const modalRef = ref(null);

const closeModal = () => {
  modalRef.value?.classList.add('hidden');
};

const show = () => {
  modalRef.value?.classList.remove('hidden');
};

defineExpose({ show });
</script>

<style scoped>

</style>
