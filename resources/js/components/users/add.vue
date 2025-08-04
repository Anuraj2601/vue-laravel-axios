<script setup>
import { onMounted, ref } from 'vue';
import SuccessModal from '../modals/SuccessModal.vue';
import Multiselect from 'vue-multiselect';
import axios from 'axios';


const token = localStorage.getItem('auth_token');
const showSuccess = ref(false);
const roles = ref([]);
const emit = defineEmits(['close','created','update:forms']);

const props = defineProps({
    formsU: {
        type: Object,
        required: true
    },
    errorsU: {
        type: Object,
        required: true
    }
});

const fetchRoles = async() => {
    try {
        const res = await axios.get('api/roles', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        roles.value = res.data.roles;
    } catch (error) {
        console.error('Unable to fetch Permissions', error);
    }
}

/* const handleRemove = (index) => {
    emit('remove', index);
} */

onMounted(fetchRoles);
</script>

<template>
    <div class="w-full">
        <SuccessModal
            :show="showSuccess"
            :title="$t('user_add.success.title')"
            :message="$t('user_add.success.message')"
            @close="showSuccess=false"
        >
        </SuccessModal>

        <div class="rounded-lg">
            <div class="space-y-6 mt-2">
                <span class="text-gray-600 text-base"></span>
                <div class="flex flex-col space-y-4 ">
                    <div class="w-full">
                        <div class="mb-4">
                            <label for="name" class="block text-lg font-medium text-gray-700 text-left">{{ $t('user_add.form.name_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input v-model="props.formsU.name" type="text" :placeholder="$t('user_add.form.name_placeholder')" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                            <span v-if="props.errorsU?.name" class="text-red-500 text-sm mt-2">
                                     {{ $t('user_add.errors.name') }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-lg font-medium text-gray-700 text-left">{{ $t('user_add.form.email_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input v-model="props.formsU.email" type="email" :placeholder="$t('user_add.form.email_placeholder')" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                            <span v-if="props.errorsU?.email" class="text-red-500 text-sm mt-2">
                                     {{ $t('user_add.errors.email') }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <label for="role" class="block text-lg font-medium text-gray-700 text-left">{{ $t('user_add.form.role_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <select v-model="props.formsU.user_role" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" id="role">
                                <option v-for="user_role in roles" :key="user_role.id" :value="user_role.name">{{ user_role.name }}</option>
                            </select>
                            <span v-if="props.errorsU?.user_role" class="text-red-500 text-sm mt-2">
                                     {{ $t('user_add.errors.user_role') }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-lg font-medium text-gray-700 text-left">{{ $t('user_add.form.password_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input v-model="props.formsU.password" type="password" :placeholder="$t('user_add.form.password_placeholder')" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                            <span v-if="props.errorsU?.password" class="text-red-500 text-sm mt-2">
                                     {{ $t('user_add.errors.password') }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-lg font-medium text-gray-700 text-left">{{ $t('user_add.form.password_confirmation_label') }}<span class="text-red-400 text-base font-medium">*</span></label>
                            <input v-model="props.formsU.password_confirmation" type="password" :placeholder="$t('user_add.form.password_confirmation_placeholder')" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                            <span v-if="props.errorsU?.password_confirmation" class="text-red-500 text-sm mt-2">
                                     {{ $t('user_add.errors.password_confirmation') }}
                            </span>
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