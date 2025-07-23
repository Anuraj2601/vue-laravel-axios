<template>
  <div class="w-full">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <div class="flex justify-between">
        <h2 class="text-2xl font-semibold">Manage Posts for {{ props.platformName }}</h2>
        <button
          type="button"
          class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50 sm:mt-0 sm:w-auto"
          @click="cancel"
          ref="cancelButtonRef"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <button type="button" @click="addForm" class="btn btn-primary mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none text-sm">Add New Post</button>

          <div class="mb-2">
              <Add :forms="forms" :errors="formErrors" @created="fetchPosts" @remove="removeForm" />
          </div>
      
      <form @submit.prevent="updatePosts">
        <div v-for="(post, index) in posts" :key="post.id" class="mb-6" v-if="!loading">
          <div class="flex pt-2 justify-end">
             <button
              type="button"
              @click="removePost(post.id)"
              class="px-3 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
            <div class="w-full md:w-1/2">
              <label for="name" class="block text-lg font-medium text-gray-700 text-left">Post Name<span class="text-red-400 text-base font-medium">*</span></label>
              <input 
                v-model="post.name" 
                type="text" 
                :placeholder="`Post Name ${post.id}`" 
                class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
              />
              <div v-if="erroru?.[index]?.name" class="text-red-500 text-sm mt-2">
                {{ erroru[index].name }}
              </div>
            </div>

          
            <div class="w-full md:w-1/2">
              <label for="description" class="block text-lg font-medium text-gray-700 text-left">Post Description<span class="text-red-400 text-base font-medium">*</span></label>
              <input 
                v-model="post.description" 
                type="text" 
                :placeholder="`Post Description ${post.id}`" 
                class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
              />
              <div v-if="erroru?.[index]?.description" class="text-red-500 text-sm mt-2">
                {{ erroru[index].description }}
              </div>
            </div>
          </div>

          
          <div class="mb-4">
            <label for="tags" class="block text-lg font-medium text-gray-700 text-left">Tags<span class="text-red-400 text-base font-medium">*</span></label>
            <Multiselect
              v-model="post.tags"
              :options="tags"
              :multiple="true"
              :close-on-select="true"
              :clear-on-select="false"
              :preserve-search="true"
              placeholder="Pick tags"
              label="name"
              track-by="id"
              :preselect-first="true"
              class="mt-2"
            >
              <template #tag="{ option, remove }">
                <span class="bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full mr-2">
                  {{ option.name }}
                  <span class="ml-2 text-red-500 cursor-pointer" @click="remove(option)">❌</span>
                </span>
              </template>
              <template #clear="props">
                <div class="multiselect__clear" v-if="post.tags.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
              </template>
            </Multiselect>
            <div v-if="erroru?.[index]?.tags" class="text-red-500 text-sm mt-2">
              {{ erroru[index].tags }}
            </div>
          </div>

          <!-- <div class="mb-4">
            <label for="social_media" class="block text-lg font-medium text-gray-700 text-left">Social Media<span class="text-red-400 text-base font-medium">*</span></label>
            <Multiselect
              v-model="post.social_media"
              :options="socialMedia"
              :multiple="true"
              :close-on-select="true"
              :clear-on-select="false"
              :preserve-search="true"
              label="platform"
              track-by="id"
              class="mt-2"
            >
              <template #tag="{ option, remove }">
                <span class="bg-green-100 text-green-800 px-2.5 py-0.5 rounded-full mr-2">
                  {{ option.platform }}
                  <span class="ml-2 text-red-500 cursor-pointer" @click="remove(option)">❌</span>
                </span>
              </template>
              <template #clear="props">
                <div class="multiselect__clear" v-if="post.social_media.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
              </template>
            </Multiselect>
            <div v-if="errors['social_media']" class="text-red-500 text-sm mt-2">
              {{ errors['social_media'] }}
            </div>
          </div> -->

          
        </div>
        <div v-else class="items-center p-4">
            <button type="button" class="flex" disabled>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 animate-spin">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                  </svg>
              <div class="text-base">Processing....</div>
            </button>
        </div>
         <div>
            <button 
            type="button" 
            @click="submitForm"
            class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
            >
            Submit
          </button>
         </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Multiselect from 'vue-multiselect';
import Add from './add.vue';

const emit = defineEmits(['updated']);
const posts = ref([]);
const open = ref(false);
const loading = ref(false);
const erroru = ref([]);

const forms = ref([
  { name: '', description: '', tags: [], socialMedia: [] }
]);

function addForm() {
  open.value= true;
    forms.value.push({
        name: '',
        description: '',
        tags: [],
        socialMedia: []
    })
}

function removeForm(index) {
    forms.value.splice(index, 1);
}

const formErrors = ref([]);

const tags = ref([]);
const socialMedia = ref([]);
const token = localStorage.getItem('auth_token');

const props = defineProps({
    socialMediaId: {
        type: [String, Number],
        required: true,
    },
    platformName: {
      type: [String],
      required: true,
    }
});

const fetchPostsForSocialMedia = async () => {
      loading.value = true;
    try {
        const response = await axios.get(`/api/posts/socmed/${props.socialMediaId}`, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        loading.value = false;
        posts.value = response.data.posts;
        tags.value = response.data.tags;
        socialMedia.value = response.data.socialMedia;
    } catch (error) {
        console.error("Error fetching posts for social media", error);
    }
};

const removePost = (postId) => {
  const index = posts.value.findIndex(post => post.id === postId);
  if(index !== -1) {
    posts.value.splice(index, 1);
  }
}

const formatValidationErrors = (errors) => {
  const structured = [];

  Object.entries(errors).forEach(([key, message]) => {
    const match = key.match(/^posts\.(\d+)\.(\w+)/);
    if (match) {
      const index = parseInt(match[1], 10);
      const field = match[2];

      if (!structured[index]) {
        structured[index] = {};
      }

      structured[index][field] = message;
    }
  });

  return structured;
};

const handleAddFormErrors = (errorObj) => {
  const parsedErrors = [];
  for (const key in errorObj) {
    const match = key.match(/^forms\.(\d+)\.(\w+)/);
    if (match) {
      const index = Number(match[1]);
      const field = match[2];
      if (!parsedErrors[index]) parsedErrors[index] = {};
      parsedErrors[index][field] = errorObj[key];
    }
  }
  formErrors.value = parsedErrors;
};



const submitForm = async() => {
    formErrors.value = {};
    erroru.value = {};
    try {
        const response = await axios.post('api/posts/store', {
        forms: forms.value
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        if(response.data.status == 'success') {
            cancel();
            emit('updated');
        }
    } catch (error) {
        if(error.response) {
            if (error.response && error.response.status === 422) {
                handleAddFormErrors(error.response.data.error);
            }
        }
    }

    try {
        const postsData = posts.value.map(post=> ({
            id: post.id,
            name: post.name,
            description: post.description,
            tags: post.tags.map(tag=> tag.id),
            /* social_media: post.social_media.map(sm=> sm.id), */
        }));
        console.log(postsData);
        const response = await axios.post('api/posts/update-all', {posts: postsData}, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })

        if(response.data.status == 'success') {
            cancel();
            emit('updated');
        }
    } catch (error) {
       if (error.response && error.response.status === 422) {
            erroru.value = formatValidationErrors(error.response.data.error);
        }
    }
};

const cancel = () => {
    emit('close');
};

onMounted(fetchPostsForSocialMedia);

</script>
