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
        <h2 class="text-2xl font-semibold"> {{ (props.showEdit || props.showUserEdit) ?  t('manage_social_posts') : $t('create_social_media') }}</h2>
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
            <AddS :formsSoc="formsSoc" @name="handleSocialMediaName" :errorsSoc="formErrors2" v-if="!props.showMyPostAdd && !props.showUserEdit && !props.showEdit && hasPermission('create_social_media')" />

            <!-- Add My posts form -->
            <Addmypost :formMyPost="formMyPost" :errorMyPost="errorMyPost" v-if="props.showMyPostAdd" />

            <!-- Edit My posts form -->
            <Editmypost :formMyPost1="formMyPost1" :errorMyPost1="errorMyPost1" :socialMediaId="props.socialMediaId" v-if="showEditReady && props.showUserEdit && !props.showMyPostAdd" />

            <!-- Edit Social Media Form -->
            <Edit :formsSoc1="formsSoc1" :errorsSoc="formErrors1" :socialMediaId="props.socialMediaId" v-if="showSocialEditReady && !props.showMyPostAdd && props.showEdit && hasPermission('edit_social_media')" />
              
          </div>
      
        <!-- Edit Posts Form -->
         <div :class="[props.showUserEdit ? border_u : border_t, border_u]" v-if="props.showEdit || props.showUserEdit">
                <h2 class="text-2xl font-semibold">{{ $t('post_edit.title_edit_posts') }}</h2>
          </div>

          <div v-if="permissionError" class="text-red-600 text-sm ml-6 my-2">
                    {{ permissionError }}
          </div>
      <form @submit.prevent="updatePosts" v-if="(props.showEdit || props.showUserEdit) && hasPermission('edit_post')">
        <div v-for="(post, index) in posts" :key="post.id" class="mb-6" v-if="!loading">
          <div class="flex pt-1 justify-between items-center">
            <span class="text-gray-600 text-base">#{{ post.id }}</span><!-- <span class="text-gray-800 text-base">{{ props.showUserEdit? '' : post.username }}</span> -->
             <div class="flex space-x-2" v-if="hasPermission('delete_post')">
                <button
              type="button"
              @click="confirmDelete(post)"
              class="px-3 py-2 bg-none text-red rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                :placeholder="t('post_edit.form.name_placeholder')" 
                class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
              />
              <div v-if="erroru?.[index]?.name" class="text-red-500 text-sm mt-2">
                {{ erroru[index].name }}
              </div>
            </div>
          
            <div class="w-full md:w-1/2">
              <label for="description" class="block text-lg font-medium text-gray-700 text-left">{{ $t('post_edit.form.description') }}<span class="text-red-400 text-base font-medium">*</span></label>
              <input 
                v-model="post.description" 
                type="text" 
                :placeholder="t('post_edit.form.description_placeholder')" 
                class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
              />
              <div v-if="erroru?.[index]?.description" class="text-red-500 text-sm mt-2">
                {{ erroru[index].description }}
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
              :clear-on-select="true"
              :preserve-search="false"
              :placeholder="$t('post_edit.form.tags_placeholder')"
              label="name"
              track-by="id"
              :preselect-first="false"
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
              <div class="text-base">{{$t('post_edit.messages.processing')}}</div>
            </button>
        </div>
        <div class="flex space-x-6 text-md mb-4" v-if="posts.length <= 0 && loadingNo">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
          </svg>
              {{ $t('post_edit.messages.no_posts') }}
        </div>
        
         
         
      </form>
      <div class="mb-2" v-if="hasPermission('create_post')">
            <div :class="[props.showEdit ? border_n : border_t, border_u]">
                <h2 class="text-2xl mt-2 font-semibold">{{ $t('post_add.title') }}</h2>
                    <!-- <button type="button" @click="addForm" class="btn btn-primary mb-4 px-2 py-2 mt-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none text-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                      </svg>
                    </button> -->
            </div>

                <!-- Add Posts Form -->
          <Add :forms="forms" :errorsF="formErrors" :errors="errorsAdd"  @remove="removeForm" :disableRemove="showMyPostAdd"   />
            <div class="flex justify-end">
              <button type="button" @click="addForm" class="btn btn-primary mb-4 px-2 py-2 mt-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
              </button>
            </div>
        </div>
        <!-- <div v-if="props.showUserEdit">
            <button 
            type="button" 
            @click="submitUserEditForm"
            class="w-full py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 text-sm"
            >
            {{$t('post_edit.buttons.submit')}}
          </button>
         </div> -->
        <div v-if="!props.showEdit && !props.showMyPostAdd && !props.showUserEdit" class="mt-4">
            <button 
            type="button" 
            @click="submitFormAdd"
            class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
            >
            {{$t('post_edit.buttons.submit')}}
          </button>
        </div>
        <div v-if="props.showEdit" class="mb-4">
            <button 
            type="button" 
            @click="submitForm"
            class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
            >
            {{$t('post_edit.buttons.submit')}}
          </button>
         </div>

         <div v-if="props.showUserEdit && !props.showMyPostAdd">
            <button 
            type="button" 
            @click="submitUpdateMyPost"
            class="w-full py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 text-sm"
            >
            {{$t('post_edit.buttons.submit')}}
          </button>
         </div>

         <div v-if="props.showMyPostAdd" class="mb-4">
            <button 
            type="button" 
            @click="submitFormSocialMediaMyPosts"
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
                  @confirm="removePost"
                >

                </WarningModal>
</template>

<script setup>
import axios from 'axios';
import { computed, onBeforeMount, onMounted, ref, watch } from 'vue';
import Multiselect from 'vue-multiselect';
import i18n from '../../i18n';
import SuccessModal from '../modals/SuccessModal.vue';
import WarningModal from '../modals/WarningModal.vue';
import AddS from '../socialmedia/add.vue';
import Edit from '../socialmedia/edit.vue';
import Add from './add.vue';
import { useForm, useField, ErrorMessage, useFieldArray } from 'vee-validate';
import * as yup from 'yup';
import { useStore } from 'vuex';
import { useI18n } from 'vue-i18n';
import Addmypost from '../myposts/addmypost.vue';
import Editmypost from '../myposts/editmypost.vue';

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
const deletedPosts = ref([]);
const store = useStore();
const { t } = useI18n();
const permissionError = ref('');
const showEditReady = ref(false);
const showSocialEditReady = ref(false);
const childRef = ref(null);
function handleSocialMediaName(name) {
    SName.value = name;
}

const errorsAdd = ref([]);

const hasRole = (role) => store.getters['auth/hasRole'](role);
const hasPermission = (permission) => store.getters['auth/hasPermission'](permission);


const today = computed(() => {
  const date = new Date();
  const dd = String(date.getDate()).padStart(2, '0');
  const mm = String(date.getMonth() + 1).padStart(2, '0');
  const yyyy = date.getFullYear();
  return `${yyyy}-${mm}-${dd}`;
});

const forms = ref([
  /* { name: '', description: '', tags: [] } */
]);

const formsSoc = ref([
  {platform: '', location: '', date: ''}
]);

const formMyPost = ref([
  {platform: '', location: '', date: ''}
]);

const formMyPost1 = ref({
  id: SName.value,
    platform: [],
    location: '',
    date: ''
})

const formsSoc1 = ref({
    id: SName.value,
    platform: '',
    location: '',
    date: ''
});

/* function addForm() {
  open.value= true;
    forms.value.push({
        name: '',
        description: '',
        tags: [],
    })
} */

const addForm = () => {
  const lastIndex = forms.value.length - 1;
  const lastForm = forms.value[lastIndex]?.value;

  // Initialize errors for this form if not exists
  if (!errorsAdd.value[lastIndex]) errorsAdd.value[lastIndex] = {};

  // If no forms exist, just add the first one
  if (!lastForm) {
    forms.value.push({ name: '', description: '', tags: [] });
    errorsAdd.value.push({});
    return;
  }

  // Validate only the last form
  let hasError = false;

  if (!lastForm.name || lastForm.name.trim() === '') {
    errorsAdd.value[lastIndex].name = t('post_add.errors.name');
    hasError = true;
  } else {
    errorsAdd.value[lastIndex].name = '';
  }

  if (!lastForm.description || lastForm.description.trim() === '') {
    errorsAdd.value[lastIndex].description = t('post_add.errors.description');
    hasError = true;
  } else {
    errorsAdd.value[lastIndex].description = '';
  }

  if (!lastForm.tags || lastForm.tags.length === 0) {
    errorsAdd.value[lastIndex].tags = t('post_add.errors.tags');
    hasError = true;
  } else {
    errorsAdd.value[lastIndex].tags = '';
  }

  // Stop if there are errors
  if (hasError) return;

  // Push new empty form and empty error object
  forms.value.push({ name: '', description: '', tags: [] });
  errorsAdd.value.push({});
};



watch(
  forms,
  (newForms) => {
    newForms.forEach((form, index) => {
      const errors = errorsAdd.value[index] || {};
      let hasChange = false;

      // Check each field and clear error if user typed something valid
      if (errors.name && form.name && form.name.trim() !== '') {
        delete errors.name;
        hasChange = true;
      }
      if (errors.description && form.description && form.description.trim() !== '') {
        delete errors.description;
        hasChange = true;
      }
      if (errors.tags && form.tags && form.tags.length > 0) {
        delete errors.tags;
        hasChange = true;
      }

      if (hasChange) errorsAdd.value[index] = { ...errors };
    });
  },
  { deep: true }
);

function confirmDelete(post) {
  postToDelete.value = post;
  showWarning.value = true;
}

function removeForm(index) {
    forms.value.splice(index,1);
}

function removePost() {
  if (postToDelete.value) {
    
    deletedPosts.value.push(postToDelete.value.id);
    posts.value = posts.value.filter(p => p.id !== postToDelete.value.id);
    postToDelete.value = null;
    showWarning.value = false;

  }
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
/* const formErrors = ref([{}]); */
const validationErrors = ref([]);
const formErrors1 = ref({});


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
    },
    showMyPostAdd: {
      type: [Boolean],
      required: true,
    }
});

/* const socialSchema = yup.object({
    platform: yup.string().required(t('social_add.name_required')),
    location: yup.string().required(t('social_add.location_required')),
    date: yup.date().required(t('social_add.date_required')),
}); */

/* const formsSoc = useForm({
  validationSchema: socialSchema,
}); */


const formErrors2 = ref({
});

const errorMyPost = ref({});

const errorMyPost1 = ref({});

/* Fetch Social Media Details by ID for Edit social Media */
const fecthSocialMediaById = async () => {
    showSocialEditReady.value = false;
    try {
      const res = await axios.get(`api/social-media/${props.socialMediaId}`, {
          headers: {
              'Authorization': `Bearer ${token}`,
              "Accept-Language": i18n.global.locale.value
          }
      });

      formsSoc1.value = res.data.social_media;
      formsSoc1.value.date = today.value;
      console.log("testing");
      showSocialEditReady.value = true;
    } catch (error) {
      if(error.response) {
        if (error.response.status === 403) {
              permissionError.value = error.response.data.message;
        }
      }
    }
}

/* Fetch Social Media by id in mypost */
const fetchSocialMediaByIdMyPost = async() => {
    showEditReady.value = false;
    try {
      const res = await axios.get(`api/social-media/${props.socialMediaId}`, {
          headers: {
              'Authorization': `Bearer ${token}`,
              "Accept-Language": i18n.global.locale.value
          }
      });

      formMyPost1.value = res.data.social_media;
      formMyPost1.value.date = today.value;
      showEditReady.value = true;
      console.log(formMyPost1.value);
    } catch (error) {
      if(error.response) {
        if (error.response.status === 403) {
              permissionError.value = error.response.data.message;
        }
      }
    }
}

/* Fetch Posts that relevant to the Social Media ID */
const fetchPostsForSocialMedia = async () => {
      loading.value = true;
    try {
        const response = await axios.get(`/api/posts/socmed/${props.socialMediaId}`, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
            }
        });
        loading.value = false;
        loadingNo.value = true;
        posts.value = response.data.posts;
        tags.value = response.data.tags;
        socialMedia.value = response.data.socialMedia;
        console.log(response.data.posts);
    } catch (error) {
      if(error.response) {
        if (error.response.status === 403) {
              permissionError.value = error.response.data.message;
        }
      }
        console.error("Error fetching posts for social media", error);
    }
};

const fetchPostsforUser = async() => {
      loading.value = true;
      try {
          const response = await axios.get(`api/posts/socmeduser/${props.socialMediaId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    "Accept-Language": i18n.global.locale.value
                }
          });
          loading.value = false;
          posts.value = response.data.posts;
          tags.value = response.data.tags;
          loadingNo.value = true;
      } catch (error) {
        if (error.response.status === 403) {
              permissionError.value = error.response.data.message;
        } 
          console.error('Error fetching User posts related social media', response.data.error);
      }
}

const deletePost = async () => {
  try {
    showWarning.value = false;
    const response = await axios.delete(`/api/posts/delete/${postToDelete.value}`, {
        headers: {
            'Authorization' : `Bearer ${token}`,
            "Accept-Language": i18n.global.locale.value
        }
    });
    if (response.data.status == 'success') {
            fetchPostsForSocialMedia();
    }
  } catch (error) {
    console.error("Delete post Error", error);
  }
}

/* Remove post from form */
/* const removePost = (postId) => {
  const index = posts.value.findIndex(post => post.id === postId);
  if(index !== -1) {
    posts.value.splice(index, 1);
  }
} */

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
                "Authorization": `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
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

/**
 * User Created Social Media for myposts
 */
const submitFormSocialMediaMyPosts = async() => {
    formErrors.value = []; // Posts form errors
    errorMyPost.value = {}; // Social Media form errors
  console.log(formMyPost.value.platform);
    try {
        const response = await axios.post('api/social-media-create/mypost', {
            platform: formMyPost.value.platform,
            location: formMyPost.value.location,
            date: formMyPost.value.date,
            forms: forms.value
        }, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
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
               errorMyPost.value = socialErrors;
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
            posts: posts.value,
            deletedPostIds: deletedPosts.value
        }, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
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
      console.log(formsSoc1.value);
        const response = await axios.post('api/social-media/update', {
        id: formsSoc1.value.id,
        platform: formsSoc1.value.platform,
        location: formsSoc1.value.location,
        date: formsSoc1.value.date,
        forms: forms.value,
        posts: posts.value,
        deletedPostIds: deletedPosts.value
        }, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
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

const submitUpdateMyPost = async() => {
    formErrors.value = [];  // post create errors
    erroru.value = [];    // edit post errors
    errorMyPost1.value = {}; // eit social media errors

    try {
      console.log(formMyPost1.value);
        const response = await axios.post('api/social-media/update', {
        id: formMyPost1.value.id,
        platform: formMyPost1.value.platform,
        location: formMyPost1.value.location,
        date: formMyPost1.value.date,
        forms: forms.value,
        posts: posts.value,
        deletedPostIds: deletedPosts.value
        }, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
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
                errorMyPost1.value = socialErrors;
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


onBeforeMount(async () => {
  if (!props.showMyPostAdd && props.showEdit && !props.showUserEdit) {
    await fecthSocialMediaById();
    console.log("testing");
  }
});

onBeforeMount(async () => {
  if (props.showUserEdit && !props.showMyPostAdd) {
    try {
      await fetchSocialMediaByIdMyPost();
      await fetchPostsforUser();
    } catch (err) {
      console.error('Failed to fetch data for edit:', err);
    }
  }
});

onMounted(() => {
  if(props.showEdit && !props.showMyPostAdd) {
      fetchPostsForSocialMedia();
  }
  /* if(props.showUserEdit && !props.showMyPostAdd) {
      fetchSocialMediaByIdMyPost();
      fetchPostsforUser();
  } */

  if(props.showMyPostAdd) {
    open.value = true;
    forms.value.push({
        name: '',
        description: '',
        tags: [],
        socialMedia: []
    });
  }
});

</script>
