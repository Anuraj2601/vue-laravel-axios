<template>
  <TransitionRoot appear :show="show" as="template">
    <div class="fixed inset-0 z-50 overflow-hidden">
      
      <div class="absolute inset-0 bg-gray-500/75 transition-opacity" @click="close"></div>
      <div class="fixed inset-y-0 right-0 max-w-full flex">
        <TransitionChild
          as="div"
          enter="transform transition ease-in-out duration-300"
          enter-from="translate-x-full"
          enter-to="translate-x-0"
          leave="transform transition ease-in-out duration-300"
          leave-from="translate-x-0"
          leave-to="translate-x-full"
          class="w-screen max-w-xl"
        >
          <div class="h-full flex flex-col bg-white shadow-xl overflow-y-auto">
            <div class="px-4 py-6 border-b border-gray-200 flex justify-between items-center">
              <h2 class="text-lg font-medium text-gray-900">Edit user ID: {{ userId }}</h2>
              <button @click="close" class="text-gray-400 hover:text-gray-600">
                ✕
              </button>
            </div>

            <div class="px-6 py-4">
              <Edit :userId="userId" @updated="handleUpdated" />
            </div>
          </div>
        </TransitionChild>
      </div>
    </div>
  </TransitionRoot>
</template>

<script setup>
import { TransitionChild, TransitionRoot } from '@headlessui/vue'
import Edit from './edit.vue';


defineProps({
  show: Boolean,
  userId: [String, Number],
})

const emit = defineEmits(['close', 'updated'])

function close() {
  emit('close');
}

function handleUpdated() {
  emit('updated');
  close();
}
</script>
