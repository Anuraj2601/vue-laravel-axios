<script setup>
import { onMounted, ref } from 'vue';
import SuccessModal from '../modals/SuccessModal.vue';
import Add from './add.vue';
import axios from 'axios';
import Multiselect from 'vue-multiselect';
import WarningModal from '../modals/WarningModal.vue';
import { useStore } from 'vuex';
const showSuccess = ref(false);
const showWarning = ref(false);
const showUpdate = ref(false);
const emit = defineEmits(['updated']);
const RName = ref('');
const roleToDelete = ref(null);
const loading = ref(false);

const token = localStorage.getItem('auth_token');

const props = defineProps({
    roleId: {
        type: [String, Number],
        required: true,
    },
    roleName: {
        type: [String],
        required: true,
    },
    showEdit: {
        type: [Boolean],
        required: true,
    }
});

const roles = ref([]);

const permissions = ref([]);

const errorR = ref({}); // Edit  roles error
const errorsR = ref({})  // create role error

const formsR = ref({
    name: '', 
    permissions: []
});

const formsE = ref({
    name: '',
    permissions: []
})

function handleRoleName(name) {
    RName.value = name;
}

function showSuccessModal() {
    showSuccess.value = true;
    setTimeout(() => {
        emit('created')
        showSuccess.value = false
        emit('close')
    }, 1500)
}

/* function addForm() {
  open.value= true;
    formsR.value.push({
        name: '',
        permissions: []
    })
}

function removeForm(index) {
    formsR.value.splice(index, 1);
} */

function removeRole(roleId) {
    const index = roles.value.findIndex(role => role.id === roleId);
    if(index !== -1) {
        roles.value.splice(index, 1);
    }
}

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

const fetchRoles = async () => {
    try {
        loading.value = true;
        const res = await axios.get('api/roles', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        roles.value = res.data.roles;
        roles.value.permissions = res.data.roles.permissions;
        permissions.value = res.data.permissions;
        loading.value = false;
    } catch (error) {
        console.error('Unable to fetch roles', error);
    }
}

const fetchRoleById = async() => {
    try {
        loading.value = true;
        const res = await axios.get(`api/role/${props.roleId}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        formsE.value = res.data.roles;
        formsE.value.permissions = res.data.roles.permissions;
        /* permissions.value = res.data.permissions; */
        loading.value = false;
    } catch (error) {
        console.error('Unable to fetch roles', error);
    }
}

function confirmDelete(id) {
    roleToDelete.value = id;
    showWarning.value = true;
}

const store = useStore();

const hasRole = (role) => store.getters['auth/hasRole'](role);
const hasPermission = (permission) => store.getters['auth/hasPermission'](permission);


const submitFormAdd = async() => {
    try {
        const response = await axios.post('api/roles/create', {
            role: formsR.value,
        }, {
            headers: {
                "Authorization": `Bearer ${token}`
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
               const { addErrors } =  handleAddRoleErrors(error.response.data.errors);
               errorsR.value = addErrors;
            }
        }
        else {
            console.error('Unable to create the role',error);
        }
    }
}

const submitFormEdit = async() => {
    try {
        const response = await axios.post('api/roles/create', {
            roles: formsE.value
        }, {
            headers: {
                "Authorization": `Bearer ${token}`
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
               const { editErrors } =  handleEditRoleErrors(error.response.data.errors);
               errorR.value = editErrors;
            }
        }
        else {
            console.error('Unable to create the role',error);
        }
    }
}



const handleAddRoleErrors = (errors) => {
    const addErrors = {};
    if (errors[`role.name`]) addErrors.name = errors[`role.name`][0];
    if (errors[`role.permissions`]) addErrors.permissions = errors[`role.permissions`][0];
    return { addErrors };
}

const handleEditRoleErrors = (errors) => {
    const editErrors = {};
    if (errors[`roles.name`]) editErrors.name = errors[`roles.name`][0];
    if (errors[`roles.permissions`]) editErrors.permissions = errors[`roles.permissions`][0];
    return { editErrors };
}

const fetchPermissions = async() => {
    try {
        const res = await axios.get('api/permissions', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        permissions.value = res.data.permissions;
    } catch (error) {
        console.error('Unable to fetch Permissions', error);
    }
}

const cancel = () => {
    emit('close');
}

onMounted(async () => {
    await fetchPermissions();
    if(props.showEdit) {
        fetchRoleById();
    }else {
        fetchRoles();
    }
});
</script>

<template>
    <div class="w-full">
        <SuccessModal
            :show="showSuccess"
            :="$t('roles_edit.success_create.title')"
            :message="$t('roles_edit.success_create.message')"
            @close="showSuccess=false"
        >
        </SuccessModal>

        <SuccessModal
            :show="showUpdate"
            :title="$t('roles_edit.success_update.title')"
            :message="$t('roles_edit.success_update.message')"
            @close="showUpdate=false"
        >
        </SuccessModal>

        <WarningModal
        :show="showWarning"
        :title="$t('roles_edit.warning.title')"
        :message="$t('roles_edit.warning.message')"
        @close="showWarning=false"
        @confirm="deleteRole"
      >

      </WarningModal>

        <div class="bg-white p-6 rounded-lg mt-4">
            

            <div class="mb-2 justify-between" v-if="!props.showEdit">
                <div class="flex justify-between">
                    <h2 class="text-2xl font-semibold">{{ $t('roles_edit.create_title') }}</h2>
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
                <Add :formsR="formsR" :errorsR="errorsR" @remove="removeForm" v-if="!props.showEdit" />
            </div>

            <div class="mb-2 flex justify-between" v-if="props.showEdit">
                <h2 class="text-2xl font-semibold">{{$t('roles_edit.edit_title')}}</h2>
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
            <form @submit.prevent="updateRole" v-if="props.showEdit">
                <div class="mb-6" v-if="!loading">
                    <div class="flex pt-1 justify-between items-center">
                        <span class="text-gray-600 text-base">#{{ props.roleId }}</span><!-- <span class="text-gray-800 text-base">{{ props.showUserEdit? '' : post.username }}</span> -->
                    </div>
                    <div class="flex flex-col space-y-4">
                        <div class="w-full">
                            <label for="name" class="block text-lg font-medium text-gray-700 text-left">{{ $t('roles_edit.form.name_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input 
                                v-model="formsE.name" 
                                type="text" 
                                :placeholder="$t('roles_edit.form.name_placeholder')" 
                                class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            />
                            <div v-if="errorR?.name" class="text-red-500 text-sm mt-2">
                                {{ $t('roles_edit.errors.name') }}
                            </div>
                        </div>
                    </div>

            
                    <div class="mb-4">
                        <label for="tags" class="block text-lg font-medium text-gray-700 text-left">Permissions<span class="text-red-400 text-base font-medium">*</span></label>
                        <Multiselect
                            v-model="formsE.permissions"
                            :options="permissions"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="true"
                            
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
                            <div class="multiselect__clear" v-if="formsE.permissions.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                        </template>
                        </Multiselect>
                        <div v-if="errorR?.permissions" class="text-red-500 text-sm mt-2">
                            {{ $t('roles_edit.errors.permissions') }}
                        </div>
                    </div>
                </div>
                <div v-else class="items-center p-4">
                    <button type="button" class="flex" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 animate-spin">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    <div class="text-base">{{ $t('roles_edit.form.processing') }}</div>
                    </button>
                </div>
                <div class="flex space-x-6 text-md mb-4" v-if="formsE.length <= 0 && !loading">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    {{ $t('roles_edit.form.no_roles') }}
                </div>
                <div class="mt-6 mb-2">
                <button 
                    type="button" 
                    @click="submitFormEdit"
                    class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    >
                    {{ $t('roles_edit.form.edit') }}
                </button>
            </div>
            </form>
            <div class="mt-4" v-if="!props.showEdit">
                <button 
                    type="button" 
                    @click="submitFormAdd"
                    class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                    >
                    {{ $t('roles_edit.form.submit') }}
                </button>
            </div>
        </div>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css">

</style>