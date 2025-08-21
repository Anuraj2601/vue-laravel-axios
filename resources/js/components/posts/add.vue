<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Multiselect from 'vue-multiselect';
import { useForm, useFieldArray } from 'vee-validate';
import * as yup from 'yup';
import SuccessModal from '../modals/SuccessModal.vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const props = defineProps({
  forms: { type: Array, required: true },
  disableRemove: { type: Boolean, default: false },
  errorsF: { type: Array, required: true },
});

const emit = defineEmits(['close', 'created', 'update:forms', 'remove']);

const tags = ref([]);
const showSuccess = ref(false);

// Fetch tags from backend
const token = localStorage.getItem('auth_token');
const fetchTags = async () => {
  const res = await axios.get('api/posts/create', {
    headers: {
      Authorization: `Bearer ${token}`,
      'Accept-Language': t('locale'),
    },
  });
  tags.value = res.data.tags;
};

onMounted(fetchTags);

// Define vee-validate schema
const schema = yup.object({
  forms: yup.array().of(
    yup.object({
      name: yup.string().required(t('post_add.errors.name')),
      description: yup.string().required(t('post_add.errors.description')),
      tags: yup.array().min(1, t('post_add.errors.tags')),
    })
  ),
});

// Initialize form
const { values, errors, validate } = useForm({
  validationSchema: schema,
  initialValues: { forms: props.forms },
  validateOnInput: true,
});

const { fields, push, remove } = useFieldArray('forms');


defineExpose({ addForm: () => push({ name: '', description: '', tags: [] }) });


const handleRemove = (index) => {
  remove(index);
  emit('update:forms', fields.value.map(f => f.value));
};
// Watch fields and emit updates to parent
watch(
  () => fields.value,
  (newFields) => {
    emit('update:forms', newFields.map(f => f.value));
  },
  { deep: true }
);

// Show success modal
const showSuccessModal = () => {
  showSuccess.value = true;
  setTimeout(() => {
    emit('created');
    showSuccess.value = false;
    emit('close');
  }, 1500);
};
</script>

<template>
  <div class="w-full">
    <SuccessModal
      :show="showSuccess"
      title="Post Created Successfully"
      message="Your new post has been saved"
      @close="showSuccess = false"
    />

    <div v-for="(field, index) in fields" :key="index" class="space-y-6 mt-2">
      <div class="flex justify-between">
        <span class="text-gray-600">#{{ index + 1 }}</span>
        <button
          v-if="!disableRemove || fields.length > 1"
          type="button"
          @click="handleRemove(index)"
          class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 text-sm"
        >
          Remove
        </button>
      </div>

      <!-- Name Input -->
      <div class="flex flex-col md:flex-row gap-4">
        <div class="w-full md:w-1/2">
          <label class="text-lg font-medium">{{ t('post_add.post_name') }}*</label>
          <input
            v-model="field.value.name"
            type="text"
            class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
            :placeholder="t('post_add.post_name_placeholder')"
          />
          <div class="text-red-500 text-sm mt-1">{{ props.errorsF?.[index]?.name || errors.value?.forms?.[index]?.name }}</div>
        </div>

        <!-- Description Input -->
        <div class="w-full md:w-1/2">
          <label class="text-lg font-medium">{{ t('post_add.post_description') }}*</label>
          <input
            v-model="field.value.description"
            type="text"
            class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
            :placeholder="t('post_add.post_description_placeholder')"
          />
          <div class="text-red-500 text-sm mt-1">{{ props.errorsF?.[index]?.description || errors.value?.forms?.[index]?.description }}</div>
        </div>
      </div>

      <!-- Tags Multiselect -->
      <div>
        <label class="text-lg font-medium">{{ t('post_add.tags') }}*</label>
        <Multiselect
          v-model="field.value.tags"
          :options="tags"
          :multiple="true"
          label="name"
          track-by="id"
          class="mt-2"
        >
        <template #tag="{ option, remove }">
                <span class="bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full mr-2">
                  {{ option.name }}
                  <span class="ml-2 text-red-500 cursor-pointer" @click="remove(option)">❌</span>
                </span>
              </template>
        </Multiselect>
        <div class="text-red-500 text-sm mt-1">{{ props.errorsF?.[index]?.tags || errors.value?.forms?.[index]?.tags }}</div>
      </div>
    </div>
  </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
