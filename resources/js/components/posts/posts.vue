<template>
            <div class="title text-center mb-6">
                <h2 class="text-2xl font-semibold">{{ $t('social_s.title') }}</h2>
                </div>

                
                <Modal
                    ref="modalRef"
                    :title="modalTitle"
                    :message="modalMessage"
                    modal-id="feedbackModal"
                />

                <div class="title flex justify-between space-x-4" v-if="hasPermission('create_social_media')">
                    <button 
                        class="bg-gray-200 text-black mr-32 px-2 py-2 rounded-lg focus:outline-none hover:bg-gray-800 hover:text-gray-100 focus:ring-2 focus:ring-black-500 flex items-center space-x-2"
                        @click="addPost(selectedTab,selectedPlatform)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ $t('social_s.add') }}
                      </button>

                      <div class="space-x-1">
                          <select 
                            v-model="perPage" 
                            @change="fetchSocialMedia(1)"
                            class="mt-2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          >
                            <option v-for="n in [5, 6, 7, 8]" :key="n" :value="n">
                                {{ n }} rows
                            </option>
                          </select>
                        <input 
                        v-model="searchQuery" 
                        @input="fetchSocialMedia(1)" 
                        type="text" 
                        class="mt-2 w-65 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                        :placeholder="$t('placeholders.search_social_media')"
                        />
                    </div>
                </div>

                <div v-if="permissionError" class="text-red-600 text-sm ml-6 my-2">
                    {{ permissionError }}
                </div>
                

                <!-- <div class="title flex justify-between items-center mb-6">
                  <div class="flex justify-between">
                    <button 
                        class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 flex items-center space-x-2"
                        @click="editPost(selectedTab,selectedPlatform)"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                              Manage Posts
                      </button>
                  </div>

                    <div class="mb-4 flex-1 ml-4 text-right">
                        <input 
                        v-model="searchQuery" 
                        @input="fetchPosts(1)" 
                        type="text" 
                        class="mt-2 w-65 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                        placeholder="Search posts by name or description..."
                        />
                    </div>
                </div> -->

                <div class="overflow-x-auto mr-10 ml-6">
                    <div class="list-container">
                      <div class="flex items-center text-left font-semibold text-gray-800 mt-6 px-10">
                        <span class="flex-1 text-sm">{{ $t('social_s.platform') }}</span>
                        <!-- <span class="flex-1 text-sm">URL</span> -->
                        <span class="flex-1 text-sm">{{ $t('social_s.location') }}</span>
                        <span class="flex-1 text-sm">{{ $t('social_s.date') }}</span>
                        <span class="text-sm w-auto" v-if="hasPermission('edit_social_media') || hasPermission('delete_social_media')">{{ $t('social_s.action') }}</span>
                      </div>
                        <ul class="w-full bg-white-100 p-4 space-y-2 rounded-lg">
                          <li 
                            v-for="(social, index) in socialMediaTypes" 
                            :key="index"
                            :class="{
                            }"
                            class="list-item py-2 px-6 cursor-pointer text-left bg-gray-100 flex justify-between items-center border-b-2 border-gray-200 rounded-lg"
                            @click="selectTab(social.id, social.platform)"
                          >
                            <div class="flex items-center text-left mt-2">
                              <span class="flex-1 text-base text-black truncate">{{ social.platform }}</span>
                              <!-- <span class="flex-1 text-sm text-gray-500 truncate">{{ social.url }}</span> -->
                              <span class="flex-1 text-sm text-gray-500 truncate">{{ social.location }}</span>
                              <span class="flex-1 text-sm text-gray-500 truncate">{{ social.date }}</span>
                                <div class="flex space-x-2">
                                  <button 
                                    v-if="hasPermission('edit_social_media')"
                                    class="bg-yellow-500 text-white p-2 rounded-md hover:bg-yellow-400 text-sm flex items-center space-x-2"
                                    @click="editPost(social.id,social.platform)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                      </svg>
                                    {{ $t('social_s.edit') }}
                                  </button>
                                  <button 
                                    v-if="hasPermission('delete_social_media')"
                                    class="bg-red-500 text-white p-2 rounded-md hover:bg-red-400 text-sm flex items-center space-x-2"
                                    @click="confirmDelete(social.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                      </svg>
                                    {{ $t('social_delete.action') }}
                                  </button>
                                </div>
                            </div>
                          </li>
                        </ul>
                    </div>
                    <!-- <div 
                      v-for="(post, index) in posts" 
                      :key="post.id" 
                      class="bg-white shadow-lg rounded-lg p-4 h-full flex flex-col justify-between hover:shadow-xl/20"
                      v-if="!loading"
                    >
                      <div>
                        <div class="flex justify-between items-center">
                          <h3 class="text-xl font-medium text-gray-800">{{ post.name }}</h3>
                          <span class="text-sm text-gray-500">#{{ index + 1 }}</span>
                        </div>
                        <p class="text-gray-700 text-base mt-2 text-left">{{ post.description }}</p>
                      </div>

                      <div class="mt-4">
                        <div class="text-sm text-gray-600">User: <span class="font-semibold">{{ post.user.name }}</span></div>
                        <div class="mt-2 flex flex-wrap gap-1">
                          <span 
                            v-for="(tag, idx) in post.tags" 
                            :key="idx" 
                            class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full"
                          >
                            {{ tag.name }}
                          </span>
                        </div>

                        <div class="mt-4">
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <span class="inline-flex items-center space-x-1">
                                        <span v-for="(socialMed, idx) in post.social_media" :key="idx" class="bg-pink-100 text-pink-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                            {{ socialMed.platform }}
                                        </span>
                                    </span>
                                </div>
                        </div>

                        <div class="mt-4 flex space-x-2">
                          
                          <button 
                            class="bg-red-500 text-white p-2 rounded-md hover:bg-red-400 text-sm w-full"
                            @click="confirmDelete(post.id)"
                          >
                            <svg class="h-5 w-5 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Delete
                          </button>
                        </div>
                      </div>
                    </div>
                    <div v-else class="items-center p-4">
                        <button type="button" class="flex" disabled>
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 animate-spin">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                          </svg>
                          <div class="text-base">Processing....</div>
                        </button>
                    </div> -->
                  
                </div>


                

                <!-- <EditDrawer :show="showDrawer" :postId="selectedPostId" @close="showDrawer=false"  @updated="fetchPosts"/> -->

                <div class="mt-2">
                    <nav aria-label="Page navigation example">
                    <ul class="flex justify-center space-x-2">
                        <li class="page-item" :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                        <a 
                            class="page-link inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-md hover:bg-gray-200"
                            href="#" 
                            aria-label="Previous" 
                            @click.prevent="changePage(currentPage - 1)">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                        </li>

                        <li 
                        v-for="pageNumber in totalPages" 
                        :key="pageNumber"
                        
                        class="page-item">
                        <a 
                            :class="{ 'bg-blue-500 rounded-md text-white': pageNumber === currentPage, 'text-gray-700': pageNumber !== currentPage }"
                            class="page-link inline-flex items-center px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-200 hover:text-black "
                            href="#" 
                            @click.prevent="changePage(pageNumber)">
                            {{ pageNumber }}
                        </a>
                        </li>

                        <li class="page-item" :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }">
                        <a 
                            class="page-link inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-md hover:bg-gray-200"
                            href="#" 
                            aria-label="Next" 
                            @click.prevent="changePage(currentPage + 1)">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <TransitionRoot as="template" :show="edit">
            <Dialog class="relative z-10">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-gray-500/75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                >
                <TransitionChild
                    as="template"
                    enter="ease-out duration-300"
                    enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to="opacity-100 translate-y-0 sm:scale-100"
                    leave="ease-in duration-200"
                    leave-from="opacity-100 translate-y-0 sm:scale-100"
                    leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <DialogPanel
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-lg"
                    >
                    
                    <Edit :socialMediaId="selectedSocialMediaId" :showEdit="showEditForm" :showUserEdit="showUserEditForm" :platformName="selectedSocialMediaName" @close="closeEdit" @updated="fetchSocialMedia" /> 
                    
                    </DialogPanel>
                </TransitionChild>
                </div>
            </div>
            </Dialog>
            </TransitionRoot>

            <WarningModal
        :show="showWarning"
        :title="$t('social_delete.delete_title')"
        :message="$t('social_delete.delete_message')"
        @close="showWarning=false"
        @confirm="deleteSocialMedia"
      >

      </WarningModal>
</template>


<script setup>
import { onMounted, ref } from 'vue';
import axios from "axios";
import { useRouter } from "vue-router";
import Modal from "../modals/Modal.vue";
import EditDrawer from './EditDrawer.vue';
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import Edit from './edit.vue';
import i18n from '../../i18n';
import { useStore } from 'vuex';
import WarningModal from '../modals/WarningModal.vue';


const posts = ref([]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalPages = ref(1);
const totalSocialMedia = ref(0);
const totalPosts = ref(0);
const router = useRouter();
const loading = ref(false);
const socilMediaToDelete = ref(null);
const edit = ref(false);
const selectedSocialMediaId = ref(null);
const selectedSocialMediaName = ref(null);
const socialMediaTypes = ref([]);
const selectedTab = ref(1);
const selectedPlatform = ref("Facebook");
const modalTitle = ref('');
const modalMessage = ref('');
const modalRef = ref(null);
const showEditForm = ref(false);
const showUserEditForm = ref(false);
const store = useStore();
const permissionError = ref('');
const showWarning = ref(false);
const perPage = ref(7);
const showMyPostAddForm = ref(false);
const showModal = (title, message) => {
  modalTitle.value = title;
  modalMessage.value = message;
  modalRef.value?.show();
};

function editPost(id,name) {
    selectedSocialMediaId.value = id;
    selectedSocialMediaName.value = name;
    edit.value = true;
    showEditForm.value = true;
    showUserEditForm.value = false;
    showMyPostAddForm.value = false;
}

const hasRole = (role) => store.getters['auth/hasRole'](role);
const hasPermission = (permission) => store.getters['auth/hasPermission'](permission);


function addPost(id,name) {
    selectedSocialMediaId.value = id;
    selectedSocialMediaName.value = name;
    edit.value = true;
    showEditForm.value = false;
    showUserEditForm.value = false;
    showMyPostAddForm.value = false;
}

function closeEdit() {
    edit.value = false;
}

const selectTab = (platformId,platform) => {
    selectedTab.value = platformId;
    selectedPlatform.value = platform;
    fetchSocialMedia();
};

const token = localStorage.getItem('auth_token');

const axiosInstance = axios.create({
  headers: {
    Authorization: `Bearer ${token}`
  }
});

function confirmDelete(id) {
  socilMediaToDelete.value = id;
  showWarning.value = true;
}


const fetchSocialMedia = async () => {
    try {
        loading.value = true;
         const response = await axios.get(`api/posts/create?page=${currentPage.value}&search=${searchQuery.value}&per_page=${perPage.value}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
            }
        });
        socialMediaTypes.value = response.data.socialMedia;
        totalSocialMedia.value = response.data.total;
        totalPages.value = response.data.last_page;
        loading.value = false;
    } catch (error) {
        if (error.response.status === 403) {
              permissionError.value = error.response.data.message;
        } 
        console.error('Unable to fetch Social media', error);
    }
}

const deleteSocialMedia = async() => {
    try {
        showWarning.value = false;
         loading.value = true;
         const response = await axios.delete('api/social-media/delete', {
            headers: {
                'Authorization': `Bearer ${token}`,
                "Accept-Language": i18n.global.locale.value
            },
            data: { social_media_id:  socilMediaToDelete.value}
        });
        if(response.data.status == 'success') {
            fetchSocialMedia();
        }
        
        loading.value = false;
    } catch (error) {
        if (error.response.status === 403) {
              permissionError.value = error.response.data.message;
        } 
        console.error('Unable to fetch Social media', error);
    }
}

/* const fetchPosts = async () => {
  try {
    loading.value = true;
    const response = await axiosInstance.get(`/api/posts/all?page=${currentPage.value}&search=${searchQuery.value}&socialMediaType=${selectedTab.value}`);
    posts.value = response.data.posts;
    totalPosts.value = response.data.total;
    totalPages.value = response.data.last_page;
    loading.value = false;
  } catch (response) {
    if (response.status == '403') {
      showModal('Error', 'You do not have permission to access this resource');
      router.push('/dashboard');
    }
    console.error('Fetching posts details error', response);
  }
} */

const changePage = (page) => {
  if (page > 0 && page <= totalPages.value) {
    currentPage.value = page;
    fetchSocialMedia();
  }
}





onMounted(async () => {
  await fetchSocialMedia();
  /* fetchPosts(); */
});
</script>


<style scoped>
.custom-badge {
    background-color: blue;
    color: white;
    text-align: center;
    width: 86px;
    height: 23px;
    margin: 10px;
}
.pagination-container {
    margin-top: 20px;
    margin-left: 10px;
    font-size: large;
    text-align: center;
}

.table {
    font-size: large;
    text-align: left;
}

.form-control {
    width: 300px;
}

.add-post {
    width: 100px;
    height: 50px;
    margin-top: 10px;
}

.title {
    display: flex;
    /* justify-content:space-between; */
    margin-top: 12px;
    margin-left: 40px;
    margin-right: 58px;
    text-align: left;
    font-size: small;
}

.pagination {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000; 
  background-color: white; 
  width: 100%;
  padding: 10px 0; 
}
</style>