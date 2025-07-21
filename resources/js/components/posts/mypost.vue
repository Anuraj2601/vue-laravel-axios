<template>
        <div class="title text-center mb-6">
            <h2 class="text-2xl font-semibold">My Posts</h2>
        </div>
            <div class="title flex justify-between items-center mb-6">
                <button @click="open = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Add Post</span>
                </button>

                <div class="mb-4 flex-1 ml-4 text-right">
                    <input 
                    v-model="searchQuery" 
                    @input="fetchPosts(1)" 
                    type="text" 
                    class="mt-2 w-65 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                    placeholder="Search posts by name or description..."
                    />
                </div>
            </div>

            <div class="overflow-x-auto ml-15 mr-15">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="(post, index) in posts" :key="post.id" class="bg-white shadow-lg rounded-lg p-4 h-full flex flex-col justify-between" v-if="!loading">
                            <div>
                                <div class="flex justify-between items-center">
                                    <h3 class="text-xl font-medium text-gray-800">{{ post.name }}</h3>
                                    <span class="text-sm text-gray-500">#{{ index + 1 }}</span>
                                </div>
                                <p class="text-gray-700 text-base mt-2 text-left">{{ post.description }}</p>
                            </div>
                            
                            <div class="mt-4">
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <span class="inline-flex items-center space-x-1">
                                        <span v-for="(tag, idx) in post.tags" :key="idx" class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                            {{ tag.name }}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 flex space-x-2">
                                <button 
                                    class="bg-yellow-500 text-white p-2 rounded-md hover:bg-yellow-400 text-sm w-full"
                                    @click="openDrawer(post.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    Edit
                                </button>

                                <button 
                                    class="bg-red-500 text-white p-2 rounded-md hover:bg-red-400 text-sm w-full"
                                    @click="confirmDelete(post.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div v-else class="text-center p-4">Loading posts...</div>
                    </div>
                </div>

        <div class="mt-6">
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

      <WarningModal
        :show="showWarning"
        title="Delete Post"
        message="Are you sure, you want to delete this post? This cannot be undone."
        @close="showWarning=false"
        @confirm="deletePost"
      >

      </WarningModal>

      <TransitionRoot as="template" :show="open">
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
                    
                    <Add @close="closeModal" @created="fetchPosts" />
                    
                    </DialogPanel>
                </TransitionChild>
                </div>
            </div>
            </Dialog>
        </TransitionRoot>

        <EditDrawer :show="showDrawer" :postId="selectedPostId" @close="showDrawer=false" @updated="fetchPosts" />
</template>

<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from "@headlessui/vue";
import Add from "./add.vue";
import WarningModal from "../modals/WarningModal.vue";
import EditDrawer from "./EditDrawer.vue";

const posts = ref([]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalPages = ref(1);
const totalPosts = ref(0);
const router = useRouter();
const open = ref(false);
const loading = ref(false);
const showWarning = ref(false);
const postToDelete = ref(null);
const showDrawer = ref(false);
const selectedPostId = ref(null);

function openModal() {
  open.value = true
}

function closeModal() {
  open.value = false
}

function openDrawer(id) {
    selectedPostId.value = id;
    showDrawer.value = true;
}

const token = localStorage.getItem('auth_token');
const userRole = localStorage.getItem('user_role');

const axiosInstance = axios.create({
    headers: {
      Authorization: `Bearer ${token}`
    }
});

const fetchPosts = async () => {
    try {
        loading.value = true;
        const response = await axiosInstance.get(`/api/posts?page=${currentPage.value}&search=${searchQuery.value}`);
        posts.value = response.data.posts;
        
        totalPosts.value = response.data.total;
        totalPages.value = response.data.last_page;
        loading.value = false;
        console.log(response);
        console.log(userRole);
    } catch (error) {
        console.error('Fetching posts details error', error);
    }
}

const changePage = (page) => {
    if(page > 0 && page <= totalPages.value) {
        currentPage.value = page;
        fetchPosts();
    }
}

/* const editPost = (postId) => {
    try {
        router.push(`/edit-post/${postId}`);
    } catch (error) {
        console.error('edit post redirect error');
    }
} */

function confirmDelete(id) {
    postToDelete.value = id;
    showWarning.value = true;
}

const deletePost = async () => {
    try {
        showWarning.value = false;
        const response = await axiosInstance.delete(`/api/posts/delete/${postToDelete.value}`);

        if(response.data.status == 'success') {
           fetchPosts();
        }
    } catch (error) {
        console.error("Delete post Error" , error)
    }
}

/* const addPost = () => {
    try {
        router.push('/add-post');
    } catch (error) {
        console.log('add post redirect error', error);
    }
} */

onMounted(fetchPosts);
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
    justify-content:space-between;
    margin-top: 20px;
    margin-left: 60px;
    margin-right: 120px;
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