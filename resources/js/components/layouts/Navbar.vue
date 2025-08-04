<template>
        <nav class="bg-white shadow-md">
            <div class="max-w-screen-xl mx-10 py-2">
                <div class="flex items-center justify-between">
                    <a
                        class="text-xl text-left font-semibold text-gray-700 no-underline"
                        href="#"
                        >{{ $t('navbar.userPosts') }}</a
                    >
                    <div class="relative">
                        <div class="flex space-x-2">
                        <div class="language-switcher text-xl mt-2">
                            <select v-model="locale" @change="changeLocale" class="p-1 rounded">
                                <option v-for="loc in availableLocales" :key="loc" :value="loc">
                                {{ getFlag(loc) }} {{ loc.toUpperCase() }}
                                </option>
                            </select>
                        </div>
                        <button
                            ref="buttonRef"
                            type="button"
                            id="user-menu-button"
                            aria-expanded="false"
                            @click="toggleDropdown"
                            class="flex items-center justify-center w-12 h-12 rounded-full hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300"
                            >
                            <img
                                class="w-10 h-10 rounded-full object-cover"
                                :src="userImage"
                                alt=""
                            />
                            </button>

                        <button
                            class="text-gray-700 text-xl hover:text-blue-600 no-underline"
                            aria-haspopup="true"    
                        >
                            {{ userName }}
                        </button>
                        </div>
                        <div
                            v-if="dropdownOpen"
                            ref="dropdownRef"
                            class="absolute right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg w-48"
                        >
                            <a
                                @click="openProfile"
                                class="block px-4 py-2 text-xl text-gray-700 hover:bg-gray-100 no-underline flex space-x-4 text-center"
                                >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span>{{ $t('navbar.profile') }}</span>
                            </a>
                            <a
                                href="#"
                                @click="logoutUser"
                                class="block px-4 py-2 text-xl text-gray-700 hover:bg-gray-100 no-underline flex space-x-4 items-center"
                                >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                                </svg>
                                <span>{{ $t('navbar.logout') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <Profile :show="showProfile" @close="showProfile=false" @updated="fetchUserData"/> 
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import Profile from "../users/profile.vue";
import axios from "axios";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";

const router = useRouter();
const userName = ref("User");
const userRole = ref("User");
const dropdownOpen = ref(false);
const navbarOpen = ref(false);
const showProfile = ref(false);
const userImage = ref(null);
const dropdownRef = ref(null);
const buttonRef = ref(null);

const userId = localStorage.getItem('user_id');
const name = localStorage.getItem("user_name");
const role = localStorage.getItem("user_role");
const token = localStorage.getItem('auth_token');
const store = useStore();

const { locale } = useI18n();

const availableLocales = ['en', 'de'];

const changeLocale = (event) => {
  locale.value = event.target.value;
};

const getFlag = (locale) => {
  const flags = {
    en: '🇺🇸',
    de: '🇩🇪',
    fr: '🇫🇷', // Add more if needed
  };
  return flags[locale] || '🏳️';
};

const logoutUser = () => {
    localStorage.removeItem("auth_token");

    router.push("/login");
};

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

function openProfile() {
    showProfile.value = true;
}

const handleClickOutside = (event) => {
    if (
        dropdownRef.value &&
        !dropdownRef.value.contains(event.target) &&
        buttonRef.value &&
        !buttonRef.value.contains(event.target)
    ) {
        dropdownOpen.value = false;
    }
};



const fetchUserData = async () => {
  try {
    const response = await axios.get(`/api/profile/${userId}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    const user = response.data.user;
    userImage.value = user.image
      ? `${user.image}`
      : `storage/profile_images/sample.jpg`;

   userName.value = user.name;
    localStorage.setItem("user_name", user.name);
  } catch (error) {
    console.error('Failed to fetch user profile:', error);
  }
};

onMounted(() => {
    fetchUserData();
    
    if (name) {
        userName.value = name;
    }

    if (role) {
        userRole.value = role;
    }

    window.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    window.removeEventListener("click", handleClickOutside);
});
</script>

<style src="">
.navbar-nav {
    display: flex;
    font-size: large;
}
</style>
