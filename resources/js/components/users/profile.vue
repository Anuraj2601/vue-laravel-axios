<template>
  <TransitionRoot :show="show" as="template">
    <div class="fixed inset-0 z-50 overflow-y-auto">
      <!-- Background Overlay -->
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" @click="$emit('close')" />
      </TransitionChild>

      <!-- Modal Dialog -->
      <div class="flex items-center justify-center min-h-full p-4 text-center">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0 scale-95"
          enter-to="opacity-100 scale-100"
          leave="ease-in duration-200"
          leave-from="opacity-100 scale-100"
          leave-to="opacity-0 scale-95"
        >
          <div
            class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white p-6 text-left shadow-xl transition-all"
          >
            <!-- Close Button -->
            <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
              ✕
            </button>

            <h2 class="text-xl font-semibold text-gray-800 text-center mb-4">Edit Profile</h2>

            <form @submit.prevent="saveChanges" class="space-y-4">
              <!-- <div class="flex justify-center">
                <img
                  :src="defaultImage"
                  alt="Profile"
                  class="w-24 h-24 object-cover rounded-full border border-gray-300"
                />
              </div> -->
               <div class="col-span-full">
                <div class="mt-4 flex flex-col items-center text-sm text-gray-600">
                <label for="file-upload" class="cursor-pointer relative group">
                    <img
                    :src="imagePreview || (user.image ? user.image : defaultImage)"
                    alt="Profile Preview"
                    class="w-24 h-24 rounded-full object-cover border border-gray-300 hover:opacity-80 transition"
                    />
                    <input
                    id="file-upload"
                    name="file-upload"
                    type="file"
                    class="hidden"
                    accept="image/*"
                    @change="handleFileChange"
                    />
                </label>
                <span v-if="errors.image" class="text-red-500 text-sm mt-2">
                            {{ errors.image }}
                    </span>
                <span class="mt-2 text-indigo-600 hover:text-indigo-500 font-semibold cursor-pointer">
                    Upload a Photo
                </span>
                </div>
            </div>

              <div>
                <label class="block text-lg font-medium text-gray-700">Name</label>
                <input
                  v-model="user.name"
                  type="text"
                  class="mt-2 block w-full p-3 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
                <span v-if="errors.name" class="text-red-500 text-sm mt-2">
                            {{ errors.name }}
                    </span>
              </div>

              <div>
                <label class="block text-lg font-medium text-gray-700">Email</label>
                <input
                  v-model="user.email"
                  type="email"
                  class="mt-2 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
                <span v-if="errors.email" class="text-red-500 text-sm mt-2">
                            {{ errors.email }}
                </span>
              </div>

              <div>
                <label class="block text-lg font-medium text-gray-700">Role</label>
                <input
                  v-model="user.role"
                  type="text"
                  class="mt-2 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  disabled
                />
                <span v-if="errors.role" class="text-red-500 text-sm mt-2">
                            {{ errors.role }}
                </span>
              </div>

              <div class="mt-6 flex justify-end space-x-3">
                <button
                  type="button"
                  @click="$emit('close')"
                  class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 text-sm"
                >
                  Cancel
                </button>
                <button
                  @click="updateProfileInfo"
                  type="submit"
                  class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm"
                >
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </TransitionChild>
      </div>
    </div>
  </TransitionRoot>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { TransitionRoot, TransitionChild } from '@headlessui/vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { isNull } from 'lodash';

const props = defineProps({
  show: Boolean,
});

const userId = localStorage.getItem('user_id');
const token = localStorage.getItem('auth_token');
const emit = defineEmits(['close', 'save']);
const router = useRouter();

const defaultImage = 'storage/profile_images/sample.jpg';

const imagePreview = ref(null);
const selectedFile = ref(null);

function handleFileChange(event) {
  const file = event.target.files[0];
  if (file) {
    selectedFile.value = file; 
    imagePreview.value = URL.createObjectURL(file);
  }
}

const user = ref({
  name: '',
  email: '',
  role: '',
  image: ''
});

 const errors = ref({
        name: '',
        email: '',
        role: '',
        image: ''
    });

function saveChanges() {
  emit('save', { ...user });
  /* emit('close'); */
}

const getProfileInfo = async () => {
    try {
        const response = await axios.get(`api/profile/${userId}` , {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
    console.log(response.data);
    user.value = response.data.user;
    user.value.role = response.data.user.role[0];
    if (!user.value.image) {
        user.value.image = defaultImage;
    }
    console.log(response.data);
    } catch (error) {
        console.error('Unable to get profile details', error);
    }
}

const updateProfileInfo = async () => {
  try {
    const formData = new FormData();
    formData.append('name', user.value.name);
    formData.append('email', user.value.email);
    formData.append('role', user.value.role);

    if (selectedFile.value) {
        formData.append('image', selectedFile.value);
    } else {
        formData.append('image', user.value.image || '');
    }

    console.log(formData);
    const response = await axios.post('api/profile/update', formData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data',
      }
    });
    console.log(response.data);

    if (response.data.status === 'success') {
      emit('close');
      getProfileInfo();
      router.push('dashboard');
      console.log(response.data);
    }
  } catch (error) {
            if(error.response) {
                if (error.response && error.response.status === 422) {  
                errors.value = error.response.data.error; 
                console.error('Validation errors:', errors.value);
                }
            }
            console.log("User Updated failed. " , error);
        }
};



onMounted(getProfileInfo);
</script>
