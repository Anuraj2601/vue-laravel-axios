<template>
  <section class="h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">
      <button 
        @click="router.push('/login')" 
        class="top-4 left-4 text-blue-500 hover:cursor-pointer text-sm flex items-center"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Back
      </button>
      <h2 class="text-xl font-bold mb-4">Forgot Password</h2>
      <div v-if="!loading">
        <form @submit.prevent="onSubmit">
        <div class="mb-4">
          <label class="block text-left text-sm font-medium text-gray-700">Email address</label>
          <input
            v-model="email"
            type="email"
            class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500 text-sm"
            placeholder="Enter your email"
          />
          <span class="text-red-500 text-sm text-left">{{ emailError }}</span>
        </div>
        <button
          type="submit"
          class="w-full py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
        >
          Send Reset Link
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
      <!-- <p v-if="message" class="text-green-500 mt-3 text-left text-sm">{{ message }}</p>
      <p v-if="error" class="text-red-500 mt-3 text-left text-sm">{{ error }}</p> -->
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
import { reactive, ref } from 'vue';
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import { useRouter } from 'vue-router';
import SuccessModal from './modals/SuccessModal.vue';
import ErrorModal from './modals/ErrorModal.vue';
import { useI18n } from 'vue-i18n';

/* const email = ref(''); */
const message = ref('');
const error = ref('');
const router = useRouter();
const showSuccess = ref(false);
const showError = ref(false);
const loading = ref(false);
const { t } =  useI18n();

const schema = yup.object({
    email: yup.string().email('Enter a valid email address').required('Email field is required'),
})

const {handleSubmit, setErrors} = useForm({
    validationSchema: schema
})

const { value:email , errorMessage: emailError} = useField('email');

const submitForm = async() => {
  message.value = '';
  error.value = '';
    try {
        loading.value = true;
        const res = await axios.post('/api/forgot-password', {
            email: email.value,
        });
        message.value = res.data.status;
        loading.value = false;
        showSuccess.value = true;
    } catch (err) {
          loading.value = false;
          error.value = err.response?.data?.email || 'Something went wrong';
          showError.value = true;
    }
}

function handleCloseSuccess() {
  showSuccess.value = false;
  setTimeout(() => {
    router.push('/login');
  }, 500);
}

const onSubmit = handleSubmit(submitForm)
</script>