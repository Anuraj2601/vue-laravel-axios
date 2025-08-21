import axios from 'axios';
import i18n from './i18n';

axios.defaults.baseURL = 'http://localhost:8000/api';

axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
axios.defaults.headers.common['Content-Type'] = 'application/json';


axios.interceptors.request.use(config => {
  const language = i18n.global.locale.value;
  config.headers['Accept-Language'] = language;
  return config;
}, error => {
  return Promise.reject(error);
});
export default axios;
