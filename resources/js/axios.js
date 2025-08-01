import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:8000/api';

axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
axios.defaults.headers.common['Content-Type'] = 'application/json';


export default axios;
