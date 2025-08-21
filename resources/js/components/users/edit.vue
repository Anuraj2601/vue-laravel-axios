<template>
        <div class="w-full">
            <div class="bg-white p-6 rounded-lg">
            <div class="flex justify-between">
                <h2 class="text-2xl font-semibold text-gray-800">{{ $t('user_manage.title') }}</h2>
                <button
                    type="button"
                    class=" inline-flex justify-center rounded-md bg-red-600 px-2 py-2 text-sm font-semibold text-gray-100 hover:bg-red-800 sm:mt-0 sm:w-auto"
                    @click="cancel"
                    ref="cancelButtonRef"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
        <div class="mb-2">
            <div class="border-t border-gray-300 mt-4 mb-2 flex justify-between"  v-if="!props.showEdit">
                    <h2 class="text-2xl mt-2 font-semibold">{{ $t('user_manage.create_title') }}</h2>
                    <!-- <button type="button" @click="addForm" class="btn btn-primary mb-4 px-2 py-2 mt-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none text-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                      </svg>
                    </button> -->
            </div>
            <Add :formsU="formsU" :errorsU="errorsU" @remove="removeForm" v-if="!props.showEdit" />

            <div class="border-t border-gray-300 mt-4 mb-2 flex justify-between" v-if="props.showEdit">
                <h2 class="text-2xl font-semibold">{{ $t('user_manage.edit_title') }}</h2>
            </div>
            <form @submit.prevent="updateUser" v-if="props.showEdit">
                <div class="mb-4">
                    <label for="name" class="block text-lg font-medium text-gray-700 text-left">{{ $t('user_manage.form.name_label') }}</label>
                    <input v-model="name" type="text" :placeholder="$t('user_manage.form.name_placeholder')" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                    <span v-if="errors.name" class="text-red-500 text-sm mt-2">
                            {{ errors.name }}
                    </span>
                    <span v-else-if="nameError" class="text-red-500 text-sm mt-2">
                        {{ nameError }}
                    </span>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-lg font-medium text-gray-700 text-left">{{ $t('user_manage.form.email_label') }}</label>
                    <input v-model="email" type="email" :placeholder="$t('user_manage.form.email_placeholder')" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                    <span v-if="errors.email" class="text-red-500 text-sm mt-2">
                            {{ errors.email }}
                    </span>
                    <span v-else-if="emailError" class="text-red-500 text-sm mt-2">
                        {{ emailError }}
                    </span>
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-lg font-medium text-gray-700 text-left">{{ $t('user_manage.form.role_label') }}</label>
                    <select v-model="user_role" class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" id="role">
                        <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                    </select>
                    <span v-if="errors.user_role" class="text-red-500 text-sm mt-2">
                            {{errors.user_role }}
                    </span>
                    <span v-else-if="user_roleError" class="text-red-500 text-sm mt-2">
                        {{ user_roleError }}
                    </span>
                </div>

                <div class="mb-4 relative">
                    <label class="block text-lg font-medium text-gray-700 text-left">Password</label>
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        v-model="password"
                        placeholder="Enter new password"
                        class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                    />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute right-3 top-11 text-gray-500">
                        {{ showPassword ? 'Hide' : 'Show' }}
                    </button>
                    <span v-if="errors.password" class="text-red-500 text-sm mt-2">{{ errors.password }}</span>
                    <span v-else-if="passwordError" class="text-red-500 text-sm mt-2">
                        {{ passwordError }}
                    </span>
                </div>

                
                <div class="mb-4 relative">
                    <label class="block text-lg font-medium text-gray-700 text-left">Confirm Password</label>
                    <input
                        :type="showPasswordConfirm ? 'text' : 'password'"
                        v-model="password_confirmation"
                        placeholder="Confirm new password"
                        class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                    />
                    <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                        class="absolute right-3 top-11 text-gray-500">
                        {{ showPasswordConfirm ? 'Hide' : 'Show' }}
                    </button>
                    <span v-if="errors.password_confirmation" class="text-red-500 text-sm mt-2">
                        {{ errors.password_confirmation }}
                    </span>
                    <span v-else-if="passwordConfirmError" class="text-red-500 text-sm mt-2">
                        {{ passwordConfirmError }}
                    </span>
                </div>
                
                <div class="mt-6">
                    <button 
                        type="submit" 
                        class="w-full py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 text-sm">
                        {{$t('user_manage.form.update_button')}}
                    </button>
                </div>
            </form>
            <div class="mt-6" v-if="!props.showEdit">
                <button 
                    type="submit" 
                    @click="createUser"
                    class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                    {{ $t('user_manage.form.submit_button') }}
                </button>
            </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, ref, watch } from 'vue';
    import axios from 'axios';
    import { useRoute, useRouter } from 'vue-router';
    import Add from './add.vue';
    import i18n from '../../i18n';
    import store from 'vuex';
    import * as yup from 'yup';
    import { useI18n } from 'vue-i18n';
import { useField, useForm } from 'vee-validate';

    const { t } = useI18n();
    const emit = defineEmits(['update:formsU', 'close', 'created']);
    const user = ref({
        name: '',
        email: '',
        id: '',
        user_role: '',
        password: ref(''),
        password_confirmation: ref(''),
    });

    const errorsU = ref({});

    const formsU = ref({
        name: '', 
        email: '',
        user_role: '',
        password: '',
        password_confirmation: ''
    });

    const errors = ref({
        name: '',
        email: '',
        user_role: '',
        password: '',
        password_confirmation: ''
    });

    const showPassword = ref(false);
    const showPasswordConfirm = ref(false);
    const router = useRouter();
    const route  = useRoute();
    const token = localStorage.getItem('auth_token');
    const userId = route.params.id;
    const roles = ref([]);

    const props = defineProps({
        userId: {
            type: [String, Number],
            required: true,
        },
        showEdit: {
            type: [Boolean],
            required: true,
        }
    })

    function cancel() {
        emit('close');
    }

    useForm({
        validateOnInput: true,
    });

    const {
        value: name,
        errorMessage: nameError
    } = useField('name', yup.string().required(t('user_manage.errors.name')))

    const {
        value: email,
        errorMessage: emailError
    } = useField('email', yup.string().required(t('user_manage.errors.email')))

    const {
        value: user_role,
        errorMessage: user_roleError
    } = useField('user_role', yup.string().required(t('user_manage.errors.user_role')))

    const {
        value: password,
        errorMessage: passwordError
    } = useField(
        'password',
        yup
            .string()
            .nullable()
            .notRequired()
            .test(
            'min-if-filled',
            t('user_manage.password.min'),
            value => !value || value.length >= 8
        )
    );

    const {
        value: password_confirmation,
        errorMessage: passwordConfirmError
    } = useField(
        'password_confirmation',
        yup
            .string()
            .nullable()
            .when('password', {
                is: val => val && val.length > 0,
                then: yup.string().oneOf([password.value], 'Passwords must match'),
                otherwise: yup.string().nullable(),
            })
    );


    watch(name, (newVal) => {
        user.value.name = newVal;
    });

    watch(email, (newVal) => {
        user.value.email = newVal;
    });

    watch(user_role, (newVal) => {
        user.value.user_role = newVal;
    });

    watch(password, (newVal) => {
        user.value.password = newVal;
    });

    watch(password_confirmation, (newVal) => {
        user.value.password_confirmation = newVal;
    });

    /* function addForm() {
    open.value= true;
        formsU.value.push({
            name: '', 
            email: '',
            user_role: ''
        })
    }

    function removeForm(index) {
        formsU.value.splice(index, 1);
    } */

    const editUser = async () => {
        try {
            const response = await axios.get(`/api/edit/${props.userId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    "Accept-Language": i18n.global.locale.value
                }
            });

            roles.value = response.data.roles;
            user.value  = response.data.user;
            name.value = response.data.user.name;
            email.value = response.data.user.email;
            user_role.value = response.data.user_role[0];
            password.value = '';
            password_confirmation.value = '';
        } catch (error) {
            console.error("Retrieve User details error" , error);
        }
    }

    const fetchRoles = async() => {
        try {
            const response = await axios.get('api/roles', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    "Accept-Language": i18n.global.locale.value
                }
            });
            roles.value = response.data.roles;
        } catch (error) {
            
        }
    }

    const createUser = async() => {
        errorsU.value = {};
        try {
            const response = await axios.post('api/register', {
                name: formsU.value.name,
                email: formsU.value.email,
                user_role: formsU.value.user_role,
                password: formsU.value.password,
                password_confirmation: formsU.value.password_confirmation
            }, {
                headers: {
                    "Authorization" : `Bearer ${token}`,
                    "Accept-Language": i18n.global.locale.value
                }
            });
            if(response.data.status == 'success') {
                emit('close');
                emit('updated');
            }
        } catch (error) {
            if(error.response) {
                if(error.response.status === 422) {
                    const backendErrors = error.response.data.error;
                    for (const field in backendErrors) {
                        errorsU.value[field] = backendErrors[field];
                    }
                } else {
                    console.error('Unexpected error: ', error);
                }
            }
        }
    }
    
    const updateUser = async (id) => {
        errors.value = {};
        console.log(user.value.user_role);
        try {
            const payload = {
                name: user.value.name,
                email: user.value.email,
                role: user.value.user_role,
            };

            if (password.value) {
                payload.password = password.value;
                payload.password_confirmation = password_confirmation.value;
            }
            const response = await axios.put( `/api/update/${user.value.id}`, 
                payload
            ,{
                headers: {
                    'Authorization': `Bearer ${token}`,
                    "Accept-Language": i18n.global.locale.value
                }
            }
        );
            if(response.data.status == 'success') {
                emit('close');
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
            console.log("User Updated failed. " , error);
        }
    }

    onMounted(async () => {
        await fetchRoles();
            if(props.showEdit) {
                editUser();
            } else {
                fetchRoles();
            }
        }
    );

</script>

<style>

</style>