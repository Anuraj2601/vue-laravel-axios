<script setup>
import axios from 'axios';
import { computed, onBeforeMount, onMounted, reactive, ref, watch, watchEffect } from 'vue';
import SuccessModal from '../modals/SuccessModal.vue';
import i18n from '../../i18n';
import { useField, useForm } from 'vee-validate';
import * as yup from 'yup';
import { useI18n } from 'vue-i18n';
import Multiselect from 'vue-multiselect';
const tags = ref([]);
const socialMedia = ref([]);
const { t }  = useI18n();
const token = localStorage.getItem('auth_token');
const showSuccess = ref(false);
const isNewPlatform = ref(false);
const hydrated = ref(false);

const emit = defineEmits(['close', 'created', 'update:formMyPost', 'remove', 'name']);

const props = defineProps({
    socialMediaId: {
        type: [String, Number],
        required: true,
    },
    formMyPost1: {
        type: Object,
        required: true,
    },
    errorMyPost1: {
        type: Object,
        required: true
    }
});
const closeForm = async () => {
  if (success) {
    emit('close')
  }
}

const { setFieldValue  } = useForm({
    validateOnInput: true,
    validateOnMount: false,
    validateOnModelUpdate: false,
});

const {
  value: platform,
  errorMessage: platformError
} = useField('platform', yup.object().required(t('social_add.name_required')))


const {
  value: location,
  errorMessage: locationError
} = useField('location', yup.string().required(t('social_add.location_required')))

const {
  value: date,
  errorMessage: dateError
} = useField(
  'date',
  yup
    .date()
    .required(t('social_add.date_required'))
    .test('is-today', t('social_add.date_must_be_today'), function (value) {
      if (!value) return false;

      if (!isNewPlatform.value) return true;

      const selected = new Date(value);
      selected.setHours(0, 0, 0, 0);
      return selected.getTime() === today_date.getTime();
    })
);


const today_date = new Date();
today_date.setHours(0, 0, 0, 0);

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
            'Authorization': `Bearer ${token}`,
            "Accept-Language": i18n.global.locale.value
        }
    });
    tags.value = response.data.tags;
    socialMedia.value = response.data.social_medias || [];
}

function showSuccessModal() {
    showSuccess.value = true;
    setTimeout(() => {
        emit('created')
        showSuccess.value = false
        emit('close')
    }, 1500)
}

watch(() => props.formMyPost1, (newVal) => {
  if (!newVal) return;
    if (Array.isArray(newVal.platform) && newVal.platform.length > 0) {
    setFieldValue('platform', newVal.platform[0], { force: false });
  } else if (typeof newVal.platform === 'string') {
    setFieldValue('platform', { id: Date.now(), platform: newVal.platform, isNew: true }, { force: false });
  } else {
    setFieldValue('platform', null, { force: false });
  }

  setFieldValue('location', newVal.location ?? '', { force: false });
  setFieldValue('date', newVal.date || today.value, { force: false });
  hydrated.value = true;
}, { immediate: true });



// Sync back to parent
watch(location, (newVal) => {
  props.formMyPost1.location = newVal;
});

watch(date, (newVal) => {
  props.formMyPost1.date = newVal;
});


onBeforeMount(fetchTags);
/* onMounted(() => {
  fetchTags();
}); */

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
                <form class="space-y-6" v-if="hydrated">
                    
                    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                        <div class="w-full md:w-1/2">
                            <label for="platform" class="text-lg font-medium text-gray-700 text-left"> {{ $t('social_add.name_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <Multiselect id="single-select-search" v-model="platform" :options="socialMedia" :custom-label="opt => opt.platform" placeholder="Select one" label="platform"
                                class="mt-2" :multiple="false" :disabled="true"
                                track-by="id" aria-label="pick a value"></Multiselect>
                            <div v-if="props.errorMyPost1?.platform" class="text-red-500 text-sm mt-2">
                                {{ props.errorMyPost1.platform }}
                            </div>
                            <div v-else-if="platformError" class="text-red-500 text-sm mt-2">
                                {{ platformError }}
                            </div>
                        </div>

                        <div class="w-full md:w-1/2">
                            <label for="description" class="text-lg font-medium text-gray-700 text-left">{{$t('social_add.location_label')}}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                type="text" 
                                v-model="location" 
                                id="description" 
                                class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                :placeholder="$t('social_add.location_placeholder')"
                                :disabled="true"
                            />
                            <div v-if="props.errorMyPost1?.location" class="text-red-500 text-sm mt-2">
                                {{ props.errorMyPost1.location }}
                            </div>
                            <div v-else-if="locationError" class="text-red-500 text-sm mt-2">
                                {{ locationError }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="date" class="block text-lg font-medium text-gray-700 text-left">{{$t('social_add.date_label')}}<span class="text-red-400 text-base font-medium">*</span></label>
                        <div class="text-left w-1/2">
                        <input 
                            type="date" 
                            v-model="date" 
                            id="date" 
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="$t('social_add.date_placeholder')"
                            :min="today"
                            :max="today"
                            :disabled="true"
                        />
                        <div v-if="errorMyPost1?.date" class="text-red-500 text-sm mt-2">
                            {{ errorMyPost1.date }}
                        </div>
                        <div v-else-if="dateError" class="text-red-500 text-sm mt-2">
                            {{ dateError }}
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