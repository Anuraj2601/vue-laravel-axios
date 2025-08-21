<template>
  <div class="p-6">
    <h2 class="text-2xl text-left font-semibold mb-4"> {{ t('dashboard_s.greeting') }} {{ userName }}! 😍</h2>
    <p class="text-lg text-gray-600 mb-6"></p>

    <div :class="['grid sm:grid-cols-1 md:grid-cols-2 gap-6', hasRole('superadmin') ? 'lg:grid-cols-3' : 'lg:grid-cols-4']">
      
      <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ t('dashboard_s.totalUsers') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalUsers }}</p>
      </div>

      
      <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ t('dashboard_s.totalPosts') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalPosts }}</p>
      </div>

      
      <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ t('dashboard_s.yourPosts') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ userPosts }}</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ t('dashboard_s.totalSocial') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalsocial }}</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300" v-if="hasRole('superadmin')">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ t('dashboard_s.totalRoles') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalRoles }}</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300" v-if="hasRole('superadmin')">
        <h3 class="text-xl font-medium text-gray-700 mb-2">{{ t('dashboard_s.totalPermissions') }}</h3>
        <p class="text-3xl font-semibold text-gray-900">{{ totalPermissions }}</p>
      </div>
      
    </div>

    <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
      <div class="mt-8 list-container bg-white rounded-lg shadow pb-6" v-if="hasPermission('view_latest_posts')">
        <h2 class="text-xl font-semibold mb-4">{{ t('dashboard_s.latestPosts') }}</h2>
          <div class="flex items-center text-left font-semibold text-gray-800 mt-6 px-10">
            <span class="flex-1 text-base">{{ t('dashboard_s.postTitle') }}</span>
            <span class="flex-1 text-base">{{ t('dashboard_s.postAuthor') }}</span>
            <span class="flex-1 text-base">{{ t('dashboard_s.postDate') }}</span>
          </div>
          <div v-for="post in latestPosts" :key="post.id" class="flex items-center text-left font-semibold text-gray-800 mt-6 px-10 border-gray-200">
            <span class="flex-1 text-sm">{{ post.name }}</span>
            <span class="flex-1 text-sm">{{ post.user?.name || 'N/A' }}</span>
            <span class="flex-1 text-sm">{{ new Date(post.created_at).toLocaleDateString() }}</span>
          </div>
      </div>

      <div class="mt-8 list-container bg-white rounded-lg shadow pb-6" v-if="hasPermission('view_latest_users')">
        <h2 class="text-xl font-semibold mb-4">{{ t('dashboard_s.newUsers') }}</h2>
        <div class="flex items-center text-left font-semibold text-gray-800 mt-6 px-10">
            <span class="flex-1 text-base">{{ t('dashboard_s.userName') }}</span>
            <span class="flex-1 text-base">{{ t('dashboard_s.userEmail') }}</span>
           <!--  <span class="flex-1 text-sm">Role</span> -->
          </div>
          <div v-for="user in latestUsers" :key="user.id" class="flex items-center text-left font-semibold text-gray-800 mt-6 px-10 border-gray-200">
            <span class="flex-1 text-sm">{{ user.name }}</span>
            <span class="flex-1 text-sm">{{ user.email }}</span>
            <!-- <span class="flex-1 text-sm">{{ user }}</span> -->
          </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { values } from 'lodash';
import { onMounted, ref, watch } from 'vue'
import { useStore } from 'vuex';
import { useI18n } from 'vue-i18n';

const userName = ref('User');
const totalUsers = ref('')
const totalPosts = ref('')   
const userPosts = ref('')
const totalsocial = ref('')   
const totalRoles = ref('');
const totalPermissions = ref('');  
const latestPosts = ref([]);
const latestUsers = ref([]);
/* const postEngagement = ref(85)  */
const { t } = useI18n();
const name = localStorage.getItem('user_name');
const token = localStorage.getItem('auth_token');

const store = useStore();

const hasRole = (role) => store.getters['auth/hasRole'](role);
const hasPermission = (permission) => store.getters['auth/hasPermission'](permission);


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
  totalRoles.value = response.data.totalRoles;
  totalPermissions.value = response.data.totalPermissions;
  latestPosts.value = response.data.latestPosts;
  latestUsers.value = response.data.newUsers;
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
