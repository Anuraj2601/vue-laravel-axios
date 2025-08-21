<template>
  <section class="h-screen flex items-center justify-center bg-gray-100">
    <div class="flex flex-wrap justify-center items-center w-full max-w-6xl">
      <div class="w-full md:w-1/2 lg:w-1/3">
        <img 
          src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
          alt="Sample image"
          class="w-full h-auto rounded-lg"
        />
      </div>

      <div class="w-full md:w-1/2 lg:w-1/3 p-6 bg-white rounded-lg shadow-lg">
        <form @submit.prevent="onSubmit">
          <!-- Modal Component -->
          <ErrorModal 
            :show="showError"
            title="Error"
            message="Incorrect Username or Password"
            @close="showError=false"
          />

          <!-- Email input -->
          <div class="mb-4">
            <label for="form3Example3" class="block text-left text-sm font-medium text-gray-700">Email address</label>
            <input 
              v-model="email" 
              type="email" 
              id="form3Example3" 
              class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              placeholder="Enter a valid email address" 
            />
            <span class="block text-red-500 text-left text-sm">{{ emailError }}</span>
          </div>

          <!-- Password input -->
          <div class="mb-4">
            <label for="form3Example4" class="block text-left text-sm font-medium text-gray-700">Password</label>
            <input 
              v-model="password" 
              type="password" 
              id="form3Example4" 
              class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              placeholder="Enter password" 
            />
            <span class="block text-red-500 text-left text-sm">{{ passwordError }}</span>
          </div>
          <p class="text-sm mt-2 flex justify-end">
            <router-link to="/forgot-password" class="text-red-500 hover:text-red-600">
              Forgot Password?
            </router-link>
          </p>

          <div class="text-center mt-4">
            <button 
              type="submit" 
              class="w-full py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
            >
              Login
            </button>
            <p class="text-sm mt-2">
              Don't have an account? 
              <router-link to="/register" class="text-blue-500 hover:text-blue-600">Register</router-link>
            </p>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>


<script setup>
    import { ref } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    import Modal from './modals/Modal.vue';
    import store from '../store';
    import WarningModal from './modals/WarningModal.vue';
    import ErrorModal from './modals/ErrorModal.vue';
    import { useForm, useField, ErrorMessage } from 'vee-validate'
    import * as yup from 'yup'
    import { useI18n } from 'vue-i18n';

   /*  const email = ref('');
    const password = ref(''); */
    const router = useRouter();
    const errorl = ref('');
    /* const errors = ref({
        email: '',
        password: '',
    }); */
    const showError = ref(false);
    const { locale } = useI18n();
    const modalTitle = ref('');
    const modalMessage = ref('');
    const modalRef = ref(null);

    const showModal = (title, message) => {
        modalTitle.value = title;
        modalMessage.value = message;
        modalRef.value?.show();
    };

    const schema = yup.object({
      email: yup.string().email('Enter a valid email').required('Email field is required'),
      password: yup.string().required('Password field is required').min(8, 'Password must be at least 8 characters'),
    })

    const { handleSubmit, setErrors } = useForm({
      validationSchema: schema
    })

    const { value: email, errorMessage: emailError } = useField('email')
    const { value: password, errorMessage: passwordError } = useField('password')

    const loginUser = async () => {
        /* errors.value = {}; */
        errorl.value = '';
        try {
            const response = await axios.post('/api/login', {
                email: email.value,
                password: password.value
            });
            if(response.data.status == 'success') {
                /* localStorage.setItem('auth_token', response.data.token); */
                localStorage.setItem('user_name', response.data.name);
                localStorage.setItem('user_id', response.data.id);
                localStorage.setItem("user_lang", response.data.usr.language);
                store.commit('auth/SET_TOKEN', response.data.token);
                store.commit('auth/SET_ROLES', response.data.roles);
                store.commit('auth/SET_PERMISSIONS', response.data.permissions);
                store.commit('auth/SET_USER', response.data.usr);
                const userLang = response.data.usr.language || 'en';
                locale.value = userLang;
                store.commit('auth/SET_LANGUAGE', userLang);
                router.push('/dashboard');
            }
            console.log(response.data);
        } catch (error) {
            if(error.response) {
                if(error.response.status === 422) {
                    const backendErrors = error.response.data.errors;
                    setErrors(backendErrors);
                } else if (error.response.status === 401) {
                    errorl.value = error.response.data.msg;
                    showError.value = true;
                    
                }  else {
                    errorl.value = error.response.data.msg;
                    console.error('test', errorl);
                    console.error('Unexpected error: ', error.response.data.msg);
                }
            }
        }
    }
    const onSubmit = handleSubmit(loginUser)
</script>

<style>
.error {
    font-size: medium;
}
</style>