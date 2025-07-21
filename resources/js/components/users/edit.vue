<template>
        <div class="container mx-auto px-4 mt-10">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit User</h2>
        <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
            
            <form @submit.prevent="updateUser">
                <div class="mb-4">
                    <label for="name" class="block text-lg font-medium text-gray-700 text-left">Name</label>
                    <input v-model="user.name" type="text" placeholder="Name" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                    <span v-if="errors.name" class="text-red-500 text-sm mt-2">
                            {{ errors.name }}
                    </span>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-lg font-medium text-gray-700 text-left">Email</label>
                    <input v-model="user.email" type="email" placeholder="Email" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                    <span v-if="errors.email" class="text-red-500 text-sm mt-2">
                            {{ errors.email }}
                    </span>
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-lg font-medium text-gray-700 text-left">Role</label>
                    <select v-model="user.user_role" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" id="role">
                        <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                    </select>
                    <span v-if="errors.user_role" class="text-red-500 text-sm mt-2">
                            {{ errors.user_role }}
                    </span>
                </div>
                
                <div class="mt-6">
            <button 
              type="submit" 
              class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
              Update User
            </button>
          </div>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import axios from 'axios';
    import { useRoute, useRouter } from 'vue-router';

    const emit = defineEmits(['updated']);
    const user = ref({
        name: '',
        email: '',
        id: '',
        user_role: '',
    });

    const errors = ref({
        name: '',
        email: '',
        user_role: ''
    });

    const router = useRouter();
    const route  = useRoute();
    const token = localStorage.getItem('auth_token');
    const userId = route.params.id;
    const roles = ref([]);

    const props = defineProps({
        userId: {
            type: [String, Number],
            required: true,
        }
    })

    const editUser = async () => {
        try {
            const response = await axios.get(`/api/edit/${props.userId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            roles.value = response.data.roles;
            user.value  = response.data.user;
            user.value.user_role = response.data.user_role[0];
        } catch (error) {
            console.error("Retrieve User details error" , error);
        }
    }
    
    const updateUser = async (id) => {
        try {
            
            const response = await axios.put( `/api/update/${user.value.id}`, {
                name: user.value.name,
                email: user.value.email,
                role: user.value.user_role
            },{
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        );
            if(response.data.status == 'success') {
                emit('updated')
            }
            console.log(response.data);
        } catch (error) {
            if(error.response) {
                if (error.response && error.response.status === 422) {
                
                errors.value = error.response.data.error; 


                console.error('Validation errors:', errors.value);
                }
            }
            console.log("User Updated failed. " , error);
        }
    }

    onMounted(editUser);

</script>

<style>

</style>