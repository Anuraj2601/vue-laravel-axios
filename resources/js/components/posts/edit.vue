<template>
  <div class="w-full">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <div class="flex justify-between">
        <h2 class="text-2xl font-semibold"> {{ props.showEdit ? `Manage ${props.platformName} Posts` :  'Create Social Media'}}</h2>
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

      

          <div class="mb-2">
            <!-- Social Media Create Form -->
            <AddS :formsSoc="formsSoc" @name="handleSocialMediaName" :errorsSoc="formErrors2" v-if="!props.showEdit" />

            <!-- Edit Social Media Form -->
            <Edit :formsSoc1="formsSoc1" :errorsSoc="formErrors1" :socialMediaId="props.socialMediaId" v-if="props.showEdit" />
              <div :class="[border_t, border_u]">
                <h2 class="text-2xl font-semibold">Create {{ props.showEdit ? ' & Edit' : '' }} Posts</h2>
                    <button type="button" @click="addForm" class="btn btn-primary mb-4 px-4 py-2 mt-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none text-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                      </svg>
                    </button>
                </div>

                <!-- Add Posts Form -->
              <Add :forms="forms" :errors="formErrors" @remove="removeForm"  />
          </div>
      
        <!-- Edit Posts Form -->
      <form @submit.prevent="updatePosts" v-if="props.showEdit">
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
      <div v-if="!props.showEdit">
            <button 
            type="button" 
            @click="submitFormAdd"
            class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
            >
            Submit
          </button>
         </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, markRaw } from 'vue';
import axios from 'axios';
import Multiselect from 'vue-multiselect';
import Add from './add.vue';
import AddS from '../socialmedia/add.vue';
import Edit from '../socialmedia/edit.vue';

const emit = defineEmits(['updated']);
const posts = ref([]);
const open = ref(false);
const loading = ref(false);
const erroru = ref([]);
const SName = ref('');

function handleSocialMediaName(name) {
    SName.value = name;
}

const forms = ref([
  { name: '', description: '', tags: [], socialMedia: [] }
]);

const formsSoc = ref([
  {platform: '', location: '', date: ''}
]);

const formsSoc1 = ref({
    platform: '',
    location: '',
    date: ''
});

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
const formErrors1 = ref([]);
const formErrors2 = ref({});

const tags = ref([]);
const socialMedia = ref([]);
const token = localStorage.getItem('auth_token');
const border_u = ref('mt-4 mb-2 flex justify-between');
const border_t = ref('border-t');

/* Props that pass to child component */
const props = defineProps({
    socialMediaId: {
        type: [String, Number],
        required: true,
    },
    platformName: {
      type: [String],
      required: true,
    },
    showEdit: {
      type: [Boolean],
      required: true,
    }
});

/* Fetch Social Media Details by ID */
const fecthSocialMediaById = async () => {
    const res = await axios.get(`api/social-media/${props.socialMediaId}`, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
    console.log(props.socialMediaId,res.data);

    formsSoc1.value = res.data.social_media;
}

/* Fetch Posts that relevant to the Social Media ID */
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

/* Remove post from form */
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


//correct validation 
const handleAddSocialMediaPostsErrors = (errorObj) => {
    const socialErrors = {};
    const postsErrors = [];

    Object.entries(errorObj).forEach(([key, message]) => {
        if(key.startsWith('forms.')) {
            const match = key.match(/^forms\.(\d+)\.(\w+)$/);
            if(match) {
                const index = Number(match[1]);
                const field = match[2];

                if (!postsErrors[index]) {
                    postsErrors[index] = {};
                }

                postsErrors[index][field] = message;
            }
        } else {
            socialErrors[key] = message;
        }
    });

    return {socialErrors, postsErrors};
}


const submitFormAdd = async () => {
    formErrors.value = []; // Posts form errors
    formErrors2.value = {}; // Social Media form errors

    try {
        const response = await axios.post('api/social-media/create', {
            platform: formsSoc.value.platform,
            location: formsSoc.value.location,
            date: formsSoc.value.date,
            forms: forms.value
        }, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        if(response.data.msg == 'success') {
            cancel();
            emit('updated');
        }
    } catch (error) {
        if(error.response) {
            if(error.response && error.response.status == 422) {
               const { socialErrors, postsErrors } =  handleAddSocialMediaPostsErrors(error.response.data.error);
               formErrors2.value = socialErrors;
               formErrors.value = postsErrors;
            }
        }
    }
}

const submitForm = async() => {
    formErrors.value = {};
    erroru.value = {};
    try {
        const response = await axios.post('api/social-media/store', {
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
}

    /* try {
        const postsData = posts.value.map(post=> ({
            id: post.id,
            name: post.name,
            description: post.description,
            tags: post.tags.map(tag=> tag.id),
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
}; */



const cancel = () => {
    emit('close');
};

onMounted( async () => {
  await fecthSocialMediaById(),
  fetchPostsForSocialMedia()
});

</script>
