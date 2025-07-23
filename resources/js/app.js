import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import 'tailwindcss';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import store from './store';

const app = createApp(App);
if (store.getters['auth/isAuthenticated']) {
  store.dispatch('auth/fetchUser');
}
app.use(store);
app.use(router);
app.mount('#app');
