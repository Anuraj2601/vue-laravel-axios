<script setup>
import { onMounted, ref } from 'vue';
import SuccessModal from '../modals/SuccessModal.vue';
import Multiselect from 'vue-multiselect';
import axios from 'axios';


const token = localStorage.getItem('auth_token');
const showSuccess = ref(false);
const permissions = ref([]);
const emit = defineEmits(['close','created','update:forms', 'remove']);

const props = defineProps({
    formsP: {
        type: Object,
        required: true
    },
    errorsP: {
        type: Object,
        required: true
    }
});

const fetchPermissions = async() => {
    try {
        const res = await axios.get('api/permissions', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        permissions.value = res.data.permissions;
    } catch (error) {
        console.error('Unable to fetch Permissions', error);
    }
}

const handleRemove = (index) => {
    emit('remove', index);
}


</script>

<template>
    <div class="w-full">
        <SuccessModal
            :show="showSuccess"
            :title="$t('permission_add.success_title')"
            :message="$t('permission_add.success_message')"
            @close="showSuccess=false"
        >
        </SuccessModal>

        <div class="rounded-lg">
            <div class="space-y-6 mt-2">
                <!-- <span class="text-gray-600 text-base">#{{ index + 1 }}</span> -->
                <div class="flex flex-col space-y-4 ">
                    <div class="w-full">
                        <label for="post_name" class="text-lg font-medium text-gray-700 text-left">{{ $t('permission_add.title') }}<span class="text-red-400 text-base font-medium">*</span></label>
                                <input 
                                    type="text" 
                                    v-model="props.formsP.name" 
                                    id="post_name" 
                                    class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                                    :placeholder="$t('permission_add.placeholder')"
                                />
                        <div v-if="props.errorsP?.name" class="text-red-500 text-sm mt-2">
                            {{ $t('permission_add.error_required') }}
                        </div>
                    </div>

                    <!-- <div class="w-full mt-4 text-left">
                        <button type="button" @click="handleRemove(index)" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 focus:outline-none text-sm">Remove</button>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
</style>