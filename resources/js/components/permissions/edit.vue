<script setup>
import { onMounted, ref, watch } from 'vue';
import SuccessModal from '../modals/SuccessModal.vue';
import axios from 'axios';
import Multiselect from 'vue-multiselect';
import Add from './add.vue';
import i18n from '../../i18n';
import { useStore } from 'vuex';
import { useField, useForm } from 'vee-validate';
const showSuccess = ref(false);
const showWarning = ref(false);
const showUpdate = ref(false);
const emit = defineEmits(['updated']);
const PName = ref('');
const loading = ref(false);
const store = useStore();
import * as yup from 'yup';
import { useI18n } from 'vue-i18n';
const token = localStorage.getItem('auth_token');
const {t} = useI18n();
const props = defineProps({
    permissionId: {
        type: [String, Number],
        required: true,
    },
    permissionName: {
        type: [String],
        required: true,
    },
    showEdit: {
        type: [Boolean],
        required: true,
    }
});

const permissions = ref([]);

const errorsP = ref({});
const errorsR = ref({});

const formsP = ref({
    name: '', 
});

const formsR = ref({
    name: '',
});

function handlePermissionName(name) {
    PName.value = name;
}

useForm({
    validateOnInput: true,
});

const {
  value: name,
  errorMessage: nameError
} = useField('name', yup.string().required(t('permission_edit.form.error_name_required')))


watch(name, (newVal) => {
  formsR.name = newVal;
});

/* function addForm() {
  open.value= true;
    formsP.value.push({
        name: '',
    })
}

function removeForm(index) {
    formsP.value.splice(index, 1);
} */

/* function removePermission(permissionId) {
    const index = permissions.value.findIndex(permission => permission.id === permissionId);
    if(index !== -1) {
        permissions.value.splice(index, 1);
    }
}
 */
/* const fetchRoleById = async() => {
    try {
        const res = await axios.get(`api/role/${props.roleId}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        const resp = await axios.get('api/permissions', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        roles.value = res.data.roles;
        roles.value.permissions = res.data.permissions;
        permissions.value = resp.data.permissions;
    } catch (error) {
        console.error('Unable to fetch the role by id', error);
    }
} */

const fetchPermissionById = async() => {
    try {
        loading.value = true;
        const res = await axios.get(`api/permission/${props.permissionId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
            }
        });
        name.value = res.data.permission.name;
        loading.value = false;
    } catch (error) {
        console.error('Unable to fetch Permission', error);
    }
}

const fetchPermissions = async () => {
    try {
        loading.value = true;
        const res = await axios.get('api/permissions', {
            headers: {
                'Authorization': `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
            }
        });
        permissions.value = res.data.permissions;
        loading.value = false;
    } catch (error) {
        console.error('Unable to fetch permissions', error);
    }
}

const submitForAdd = async() => {
    errorsP.value = {}; //Create permission errors

    try {
        const response = await axios.post('api/permissions/create', {
            forms: formsP.value,
        }, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
            }
        });
        if(response.data.status == 'success') {
            cancel();
            emit('updated');
            showSuccessModal();
        }
    } catch (error) {
        if(error.response) {
            if(error.response && error.response.status == 422) {
               const { addErrors } =  handleAddPermissionErrors(error.response.data.errors);
               errorsP.value = addErrors;
            }
        }
    }
}

const submitForEdit = async() => {
    errorsR.value = {}; //edit permissions errors

    try {
        const response = await axios.post('api/permissions/create', {
            permissions: {
                id: props.permissionId,
                name: name.value
            }
        }, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
            }
        });
        if(response.data.status == 'success') {
            store.dispatch('auth/fetchUser');
            cancel();
            emit('updated');
            showSuccessModal();
        }
    } catch (error) {
        if(error.response) {
            if(error.response && error.response.status == 422) {
               const { editErrors } =  handleEditPermissionErrors(error.response.data.errors);
               errorsR.value = editErrors;
            }
        }
    }
}

const handleAddPermissionErrors = (errors) => {
    const addErrors = {};
    if (errors[`forms.name`]) addErrors.name = errors[`forms.name`][0];
    return { addErrors };
}

const handleEditPermissionErrors = (errors) => {
    const editErrors = {};
    if (errors[`permissions.name`]) editErrors.name = errors[`permissions.name`][0];
    return { editErrors };
} 

const cancel = () => {
    emit('close');
}

onMounted(async () => {
    await fetchPermissions();
    if(props.showEdit) {
        fetchPermissionById();
    } else {
        fetchPermissions();
    }
});
</script>

<template>
    <div class="w-full">
        <SuccessModal
            :show="showSuccess"
            :title="$t('permission_edit.success_create_title')"
            :message="$t('permission_edit.success_create_message')"
            @close="showSuccess=false"
        >
        </SuccessModal>

        <SuccessModal
            :show="showUpdate"
            :title="$t('permission_edit.success_update_title')"
            :message="$t('permission_edit.success_update_message')"
            @close="showUpdate=false"
        >
        </SuccessModal>

        <div class="bg-white p-6 rounded-lg">
            <!-- <div class="flex justify-end"> -->
                <!-- <h2 class="text-2xl font-semibold">Manage Permissions</h2> -->
                <!-- <button
                    type="button"
                    class=" inline-flex w-full justify-center rounded-md bg-red-600 px-2 py-2 text-sm font-semibold text-gray-100 hover:bg-red-800 sm:mt-0 sm:w-auto"
                    @click="cancel"
                    ref="cancelButtonRef"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div> -->

            <div class="mb-2" v-if="!showEdit">
                <div class="flex justify-between">
                    <h2 class="text-2xl mt-2 font-semibold">{{ $t('permission_edit.create_title') }}</h2>
                    <!-- <button type="button" @click="addForm" class="btn btn-primary mb-4 px-2 py-2 mt-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none text-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                      </svg>
                    </button> -->
                    <button
                    type="button"
                    class=" inline-flex w-full justify-center rounded-md bg-red-600 px-2 py-2 text-sm font-semibold text-gray-100 hover:bg-red-800 sm:mt-0 sm:w-auto"
                    @click="cancel"
                    ref="cancelButtonRef"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <Add :formsP="formsP" :errorsP="errorsP" @remove="removeForm" v-if="!showEdit" />
            </div>

            <div class=" mb-2 flex justify-between" v-if="props.showEdit">
                <h2 class="text-2xl font-semibold">{{ $t('permission_edit.edit_title') }}</h2>
                <button
                    type="button"
                    class=" inline-flex w-full justify-center rounded-md bg-red-600 px-2 py-2 text-sm font-semibold text-gray-100 hover:bg-red-800 sm:mt-0 sm:w-auto"
                    @click="cancel"
                    ref="cancelButtonRef"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form @submit.prevent="updatePermission" v-if="props.showEdit">
                <div class="mb-6" v-if="!loading">
                    <div class="flex pt-1 justify-between items-center">
                        <span class="text-gray-600 text-base">#{{ props.permissionId }}</span>
                        <!-- <div class="flex space-x-2">
                            <button
                                type="button"
                                @click="removePermission(permissions.id)"
                                class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                                >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div> -->
                    </div>
                    <div class="flex flex-col space-y-4">
                        <div class="w-full">
                            <label for="name" class="block text-lg font-medium text-gray-700 text-left">{{ $t('permission_edit.form.label_name') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                v-model="name" 
                                type="text" 
                                :placeholder="$t('permission_edit.form.placeholder_name')" 
                                class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            />
                            <div v-if="errorsR?.name" class="text-red-500 text-sm mt-2">
                                {{ errorsR.name }}
                            </div>
                            <div v-else-if="nameError" class="text-red-500 text-sm mt-2">
                                {{ nameError }}
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="items-center p-4">
                    <button type="button" class="flex" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 animate-spin">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    <div class="text-base">{{ $t('permission_edit.loading.processing') }}</div>
                    </button>
                </div>
                <div class="flex space-x-6 text-md mb-4" v-if="permissions.length <= 0 && !loading">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    {{ $t('permission_edit.empty.message') }}
                </div>
                <div class="mt-4">
                <button 
                    type="button" 
                    @click="submitForEdit"
                    class="w-full py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 text-sm"
                    >
                    {{ $t('permission_edit.buttons.edit') }}
                </button>
            </div>
            </form>
            <div class="mt-4" v-if="!props.showEdit">
                <button 
                    type="button" 
                    @click="submitForAdd"
                    class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                    >
                    {{ $t('permission_edit.buttons.submit') }}
                </button>
            </div>
        </div>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css">

</style>