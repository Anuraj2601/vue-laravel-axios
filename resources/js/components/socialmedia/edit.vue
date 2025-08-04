<script setup>
import axios from 'axios';
import { computed, onMounted, reactive, ref } from 'vue';
import SuccessModal from '../modals/SuccessModal.vue';
import { useStore } from 'vuex';

const tags = ref([]);
const socialMedia = ref([]);

const token = localStorage.getItem('auth_token');
/* const store = useStore();

const token = computed(() => store.getters['auth/token']); */
const showSuccess = ref(false);

const emit = defineEmits(['close', 'created', 'update:formsSoc1', 'remove']);

const props = defineProps({
    socialMediaId: {
        type: [String, Number],
        required: true,
    },
    formsSoc1: {
        type: Object,
        required: true
    },
    errorsSoc: {
        type: Object,
        required: true
    }
});

const today = computed(() => {
      const date = new Date();
      const dd = String(date.getDate()).padStart(2, '0');
      const mm = String(date.getMonth() + 1).padStart(2, '0');
      const yyyy = date.getFullYear();
      return `${yyyy}-${mm}-${dd}`;
});
/* const fecthSocialMediaById = async () => {
    const res = await axios.get(`api/social-media/${props.socialMediaId}`, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
    console.log(props.socialMediaId,res.data);

    props.formsSoc1.value = res.data.social_media;
} */

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
            :title="$t('social_edit.success.title')"
            :message="$t('social_edit.success.message')"
            @close="showSuccess= false"
        >

        </SuccessModal>

        <div class="rounded-lg">
                <form class="space-y-6">
                    <!-- <input type="hidden" v-model="props.formsSoc1.id" /> -->
                    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                        <div class="w-full md:w-1/2">
                            <label for="social_media_name" class="text-lg font-medium text-gray-700 text-left">{{ $t('social_edit.label.platform') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="props.formsSoc1.platform" 
                                id="social_media_name" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                :placeholder="$t('social_edit.placeholder.platform')" 
                            />
                            <div v-if="props.errorsSoc?.platform" class="text-red-500 text-sm mt-2">
                                {{ $t('social_edit.error.platform') }}
                            </div>
                        </div>

                        <div class="w-full md:w-1/2">
                            <label for="location" class="text-lg font-medium text-gray-700 text-left">{{ $t('social_edit.label.location') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="props.formsSoc1.location" 
                                id="location" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                :placeholder="$t('social_edit.placeholder.location')"
                            />
                            <div v-if="props.errorsSoc?.location" class="text-red-500 text-sm mt-2">
                                {{ $t('social_edit.error.location') }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="date" class="block text-lg font-medium text-gray-700 text-left">{{ $t('social_edit.label.date') }}<span class="text-red-400 text-base font-medium">*</span></label>
                        <div class="text-left w-1/2">
                            <input 
                                type="date" 
                                v-model="props.formsSoc1.date" 
                                id="date" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="$t('social_edit.placeholder.date')"
                                :min="today"
                                :max="today"
                            />
                            <div v-if="props.errorsSoc?.date" class="text-red-500 text-sm mt-2">
                                {{ $t('social_edit.error.date') }}
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