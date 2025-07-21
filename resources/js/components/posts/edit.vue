<template>
    <div class="container mx-auto px-4 mt-10">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Post</h2>
      <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">

        <form @submit.prevent="updatePost">
          <div class="mb-4">
            <label for="name" class="block text-lg font-medium text-gray-700 text-left">Name</label>
            <input 
              v-model="post.name" 
              type="text" 
              placeholder="Post Name" 
              class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
            />
            <div v-if="errors.name" class="text-red-500 text-sm mt-2">
              {{ errors.name }}
            </div>
          </div>

          <div class="mb-4">
            <label for="description" class="block text-lg font-medium text-gray-700 text-left">Description</label>
            <input 
              v-model="post.description" 
              type="text" 
              placeholder="Post Description" 
              class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
            />
            <div v-if="errors.description" class="text-red-500 text-sm mt-2">
              {{ errors.description }}
            </div>
          </div>

          <div class="mb-4">
            <label for="tags" class="block text-lg font-medium text-gray-700 text-left">Tags</label>
            <Multiselect
              v-model="post.tags"
              :options="tags"
              :multiple="true"
              :close-on-select="false"
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
            <div v-if="errors.tags" class="text-red-500 text-sm mt-2">
              {{ errors.tags }}
            </div>
          </div>

          <div class="mt-6">
            <button 
              type="submit" 
              class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
              Update Post
            </button>
          </div>

        </form>
      </div>
    </div>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import axios from 'axios';
    import { useRoute, useRouter } from 'vue-router';
    import Multiselect from 'vue-multiselect';

    const emit = defineEmits(['updated']);
    const post = ref({
        name: '',
        description: '',
        id: '',
        tags: []
    });

    const errors = ref({
        name: '',
        description: '',
        tags: ''
    });

    const token = localStorage.getItem('auth_token');
    const userRole = localStorage.getItem('user_role');
    
    const tags = ref([]);

    const router = useRouter();
    const route  = useRoute();
    const props = defineProps({
      postId: {
        type: [String, Number],
        required: true,
      }
    })

const editPost = async () => {
    try {
        const response = await axios.get(`/api/posts/edit/${props.postId}`, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        post.value = response.data.post;
        tags.value = response.data.tags;
    } catch (error) {
        console.error("Retrieve posts details error" , error);
    }
}
    
    const updatePost = async () => {
        try {
            const token = localStorage.getItem('auth_token');
            const response = await axios.put( `/api/posts/update/${post.value.id}`, {
                name: post.value.name,
                description: post.value.description,
                tags: post.value.tags.map(tag => tag.id)
            },{
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }
        );
            if(response.data.status == 'success') {
                emit('updated')
            }
            console.log(response.data);
        } catch (error) {
            if(error.response) {
                if (error.response && error.response.status === 422) {
                errors.value = error.response.data.error; 
                console.error('Validation errors:', errors.value);
                }
            }
            console.log("Post Updated failed. " , error);
        }
    }

    onMounted(editPost);

</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
.error {
    font-size: medium;
}
</style>