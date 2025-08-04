<template>
  <div class="p-6">
    <h2 class="text-2xl text-left font-semibold mb-4">{{ $t('dashboard_s.greeting', { name: userName }) }}</h2>
    <p class="text-lg text-gray-600 mb-6"></p>

    <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ $t('dashboard_s.totalUsers') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalUsers }}</p>
      </div>

      
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ $t('dashboard_s.totalPosts') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalPosts }}</p>
      </div>

      
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ $t('dashboard_s.yourPosts') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ userPosts }}</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ $t('dashboard_s.totalSocial') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalsocial }}</p>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { values } from 'lodash';
import { onMounted, ref, watch } from 'vue'

const userName = ref('User');
const totalUsers = ref('')
const totalPosts = ref('')   
const userPosts = ref('')
const totalsocial = ref('')     
/* const postEngagement = ref(85)  */

const name = localStorage.getItem('user_name');
const token = localStorage.getItem('auth_token');



const getDataCount = async() => {
  const response = await axios.get(`api/list`, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  if(name) {
      userName.value = name;
  }
  totalUsers.value = response.data.user_count;
  totalPosts.value = response.data.totalpost_count;
  userPosts.value  = response.data.mypost_count;
  totalsocial.value = response.data.socialCount;
}

onMounted(getDataCount);
watch(() => userName, async(value) => {
    if(value) {
      await getDataCount();
      console.log(userName);
    } 
})
</script>

<style scoped>
</style>
