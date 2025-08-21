<template>
  <section class="h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">
      <h2 class="text-xl font-bold mb-4">Reset Password</h2>
      <div v-if="!loading">
        <form @submit.prevent="onSubmit">
        <input type="hidden" v-model="token" />
        <div class="mb-4">
          <label class="block text-left text-base font-medium text-gray-700">Email</label>
          <input v-model="email" disabled="true" type="email" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500 text-sm" />
          <span class="block text-red-500 text-sm">{{ errors.email }}</span>
        </div>
        <div class="mb-4">
          <label class="block text-left text-sm font-medium text-gray-700">New Password</label>
          <input v-model="password" type="password" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500 text-sm" />
          <span class="block text-red-500 text-left text-sm">{{ errors.password }}</span>
        </div>
        <div class="mb-4">
          <label class="block text-left text-sm font-medium text-gray-700">Confirm Password</label>
          <input v-model="password_confirmation" type="password" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500 text-sm" />
          <span class="block text-red-500 text-sm">{{ errors.password_confirmation }}</span>
        </div>
        <button type="submit" class="w-full py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
          Reset Password
        </button>
      </form>
      </div>
      <div v-else>
        <button type="button" class="flex" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 animate-spin">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <div class="text-base">{{ $t('permissions_s.processing') }}</div>
        </button>
      </div>
      <!-- <p v-if="message" class="text-green-500 text-left text-sm mt-3">{{ message }}</p>
      <p v-if="error" class="text-red-500 text-left text-sm mt-3">{{ error }}</p> -->
    </div>
  </section>

  <SuccessModal
      :show="showSuccess"
      :title="'Success'"
      :message="message"
      @close="handleCloseSuccess"
    />

    <ErrorModal 
            :show="showError"
            :title=" 'Error' "
            :message="error"
            @close="showError=false"
          />
</template>

<script setup>
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import SuccessModal from './modals/SuccessModal.vue';
import ErrorModal from './modals/ErrorModal.vue';
import { useI18n } from 'vue-i18n';

const token = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const message = ref('');
const error = ref('');
const route = useRoute();
const router = useRouter();
const errors = reactive({
  email: '',
  password: '',
  password_confirmation: '',
});
const showError = ref(false);
const showSuccess = ref(false);
const loading = ref(false);
const {t} = useI18n();

const onSubmit = async() => {
  message.value = '';
  error.value = '';
  errors.email = '';
  errors.password = '';
  errors.password_confirmation = '';
    try {
      loading.value = true;
        const res = await axios.post('api/reset-password', {
            token: token.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        })
        message.value = res.data.status;
        loading.value = false;
        showSuccess.value = true;
        setTimeout(() => {
          router.push('/login');
        }, 3000);
    } catch (err) {
        if (err.response?.status === 422) {
          loading.value = false;
          const backendErrors = err.response.data.errors;
          for (const key in backendErrors) {
            if (backendErrors[key] && backendErrors[key].length > 0) {
              errors[key] = backendErrors[key][0];
            }
          }
        } else {
          loading.value = false;
          error.value = err.response?.data?.email || 'Something went wrong';
          showError.value = true;
        }
    }
}

function handleCloseSuccess() {
  showSuccess.value = false;
  setTimeout(() => {
    router.push('/login');
  }, 500);
}

onMounted(() => {
  token.value = route.query.token || '';
  email.value = route.query.email || '';
})
</script>