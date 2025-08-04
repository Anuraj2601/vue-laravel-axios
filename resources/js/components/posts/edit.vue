<template>
  <div class="w-full">
    <SuccessModal
            :show="showSuccess"
            :title="$t('post_edit.modal.create_title')"
            :message="$t('post_edit.modal.create_message')"
            @close="showSuccess= false"
        >

        </SuccessModal>

        <SuccessModal
            :show="showUpdate"
            title="$t('post_edit.modal.update_title')"
            message="$t('post_edit.modal.update_message')"
            @close="showUpdate= false"
        >

        </SuccessModal>
    <div class="bg-white p-6 rounded-lg">
      <div class="flex justify-between">
        <h2 class="text-2xl font-semibold"> {{ props.showEdit ?  $t('manage_social_posts', { platform: props.platformName }) : $t('create_social_media') }}</h2>
        <button
          type="button"
          class=" inline-flex w-full justify-center rounded-md bg-red-600 px-2 py-2 text-sm font-semibold text-gray-100 hover:bg-red-800 sm:mt-0 sm:w-auto"
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
            <AddS :formsSoc="formsSoc" @name="handleSocialMediaName" :errorsSoc="formErrors2" v-if="!props.showEdit && !props.showUserEdit" />

            <!-- Edit Social Media Form -->
            <Edit :formsSoc1="formsSoc1" :errorsSoc="formErrors1" :socialMediaId="props.socialMediaId" v-if="props.showEdit && !props.showUserEdit" />
              
          </div>
      
        <!-- Edit Posts Form -->
         <div :class="[props.showUserEdit ? border_u : border_t, border_u]" v-if="props.showEdit">
                <h2 class="text-2xl font-semibold">{{ $t('post_edit.title_edit_posts') }}</h2>
          </div>
      <form @submit.prevent="updatePosts" v-if="props.showEdit">
        <div v-for="(post, index) in posts" :key="post.id" class="mb-6" v-if="!loading">
          <div class="flex pt-1 justify-between items-center">
            <span class="text-gray-600 text-base">#{{ post.id }}</span><!-- <span class="text-gray-800 text-base">{{ props.showUserEdit? '' : post.username }}</span> -->
             <div class="flex space-x-2">
                <button
              type="button"
              @click="removePost(post.id)"
              class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
            <button 
                class="px-3 py-2 bg-red-600 text-white p-2 rounded-md hover:bg-red-700 text-sm"
                @click="confirmDelete(post.id)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
            </button>
             </div>
          </div>
          <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
            <div class="w-full md:w-1/2">
              <label for="name" class="block text-lg font-medium text-gray-700 text-left">{{ $t('post_edit.form.name') }}<span class="text-red-400 text-base font-medium">*</span></label>
              <input 
                v-model="post.name" 
                type="text" 
                :placeholder="$t('post_edit.form.name_placeholder')" 
                class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
              />
              <div v-if="erroru?.[index]?.name" class="text-red-500 text-sm mt-2">
                <!-- {{ erroru[index].name }} -->
                  {{ $t('post_edit.errors.name') }}
              </div>
            </div>
          
            <div class="w-full md:w-1/2">
              <label for="description" class="block text-lg font-medium text-gray-700 text-left">{{ $t('post_edit.form.description') }}<span class="text-red-400 text-base font-medium">*</span></label>
              <input 
                v-model="post.description" 
                type="text" 
                :placeholder="$t('post_edit.form.description_placeholder')" 
                class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
              />
              <div v-if="erroru?.[index]?.description" class="text-red-500 text-sm mt-2">
                <!-- {{ erroru[index].description }} -->
                  {{ $t('post_edit.errors.description') }}
              </div>
            </div>
          </div>

          
          <div class="mb-4">
            <label for="tags" class="block text-lg font-medium text-gray-700 text-left">{{ $t('post_edit.form.tags') }}<span class="text-red-400 text-base font-medium">*</span></label>
            <Multiselect
              v-model="post.tags"
              :options="tags"
              :multiple="true"
              :close-on-select="true"
              :clear-on-select="false"
              :preserve-search="true"
              :placeholder="$t('post_edit.form.tags_placeholder')"
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
              <!-- {{ erroru[index].tags }} -->
                {{ $t('post_edit.errors.tags') }}
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
              <div class="text-base">{{$t('post_edit.messages.processing')}}</div>
            </button>
        </div>
        <div class="flex space-x-6 text-md mb-4" v-if="posts.length <= 0 && loadingNo">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
          </svg>
              {{ $t('post_edit.messages.no_posts') }}
        </div>
        <div class="mb-2">
            <div :class="[props.showUserEdit ? border_n : border_t, border_u]">
                <h2 class="text-2xl mt-2 font-semibold">{{ $t('post_add.title') }}</h2>
                    <button type="button" @click="addForm" class="btn btn-primary mb-4 px-2 py-2 mt-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none text-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                      </svg>
                    </button>
            </div>

                <!-- Add Posts Form -->
          <Add :forms="forms" :errors="formErrors" @remove="removeForm"  />
        </div>
         <div v-if="props.showEdit && !props.showUserEdit" class="mb-4">
            <button 
            type="button" 
            @click="submitForm"
            class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
            >
            {{$t('post_edit.buttons.submit')}}
          </button>
         </div>
         <div v-if="props.showUserEdit">
            <button 
            type="button" 
            @click="submitUserEditForm"
            class="w-full py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 text-sm"
            >
            {{$t('post_edit.buttons.submit')}}
          </button>
         </div>
      </form>
      <div v-if="!props.showEdit" class="mt-4">
            <button 
            type="button" 
            @click="submitFormAdd"
            class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
            >
            {{$t('post_edit.buttons.submit')}}
          </button>
         </div>
    </div>
  </div>

              <WarningModal
                  :show="showWarning"
                  :title="$t('post_edit.modal.delete_title')"
                  :message="$t('post_edit.modal.delete_message')"
                  @close="showWarning=false"
                  @confirm="deletePost"
                >

                </WarningModal>
</template>

<script setup>
import { ref, onMounted, markRaw, computed } from 'vue';
import axios from 'axios';
import Multiselect from 'vue-multiselect';
import Add from './add.vue';
import AddS from '../socialmedia/add.vue';
import Edit from '../socialmedia/edit.vue';
import SuccessModal from '../modals/SuccessModal.vue';
import WarningModal from '../modals/WarningModal.vue';

const emit = defineEmits(['updated']);
const posts = ref([]);
const open = ref(false);
const postToDelete = ref(null);
const loading = ref(false);
const loadingNo = ref(false);
const erroru = ref([]);
const showSuccess = ref(false);
const showWarning = ref(false);
const showUpdate = ref(false);
const SName = ref('');

function handleSocialMediaName(name) {
    SName.value = name;
}

const today = computed(() => {
  const date = new Date();
  const dd = String(date.getDate()).padStart(2, '0');
  const mm = String(date.getMonth() + 1).padStart(2, '0');
  const yyyy = date.getFullYear();
  return `${yyyy}-${mm}-${dd}`;
});

const forms = ref([
  /* { name: '', description: '', tags: [], socialMedia: [] } */
]);

const formsSoc = ref([
  {platform: '', location: '', date: ''}
]);



const formsSoc1 = ref({
    id: SName.value,
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

function confirmDelete(id) {
  postToDelete.value = id;
  showWarning.value = true;
}

function removeForm(index) {
    forms.value.splice(index, 1);
}

const modalTitle = ref('');
const modalMessage = ref('');
const modalRef = ref(null);



const showModal = (title, message) => {
  modalTitle.value = title;
  modalMessage.value = message;
  modalRef.value?.show();
};

function showSuccessModal() {
    showSuccess.value = true;
    setTimeout(() => {
        emit('created')
        /* showSuccess.value = false */
        /* emit('close') */
    }, 3500)
}

function showUpdatedModal() {
  showUpdate.value = true;
  setTimeout(() => {
      emit('created')
      /* showUpdate.value = false */
      /* emit('close') */
  },3500);
}

const formErrors = ref([]);
const formErrors1 = ref({});
const formErrors2 = ref({});

const tags = ref([]);
const socialMedia = ref([]);
const token = localStorage.getItem('auth_token');
const border_u = ref('mt-4 mb-2 flex justify-between');
const border_n = ref('mt-4 mb-2 flex space-x-2');
const border_t = ref('border-t border-gray-300');

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
    },
    showUserEdit: {
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
        loadingNo.value = true;
        posts.value = response.data.posts;
        tags.value = response.data.tags;
        socialMedia.value = response.data.socialMedia;
        console.log(response.data.posts);
    } catch (error) {
        console.error("Error fetching posts for social media", error);
    }
};

const fetchPostsforUser = async() => {
      loading.value = true;
      try {
          const response = await axios.get(`api/posts/socmeduser/${props.socialMediaId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
          });
          loading.value = false;
          posts.value = response.data.posts;
          tags.value = response.data.tags;
          loadingNo.value = true;
      } catch (error) {
          console.error('Error fetching User posts related social media', response.data.error);
      }
}

const deletePost = async () => {
  try {
    showWarning.value = false;
    const response = await axios.delete(`/api/posts/delete/${postToDelete.value}`, {
        headers: {
            'Authorization' : `Bearer ${token}`
        }
    });
    if (response.data.status == 'success') {
        if(props.showUserEdit) {
            fetchPostsforUser();
        }else {
            fetchPostsForSocialMedia();
        }
    }
  } catch (error) {
    console.error("Delete post Error", error);
  }
}

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

/* Handle all posts edit form errors */
const handleEditFormErrors = (errors) => {
    const socialErrors = {};
    const postEditErrors = [];
    const postsErrors = [];

    if (errors.platform) socialErrors.platform = errors.platform[0];
    if (errors.location) socialErrors.location = errors.location[0];
    if (errors.date) socialErrors.date = errors.date[0];

    for (let i = 0; i < forms.value.length; i++) {
        const postErrors = {};

        if (errors[`forms.${i}.name`]) postErrors.name = errors[`forms.${i}.name`][0];
        if (errors[`forms.${i}.description`]) postErrors.description = errors[`forms.${i}.description`][0];
        if (errors[`forms.${i}.tags`]) postErrors.tags = errors[`forms.${i}.tags`][0];

        if (Object.keys(postErrors).length > 0) {
            postsErrors[i] = postErrors;
        }
    }

    for (let i = 0; i < posts.value.length; i++) {
        const posteErrors = {};

        if (errors[`posts.${i}.name`]) posteErrors.name = errors[`posts.${i}.name`][0];
        if (errors[`posts.${i}.description`]) posteErrors.description = errors[`posts.${i}.description`][0];
        if (errors[`posts.${i}.tags`]) posteErrors.tags = errors[`posts.${i}.tags`][0];

        if (Object.keys(posteErrors).length > 0) {
            postEditErrors[i] = posteErrors;
        }
    }

    return {socialErrors, postsErrors , postEditErrors};
};

const handleUserEditFormErrors = (errors) => {
    const postEditErrors1 = [];
    const postsErrors1 = [];

    for (let i = 0; i < forms.value.length; i++) {
        const postErrors = {};

        if (errors[`forms.${i}.name`]) postErrors.name = errors[`forms.${i}.name`][0];
        if (errors[`forms.${i}.description`]) postErrors.description = errors[`forms.${i}.description`][0];
        if (errors[`forms.${i}.tags`]) postErrors.tags = errors[`forms.${i}.tags`][0];

        if (Object.keys(postErrors).length > 0) {
            postsErrors1[i] = postErrors;
        }
    }

    for (let i = 0; i < posts.value.length; i++) {
        const posteErrors = {};

        if (errors[`posts.${i}.name`]) posteErrors.name = errors[`posts.${i}.name`][0];
        if (errors[`posts.${i}.description`]) posteErrors.description = errors[`posts.${i}.description`][0];
        if (errors[`posts.${i}.tags`]) posteErrors.tags = errors[`posts.${i}.tags`][0];

        if (Object.keys(posteErrors).length > 0) {
            postEditErrors1[i] = posteErrors;
        }
    }

    return { postsErrors1 , postEditErrors1};
}


//correct validation 
const handleAddSocialMediaPostsErrors = (errors) => {
    const socialErrors = {};
    const postsErrors = {};
    if (errors.platform) socialErrors.platform = errors.platform[0];
    if (errors.location) socialErrors.location = errors.location[0];
    if (errors.date) socialErrors.date = errors.date[0];

    for (let i = 0; i < forms.value.length; i++) {
        const postErrors = {};

        if (errors[`forms.${i}.name`]) postErrors.name = errors[`forms.${i}.name`][0];
        if (errors[`forms.${i}.description`]) postErrors.description = errors[`forms.${i}.description`][0];
        if (errors[`forms.${i}.tags`]) postErrors.tags = errors[`forms.${i}.tags`][0];

        if (Object.keys(postErrors).length > 0) {
            postsErrors[i] = postErrors;
        }
    }
    return { socialErrors, postsErrors };
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
        if(response.data.status == 'success') {
            cancel();
            emit('updated');
            showSuccessModal();
        }
    } catch (error) {
        if(error.response) {
            if(error.response && error.response.status == 422) {
               const { socialErrors, postsErrors } =  handleAddSocialMediaPostsErrors(error.response.data.errors);
               formErrors2.value = socialErrors;
               formErrors.value = postsErrors;
            }
        }
    }
}

/* User Posts Edit Section */
const submitUserEditForm = async() => {
    formErrors.value = [];
    erroru.value = [];

    try {
        const response = await axios.post('api/social-media-user/update', {
            id: props.socialMediaId,
            forms: forms.value,
            posts: posts.value
        }, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        if(response.data.status == 'success') {
            cancel();
            emit('updated');
            showSuccessModal();
        }
    } catch (error) {
        if(error.response) {
            if (error.response && error.response.status === 422) {
                const {postsErrors1, postEditErrors1} = handleUserEditFormErrors(error.response.data.errors);
                erroru.value = postEditErrors1;
                formErrors.value = postsErrors1;
            }
        }
    }
}

const submitForm = async() => {
    formErrors.value = [];  // post create errors
    erroru.value = [];    // edit post errors
    formErrors1.value = {}; // eit social media errors

    try {
        const response = await axios.post('api/social-media/update', {
        id: formsSoc1.value.id,
        platform: formsSoc1.value.platform,
        location: formsSoc1.value.location,
        date: formsSoc1.value.date,
        forms: forms.value,
        posts: posts.value
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
        if(response.data.status == 'success') {
            cancel();
            emit('updated');
            showUpdatedModal();
        }
    } catch (error) {
        if(error.response) {
            if (error.response && error.response.status === 422) {
                const {socialErrors, postsErrors, postEditErrors} = handleEditFormErrors(error.response.data.errors);
                formErrors1.value = socialErrors;
                erroru.value = postEditErrors;
                formErrors.value = postsErrors;
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
  await fecthSocialMediaById();
  if(props.showUserEdit) {
      fetchPostsforUser();
  } else {
      fetchPostsForSocialMedia();
  }
});

</script>
