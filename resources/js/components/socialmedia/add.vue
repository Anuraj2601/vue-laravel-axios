<script setup>
import axios from 'axios';
import { computed, onMounted, reactive, ref } from 'vue';
import SuccessModal from '../modals/SuccessModal.vue';

const tags = ref([]);
const socialMedia = ref([]);

const token = localStorage.getItem('auth_token');
const showSuccess = ref(false);

const emit = defineEmits(['close', 'created', 'update:formsSoc', 'remove', 'name']);

const props = defineProps({
    formsSoc: {
        type: Object,
        required: true
    },
    errorsSoc: {
        type: Object,
        required: true
    }
});
const closeForm = async () => {
  if (success) {
    emit('close')
  }
}



const today = computed(() => {
  const date = new Date();
  const dd = String(date.getDate()).padStart(2, '0');
  const mm = String(date.getMonth() + 1).padStart(2, '0');
  const yyyy = date.getFullYear();
  return `${yyyy}-${mm}-${dd}`;
});


function cancel() {
  emit('close')
}

const alert = ref({
    visible: false,
    message: '',
    type: 'alert-success'
});

/* Fetch All Tags */
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

onMounted(() => {
    fetchTags();
    if(!props.formsSoc.date) {
        props.formsSoc.date = today.value;
    }
});
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
            :title="$t('social_add.success_title')"
            :message="$t('social_add.success_message')"
            @close="showSuccess= false"
        >
        </SuccessModal>

        <div class="rounded-lg mt-2">
                <form class="space-y-6">
                    
                    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                        <div class="w-full md:w-1/2">
                            <label for="social_media_name" class="text-lg font-medium text-gray-700 text-left"> {{ $t('social_add.name_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="props.formsSoc.platform" 
                                id="social_media_name" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                :placeholder="$t('social_add.name_placeholder')"
                            />
                            <div v-if="props.errorsSoc?.platform" class="text-red-500 text-sm mt-2">
                                {{ $t('social_add.name_required') }}
                            </div>
                        </div>

                        <div class="w-full md:w-1/2">
                            <label for="description" class="text-lg font-medium text-gray-700 text-left">{{$t('social_add.location_label')}}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="props.formsSoc.location" 
                                id="description" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                :placeholder="$t('social_add.location_placeholder')"
                            />
                            <div v-if="props.errorsSoc?.location" class="text-red-500 text-sm mt-2">
                                {{ $t('social_add.location_required') }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="date" class="block text-lg font-medium text-gray-700 text-left">{{$t('social_add.date_label')}}<span class="text-red-400 text-base font-medium">*</span></label>
                        <div class="text-left w-1/2">
                        <input 
                            type="date" 
                            v-model="formsSoc.date" 
                            id="date" 
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="$t('social_add.date_placeholder')"
                            :min="today"
                            :max="today"
                        />
                        <div v-if="errorsSoc?.date" class="text-red-500 text-sm mt-2">
                            {{ $t('social_add.date_required') }}
                        </div>
                        </div>
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