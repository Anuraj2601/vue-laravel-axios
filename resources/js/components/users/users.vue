<template>
        <div class="title text-center mb-6">
            <h2 class="text-2xl font-semibold">Users List</h2>
        </div>
        <Modal
                ref="modalRef"
                :title="modalTitle"
                :message="modalMessage"
                modal-id="feedbackModal"
            />

        
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 ml-20 mr-20">
                <div v-for="(user, index) in users" :key="user.id" class="bg-white shadow rounded-lg p-6 space-y-4">
                    <div class="flex items-center justify-between">
                    <div class="text-left">
                        <h2 class="text-lg font-semibold text-gray-800">{{ user.name }}</h2>
                        <p class="text-sm text-gray-600">{{ user.email }}</p>
                    </div>
                    <span class="text-gray-400 text-sm">#{{ index + 1 }}</span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                    <span 
                        v-for="(role, idx) in user.role" 
                        :key="idx"
                        class="inline-flex items-center bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full"
                    >
                        {{ role }}
                    </span>
                    </div>

                    <div class="flex space-x-3 pt-2">
                    <button
                        class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-400 text-sm flex items-center"
                        @click="openDrawer(user.id)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        Edit
                    </button>
                    <button
                        class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-400 text-sm flex items-center"
                        @click="confirmDelete(user.id)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Delete
                    </button>
                    </div>
                </div>
                </div>


            <WarningModal
        :show="showWarning"
        title="Delete User"
        message="Are you sure, you want to delete this user? This cannot be undone."
        @close="showWarning=false"
        @confirm="deleteUser"
      >

      </WarningModal>

      <EditDrawer :show="showDrawer" :userId="selectedUserId"  @close="showDrawer=false" @updated="fetchUsers" />
</template>

<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import Modal from "../modals/Modal.vue";
import WarningModal from "../modals/WarningModal.vue";
import EditDrawer from "./EditDrawer.vue";

const users = ref([]);
const router = useRouter();
const showWarning = ref(false);
const userToDelete = ref(null);
const showDrawer = ref(false);
const selectedUserId = ref(null);

const modalTitle = ref('');
const modalMessage = ref('');
const modalRef = ref(null);

const showModal = (title, message) => {
  modalTitle.value = title;
  modalMessage.value = message;
  modalRef.value?.show();
};

const token = localStorage.getItem('auth_token');

function openDrawer(id) {
    selectedUserId.value = id;
    showDrawer.value = true;
}

const axiosInstance = axios.create({
    headers: {
      Authorization: `Bearer ${token}`
    }
});

function confirmDelete(id) {
    userToDelete.value = id;
    showWarning.value = true;
}

const fetchUsers = async () => {
    try {
        const response = await axiosInstance.get('/api/');
        users.value = response.data.Users; 
    } catch (response) {
        if(response.status == '403') {
            showModal('Error','You do not have permission to access this resource');
            router.push('/dashboard');
        }
        console.error('Fetching user details error', response);
    }
} 

/* const editUser = (userId) => {
    try {
        router.push(`/edit-user/${userId}`);
    } catch (error) {
        console.error('Unable to redirect edit user', error);
    }
    
} */


const deleteUser = async (userId) => {
    try {
        showWarning.value = false;
        const response = await axiosInstance.delete(`/api/delete/${userId}`);

        if(response.data.status == 'success') {
            fetchUsers();
        }
    } catch (error) {
        console.error("Delete user Error" , error)
    }
}

onMounted(fetchUsers);
</script>

<style scoped>
.custom-badge {
    background-color: green;
    color: white;
    width: 86px;
    height: 23px;
    margin: 10px;
}

.form-label {
    text-align: left;
    font-size: large;
    display: flex;
}

.table {
    font-size: large;
    text-align: center;
}

.title {
    display: flex;
    justify-content:space-between;
    margin-top: 20px;
    margin-left: 120px;
    margin-right: 120px;
    text-align: left;
    font-size: small;
}

.pagination {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000; 
  background-color: white; 
  width: 100%;
  padding: 10px 0; 
}
</style>