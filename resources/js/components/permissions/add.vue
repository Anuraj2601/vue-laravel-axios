<script setup>
import { onMounted, ref, watch } from 'vue';
import SuccessModal from '../modals/SuccessModal.vue';
import Multiselect from 'vue-multiselect';
import axios from 'axios';
import i18n from '../../i18n';

const token = localStorage.getItem('auth_token');
const showSuccess = ref(false);
const permissions = ref([]);
const emit = defineEmits(['close','created','update:forms', 'remove']);

import * as yup from 'yup';
import { useI18n } from 'vue-i18n';
import { useField, useForm } from 'vee-validate';
const {t} = useI18n();
const props = defineProps({
    formsP: {
        type: Object,
        required: true
    },
    errorsP: {
        type: Object,
        required: true
    }
});

useForm({
    validateOnInput: true,
});

const {
  value: name,
  errorMessage: nameError
} = useField('name', yup.string().required(t('permission_add.error_required')))


watch(name, (newVal) => {
  props.formsP.name = newVal;
});

const fetchPermissions = async() => {
    try {
        const res = await axios.get('api/permissions', {
            headers: {
                'Authorization': `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
            }
        });
        permissions.value = res.data.permissions;
    } catch (error) {
        console.error('Unable to fetch Permissions', error);
    }
}

const handleRemove = (index) => {
    emit('remove', index);
}

onMounted(fetchPermissions);
</script>

<template>
    <div class="w-full">
        <SuccessModal
            :show="showSuccess"
            :title="$t('permission_add.success_title')"
            :message="$t('permission_add.success_message')"
            @close="showSuccess=false"
        >
        </SuccessModal>

        <div class="rounded-lg">
            <div class="space-y-6 mt-2">
                <!-- <span class="text-gray-600 text-base">#{{ index + 1 }}</span> -->
                <div class="flex flex-col space-y-4 ">
                    <div class="w-full">
                        <label for="post_name" class="text-lg font-medium text-gray-700 text-left">{{ $t('permission_add.title') }}<span class="text-red-400 text-base font-medium">*</span></label>
                                <input 
                                    type="text" 
                                    v-model="name" 
                                    id="post_name" 
                                    class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                    :placeholder="$t('permission_add.placeholder')"
                                />
                        <div v-if="props.errorsP?.name" class="text-red-500 text-sm mt-2">
                            {{ props.errorsP.name }}
                        </div>
                        <div v-else-if="nameError" class="text-red-500 text-sm mt-2">
                            {{ nameError }}
                        </div>
                    </div>

                    <!-- <div class="w-full mt-4 text-left">
                        <button type="button" @click="handleRemove(index)" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 focus:outline-none text-sm">Remove</button>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
</style>