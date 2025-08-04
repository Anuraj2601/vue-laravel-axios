<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import Multiselect from 'vue-multiselect';
import SuccessModal from '../modals/SuccessModal.vue';


const tags = ref([]);
const socialMedia = ref([]);

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
    errors: {
        type: Array,
        required: true
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
            'Authorization': `Bearer ${token}`
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

function removeForm(index) {
    forms.value.splice(index, 1);
}

const handleRemove = (index) => {
    emit('remove', index);
}

onMounted(fetchTags);
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
                <div v-for="(form, index) in props.forms" :key="index" class="space-y-6 mt-2">
                    <span class="text-gray-600 text-base">#{{ index + 1 }}</span>
                    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                        <div class="w-full md:w-1/2">
                            <label for="post_name" class="text-lg font-medium text-gray-700 text-left">{{ $t('post_add.post_name') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="form.name" 
                                id="post_name" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                :placeholder="$t('post_add.post_name_placeholder')" 
                            />
                            <div v-if="props.errors?.[index]?.name" class="text-red-500 text-sm mt-2">
                                {{ $t('post_add.errors.name') }}
                            </div>
                        </div>

                        <div class="w-full md:w-1/2">
                            <label for="description" class="text-lg font-medium text-gray-700 text-left">{{ $t('post_add.post_description') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="form.description" 
                                id="description" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                :placeholder="$t('post_add.post_description_placeholder')"
                            />
                            <div v-if="props.errors?.[index]?.description" class="text-red-500 text-sm mt-2">
                                {{ $t('post_add.errors.description') }}
                            </div>
                        </div>
                    </div>

                    <div class="">
                        <label for="tags" class="block text-lg font-medium text-gray-700 text-left">{{ $t('post_add.tags') }}<span class="text-red-400 text-base font-medium">*</span></label>
                        <Multiselect
                            id="multiselect"
                            v-model="form.tags"
                            :options="tags"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="true"
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
                                <div class="multiselect__clear" v-if="form.tags.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                            </template>
                        </Multiselect>
                        <div v-if="props.errors?.[index]?.tags" class="text-red-500 text-sm mt-2">
                            {{ $t('post_add.errors.tags') }}
                        </div>
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

                    <div class="w-full mt-4 text-left">
                        <button type="button" @click="handleRemove(index)" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 focus:outline-none text-sm">{{ $t('post_add.remove_button') }}</button>
                    </div>

                </div>

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