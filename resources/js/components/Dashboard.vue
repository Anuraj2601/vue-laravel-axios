<template>
  <div class="p-6">
    <h2 class="text-2xl text-left font-semibold mb-4">Hi, {{ userName }}! 😍</h2>
    <p class="text-lg text-gray-600 mb-6"></p>

    <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-medium text-gray-700 mb-2">Total Users</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalUsers }}</p>
      </div>

      
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-medium text-gray-700 mb-2">Total Posts</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalPosts }}</p>
      </div>

      
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-medium text-gray-700 mb-2">Your Posts</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ userPosts }}</p>
      </div>

     
      
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue'

const userName = ref('User');
const totalUsers = ref('')
const totalPosts = ref('')   
const userPosts = ref('')      
/* const postEngagement = ref(85)  */

const name = localStorage.getItem('user_name');
const token = localStorage.getItem('auth_token');

if(name) {
    userName.value = name;
}

const getDataCount = async() => {
  const response = await axios.get(`api/list`, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  totalUsers.value = response.data.user_count;
  totalPosts.value = response.data.totalpost_count;
  userPosts.value  = response.data.mypost_count

}

onMounted(getDataCount);

</script>

<style scoped>
</style>
