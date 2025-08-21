<script setup>
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import Multiselect from 'vue-multiselect';
import SuccessModal from '../modals/SuccessModal.vue';
import i18n from '../../i18n';
import { useField, useFieldArray, useForm } from 'vee-validate';
import * as yup from 'yup';
import { useI18n } from 'vue-i18n';
const { t }  = useI18n();

const tags = ref([]);
const socialMedia = ref([]);
const open = ref(false);
const token = localStorage.getItem('auth_token');
const showSuccess = ref(false);

const modalTitle = ref('');
const modalMessage = ref('');
const modalRef = ref(null);


const showModal = (title, message) => {
  modalTitle.value = title;
  modalMessage.value = message;
  modalRef.value?.show();
};


const emit = defineEmits(['close', 'created', 'update:forms', 'remove']);

const props = defineProps({
    forms: {
        type: Array,
        required: true
    },
    disableRemove: {
        type: Boolean,
        default: false
    },
    errorsF: { 
        type: Array,
        required:true,
    },
    errors: {
        type: Array,
        required: true,
    }
});
const closeForm = async () => {
  if (success) {
    emit('close')
  }
}


function cancel() {
  emit('close')
}

const alert = ref({
    visible: false,
    message: '',
    type: 'alert-success'
});

const fetchTags = async () => {
    const response = await axios.get('api/posts/create', {
        headers: {
            'Authorization': `Bearer ${token}`,
            "Accept-Language": i18n.global.locale.value
        }
    });
    tags.value = response.data.tags;
    socialMedia.value = response.data.socialMedia;
}

function showSuccessModal() {
    showSuccess.value = true;
    setTimeout(() => {
        emit('created')
        showSuccess.value = false
        emit('close')
    }, 1500)
}

const formItemSchema = yup.object({
  name: yup.string().required('Name is required'),
  description: yup.string().required('Description is required'),
  tags: yup.array().min(1, 'Select at least one tag'),
});

const schema = yup.object({
  forms: yup.array().of(formItemSchema).required('At least one form is required'),
});

// Initialize Vee-Validate form with props.forms
const { values, errors, validate } = useForm({
  validationSchema: schema,
  initialValues: { forms: [...props.forms] , errors: [...props.errors]},
  validateOnInput: true,
});

// Bind Vee-Validate field array to forms
const { fields, push, remove } = useFieldArray('forms');

watch(
  () => props.forms,
  (newForms) => {
    // Clear current fields
    while (fields.value.length > 0) remove(0);
    // Push new forms
    newForms.forEach(f => push(f));
  },
  { deep: true, immediate: true }
);

const localErrors = ref([...props.errors]);

watch(
  errors,
  (newErrors) => {
    if (newErrors.forms) {
      newErrors.forms.forEach((e, idx) => {
        localErrors.value[idx] = e || {};
      });
    }
  },
  { deep: true, immediate: true }
);
/* function removeForm(index) {
    forms.value.splice(index, 1);
} */

const handleRemove = (index) => {
    emit('remove', index);
}

onMounted(
    fetchTags);
</script>

<template>
    <!-- <div class="title text-center mb-6">
        
    </div> -->
    
    <div class="w-full">
        <div v-if="alert.visible" :class="['alert', alert.type, 'rounded-md px-4 py-2 mb-4']" role="alert">
            {{ alert.message }}
        </div>

        <SuccessModal
            :show="showSuccess"
            title="Post Created Successfully"
            message="Your new post has been saved"
            @close="showSuccess= false"
        >

        </SuccessModal>

        <div class="rounded-lg">
                <div v-for="(field, index) in fields" :key="index" class="space-y-6 mt-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 text-base">#{{ index + 1 }}</span>
                        <div class=" text-left" v-if="!disableRemove || forms.length > 1">
                            <button type="button" @click="handleRemove(index)" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 focus:outline-none text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                        <div class="w-full md:w-1/2">
                            <label for="post_name" class="text-lg font-medium text-gray-700 text-left">{{ $t('post_add.post_name') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="field.value.name" 
                                id="post_name" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                :placeholder="$t('post_add.post_name_placeholder')" 
                            />
                            <div v-if="props.errorsF?.[index]?.name" class="text-red-500 text-sm mt-2">
                                {{ props.errorsF[index].name }}
                            </div>
                            <div v-if="props.errors?.[index]?.name" class="text-red-500 text-sm mt-2">{{ props.errors?.[index].name }}</div>
                        </div>

                        <div class="w-full md:w-1/2">
                            <label for="description" class="text-lg font-medium text-gray-700 text-left">{{ $t('post_add.post_description') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="field.value.description" 
                                id="description" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                :placeholder="$t('post_add.post_description_placeholder')"
                            />
                            <div v-if="props.errorsF?.[index]?.description" class="text-red-500 text-sm mt-2">
                                {{ props.errorsF[index].description }}
                            </div>
                            <div v-if="errors.forms?.[index]?.description">{{ errors.forms[index].description }}</div>
                        </div>
                    </div>

                    <div class="">
                        <label for="tags" class="block text-lg font-medium text-gray-700 text-left">{{ $t('post_add.tags') }}<span class="text-red-400 text-base font-medium">*</span></label>
                        <Multiselect
                            id="multiselect"
                            v-model="field.value.tags"
                            :options="tags"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="false"
                            :hide-selected="true"
                            
                            label="name"
                            track-by="id"
                            :preselect-first="false"
                            class="mt-2"
                        >
                            <template #tag="{ option, remove }">
                                <span class="bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full mr-2">
                                    {{ option.name }}
                                    <span class="ml-2 text-red-500 cursor-pointer" @click="remove(option)">❌</span>
                                </span>
                            </template>
                            <template #clear="props">
                                <div class="multiselect__clear" v-if="field.value.tags" @mousedown.prevent.stop="clearAll(props.search)"></div>
                            </template>
                        </Multiselect>
                        <div v-if="props.errorsF?.[index]?.tags" class="text-red-500 text-sm mt-2">
                            {{ props.errorsF[index].tags }}
                        </div>
                        <div v-if="errors.forms?.[index]?.tags">{{ errors.forms[index].tags }}</div>
                    </div>

                    <!-- <div class="">
                        <label for="socialMedia" class="block text-lg font-medium text-gray-700 text-left">Social Media<span class="text-red-400 text-base font-medium">*</span></label>
                        <Multiselect
                            id="multiselect"
                            v-model="form.socialMedia"
                            :options="socialMedia"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :hide-selected="false"
                            
                            label="platform"
                            track-by="id"
                            :preselect-first="false"
                            class="mt-2"
                        >
                            <template #tag="{ option, remove }"> -->
                                <!-- <span class="bg-green-100 text-green-800 px-2.5 py-0.5 rounded-full mr-2"> -->
                                    <!-- {{ option.platform }} -->
                                    <!-- <span class="ml-2 text-red-500 cursor-pointer" @click="remove(option)">❌</span> -->
                                <!-- </span> -->
                           <!--  </template> -->
                            <!-- <template #clear="props">
                                <div class="multiselect__clear" v-if="form.socialMedia.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                            </template> -->
                       <!--  </Multiselect>
                        <div v-if="props.errors?.[index]?.socialMedia" class="text-red-500 text-sm mt-2">
                            {{ props.errors[index].socialMedia }}
                        </div>
                    </div> -->

                    

                </div>
                    <!-- <div class="mt-2 flex justify-end">
                        <button type="button" @click="addForm" class="btn btn-primary mb-4 px-2 py-2 mt-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                    </div> -->
        </div>
    </div>
</template>


<style src="vue-multiselect/dist/vue-multiselect.min.css">
.form-label {
    align-items:start;
    font-size: large;
    text-decoration: none;
    border: none;
}

.alert-box{
    width: 375px;
    height: 53px;
    border-left-width: 0;
    border-right-width: 0;
    border-top-width: 0;
    border-bottom-width: 0;
}

.custom__tag1 {
    background-color: blue;
    color: white;
    text-align: center;
    width: 86px;
    height: 23px;
    margin: 10px;
}

.error {
    font-size: medium;
}


</style>