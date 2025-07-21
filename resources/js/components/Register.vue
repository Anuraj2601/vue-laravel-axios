<template>
  <div>
    <section class="bg-gray-100 py-12 mt-25">
      <div class="container mx-auto px-4">
        <div class="lg:flex lg:justify-between lg:items-center">
          <div class="lg:w-1/2 mb-10 lg:mb-0">
            <h1 class="text-4xl font-extrabold leading-tight mb-4">
              User Post Management System
              <br />
              <span class="text-blue-600">for your business</span>
            </h1>
            <p class="text-gray-600">
               System allows users to create, edit, and delete their own posts, while administrators can manage all posts and users
            </p>
          </div>

          
          <div class="lg:w-1/2">
            <div class="bg-white p-8 rounded-lg shadow-lg">
              <form @submit.prevent="registerUser">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="form3Example1" class="block text-sm text-left font-medium text-gray-700">Name<span class="text-red-400 text-base font-medium">*</span></label>
                    <input 
                      type="text" 
                      v-model="name" 
                      id="form3Example1" 
                      class="mt-2 p-3 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    />
                    <span v-if="errors.name" class="text-red-500 text-left text-sm">{{ errors.name }}</span>
                  </div>
                  <div>
                    <label for="form3Example2" class="block text-left text-sm font-medium text-gray-700">Email<span class="text-red-400 text-base font-medium">*</span></label>
                    <input 
                      type="email" 
                      v-model="email" 
                      id="form3Example2" 
                      class="mt-2 p-3 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    />
                    <span v-if="errors.email" class="text-red-500 text-left text-sm">{{ errors.email }}</span>
                  </div>
                </div>

                <div class="mb-4">
                  <label for="form3Example3" class="block text-left text-sm font-medium text-gray-700">Password<span class="text-red-400 text-base font-medium">*</span></label>
                  <input 
                    type="password" 
                    v-model="password" 
                    id="form3Example3" 
                    class="mt-2 p-3 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                  />
                  <span v-if="errors.password" class="text-red-500 text-left text-sm">{{ errors.password }}</span>
                </div>

                <div class="mb-4">
                  <label for="form3Example4" class="block text-left text-sm font-medium text-gray-700">Password Confirmation<span class="text-red-400 text-base font-medium">*</span></label>
                  <input 
                    type="password" 
                    v-model="password_confirmation" 
                    id="form3Example4" 
                    class="mt-2 p-3 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                  />
                  <span v-if="errors.password_confirmation" class="text-red-500 text-left text-sm">{{ errors.password_confirmation }}</span>
                </div>

                <button 
                  type="submit" 
                  class="w-full py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                >
                  Sign Up
                </button>
                <p class="text-sm mt-2">
                  Already have an account? 
                  <router-link to="/login" class="text-blue-500 hover:text-blue-600">Sign In</router-link>
                </p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { useRouter } from 'vue-router';


const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const router = useRouter();

const errors = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});



const registerUser = async () => {

    errors.value = {};

    try {
        const response = await axios.post('/api/register', {
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        });
        
        if(response.data.status == 'success') {
            router.push('/login');
        }
    } catch (error) {
        if(error.response) {
            if(error.response.status === 422) {
                const backendErrors = error.response.data.error;
                for (const field in backendErrors) {
                    errors.value[field] = backendErrors[field]; // Assigning error message for each field
                }
            } else {
                console.error('Unexpected error: ', error);
            }
        }
    }
}
</script>

<style>
.error {
    font-size: medium;
}
</style>
