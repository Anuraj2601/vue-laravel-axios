<script setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import Edit from './edit.vue';
import { useStore } from 'vuex';
import WarningModal from '../modals/WarningModal.vue';

const rolesTypes = ref([]);
const loading = ref(false);
const edit = ref(false);
const showEditForm = ref(false);
const showEditRForm = ref(false);
const showSuccess = ref(false);
const showWarning = ref(false);
const selectedRoleId = ref('');
const roleToDelete = ref(null);
const selectedRoleName = ref('');

const token = localStorage.getItem('auth_token');

const fetchRoles = async () => {
    try {
        loading.value = true;
        const res = await axios.get('api/roles', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        rolesTypes.value = res.data.roles;

        loading.value = false;
    } catch (error) {
        console.error('Unable to fetch roles', error);
    }
}

const store = useStore();

const hasRole = (role) => store.getters['auth/hasRole'](role);
const hasPermission = (permission) => store.getters['auth/hasPermission'](permission);


function addRole() {
    edit.value = true;
    showEditForm.value = false;
}

function confirmDelete(id) {
    roleToDelete.value = id;
    showWarning.value = true;
}

function editRole(id, name) {
    selectedRoleId.value = id;
    selectedRoleName.value = name;
    edit.value = true;
    showEditForm.value = true;
}

const deleteRole = async() => {
    try {
        showWarning.value = false;
        const res = await axios.delete(`api/roles/delete/${roleToDelete.value}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        if(res.data.status == 'success') {
            fetchRoles();
        }
    } catch (error) {
        console.error('Delete permission error', error);
    }
}

function closeEdit() {
    edit.value = false;
}

onMounted(fetchRoles);
</script>

<template>
    <div class="title text-center mb-6">
        <h2 class="text-2xl font-semibold">{{ $t('roles_s.title') }}</h2>
    </div>

    <div class="title flex justify-between items-center" v-if="hasRole('superadmin')">
        <button 
            class="bg-gray-200 text-black mr-32 px-2 py-2 rounded-lg focus:outline-none hover:bg-gray-800 hover:text-gray-100 focus:ring-2 focus:ring-black-500 flex items-center space-x-2"
            @click="addRole()"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
                {{ $t('roles_s.addRole') }}
        </button>
    </div>

    <WarningModal
        :show="showWarning"
        :title="$t('roles_s.deleteRoleTitle')"
        :message="$t('roles_s.deleteRoleMessage')"
        @close="showWarning=false"
        @confirm="deleteRole"
    >

    </WarningModal>

    <div class="overflow-x-auto ml-6 mr-10">
        <div class="list-container">
            <div class="flex items-center text-left font-semibold text-gray-800 mt-6 px-10">
                <span class="flex-1 text-sm">{{ $t('roles_s.name') }}</span>
                <span class="flex-1/2 text-sm">{{ $t('roles_s.permissions') }}</span>
                <span class="w-auto text-sm">{{ $t('roles_s.actions') }}</span>
            </div>
            <ul class="w-full bg-white p-4 space-y-2 rounded-lg mt-2">
                <li v-for="(role, idx) in rolesTypes"
                    :key="idx"
                    v-if="!loading" 
                    class="py-2 cursor-pointer bg-gray-100 flex justify-between items-center border-b-2 border-gray-200 rounded-lg">
                    <span class="flex-1 text-lg text-left ml-6 truncate">{{ role.name }}</span>
                    <span class="flex-1/2 text-sm overflow-y-auto max-h-12 flex-wrap text-left space-x-2">
                        <span
                            v-for="(permission, idx) in role.permissions"
                            :key="idx"
                            class="inline-block bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs font-semibold"
                        >
                            {{ permission.name }}
                        </span>
                    </span>
                    <div class="flex space-x-2">
                        <button
                            @click.stop="editRole(role.id)"
                            class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-400 text-sm flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                                {{ $t('roles_s.edit') }}
                        </button>
                        <button
                            @click.stop="confirmDelete(role.id)"
                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-400 text-sm flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                                {{ $t('roles_s.delete') }}
                        </button>
                    </div>
                </li>
                <li v-else class="items-center p-4">
                    <button type="button" class="flex" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 animate-spin">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <div class="text-base">{{ $t('roles_s.processing') }}</div>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    

    <TransitionRoot as="template" :show="edit">
        <Dialog class="relative z-10">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-gray-500/75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                >
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel
                                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all
                                sm:my-8 w-full sm:max-w-2xl h-auto"
                        >
                    
                            <Edit :roleId="selectedRoleId" :showEdit="showEditForm" :roleName="selectedRoleName" @close="closeEdit" @updated="fetchRoles" /> 
                    
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<style scoped>
.title {
    display: flex;
    justify-content:space-between;
    margin-top: 20px;
    margin-left: 40px;
    margin-right: 120px;
    text-align: left;
    font-size: small;
}
</style>