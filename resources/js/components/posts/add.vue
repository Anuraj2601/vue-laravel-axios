<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Multiselect from 'vue-multiselect';
import SuccessModal from '../modals/SuccessModal.vue';

const tags = ref([]);

const router = useRouter();
const route  = useRoute();



const token = localStorage.getItem('auth_token');
const userRole = localStorage.getItem('user_role');
const showSuccess = ref(false);

const modalTitle = ref('');
const modalMessage = ref('');
const modalRef = ref(null);

const showModal = (title, message) => {
  modalTitle.value = title;
  modalMessage.value = message;
  modalRef.value?.show();
};

const emit = defineEmits(['close'])

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
})
 
const errors = ref({});

const fetchTags = async () => {
    const response = await axios.get('api/posts/create', {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
    tags.value = response.data.tags;
}

const forms = ref([
    {
        name: '',
        description: '',
        tags: []
    }
]);

function addForm() {
    forms.value.push({
        name: '',
        description: '',
        tags: []
    })
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

const submitForm = async () => {
        errors.value = {};
    try {
        const response = await axios.post('api/posts/store', {
        forms: forms.value
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        if(response.data.status == 'success') {
            showSuccessModal();
        }
    } catch (error) {
        if(error.response) {
            if (error.response && error.response.status === 422) {
            
            errors.value = error.response.data.error; 


            console.error('Validation errors:', errors.value);
            }
        }
    }
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

        <div class="bg-white shadow-lg rounded-lg p-6">
             <div class="flex justify-between">
                <h2 class="text-2xl font-semibold">Create Posts</h2>
                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="cancel" ref="cancelButtonRef">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
             </div>
             <button type="button" @click="addForm" class="btn btn-primary mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none text-sm">Add New Post</button>

            <form @submit.prevent="submitForm">
                <div v-for="(form, index) in forms" :key="index" class="space-y-6">
                    
                    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                        <div class="w-full md:w-1/2">
                            <label for="post_name" class="text-lg font-medium text-gray-700 text-left">Post Name<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="form.name" 
                                id="post_name" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                placeholder="Tech Enthusiast Blog" 
                            />
                            <div v-if="errors['forms.' + index + '.name']" class="text-red-500 text-sm mt-2">
                                {{ errors['forms.' + index + '.name'] }}
                            </div>
                        </div>

                        <div class="w-full md:w-1/2">
                            <label for="description" class="text-lg font-medium text-gray-700 text-left">Post Description<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="form.description" 
                                id="description" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                placeholder="Sharing my thoughts on the latest tech trends"
                            />
                            <div v-if="errors['forms.' + index + '.description']" class="text-red-500 text-sm mt-2">
                                {{ errors['forms.' + index + '.description'] }}
                            </div>
                        </div>
                    </div>

                    <div class="">
                        <label for="tags" class="block text-lg font-medium text-gray-700 text-left">Tags<span class="text-red-400 text-base font-medium">*</span></label>
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
                            :preselect-first="true"
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
                        <div v-if="errors['forms.' + index + '.tags']" class="text-red-500 text-sm mt-2">
                            {{ errors['forms.' + index + '.tags'] }}
                        </div>
                    </div>

                    <div class="w-full mt-4 text-left">
                        <button type="button" @click="removeForm(index)" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 focus:outline-none text-sm">Remove</button>
                    </div>

                </div>

                <div class="mt-6 text-right">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none text-sm">Submit</button>
                </div>
            </form>
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