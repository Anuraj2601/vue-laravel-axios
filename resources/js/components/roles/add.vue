<script setup>
import { onMounted, ref, watch } from 'vue';
import SuccessModal from '../modals/SuccessModal.vue';
import Multiselect from 'vue-multiselect';
import axios from 'axios';
import i18n from '../../i18n';

import * as yup from 'yup';
import { useField, useForm } from 'vee-validate';
import { useI18n } from 'vue-i18n';

const token = localStorage.getItem('auth_token');
const showSuccess = ref(false);
const permissions = ref([]);
const emit = defineEmits(['close','created','update:forms', 'remove']);
const { t }  = useI18n();
const props = defineProps({
    formsR: {
        type: Object,
        required: true
    },
    errorsR: {
        type: Object,
        required: true
    }
});

const fetchPermissions = async() => {
    try {
        const res = await axios.get('api/roles', {
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

useForm({
    validateOnInput: true,
});

const {
  value: name,
  errorMessage: nameError
} = useField('name', yup.string().required(t('role_add.errors.name')))

const {
  value: permission,
  errorMessage: permissionError
} = useField('permission', yup.array().required(t('role_add.errors.permissions')))


watch(name, (newVal) => {
  props.formsR.name = newVal;
});

watch(permission, (newVal) => {
  props.formsR.permissions = newVal;
});

onMounted(fetchPermissions);
</script>

<template>
    <div class="w-full">
        <SuccessModal
            :show="showSuccess"
            :title="$t('role_add.success_modal.title')"
            :message="$t('role_add.success_modal.message')"
            @close="showSuccess=false"
        >
        </SuccessModal>

        <div class="rounded-lg">
            <div class="space-y-6 mt-2">
                <span class="text-gray-600 text-base"></span>
                <div class="flex flex-col space-y-4 ">
                    <div class="w-full">
                        <label for="post_name" class="text-lg font-medium text-gray-700 text-left">{{ $t('role_add.form.name_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                                <input 
                                    type="text" 
                                    v-model="name" 
                                    id="post_name" 
                                    class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                    :placeholder="$t('role_add.form.name_placeholder')" 
                                />
                        <div v-if="props.errorsR?.name" class="text-red-500 text-sm mt-2">
                            {{ props.errorsR.name }}
                        </div>
                        <div v-else-if="nameError" class="text-red-500 text-sm mt-2">
                            {{ nameError }}
                        </div>
                    </div>

                    <div class="">
                        <label for="permissions" class="block text-lg font-medium text-gray-700 text-left">{{ $t('role_add.form.permissions_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                        <Multiselect
                            id="multiselect"
                            v-model="permission"
                            :options="permissions"
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
                                <div class="multiselect__clear" v-if="permissions.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                            </template>
                        </Multiselect>
                        <div v-if="props.errorsR?.permissions" class="text-red-500 text-sm mt-2">
                             {{ props.errorsR.permissions }}
                        </div>
                        <div v-else-if="permissionError" class="text-red-500 text-sm mt-2">
                             {{ permissionError }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
</style>