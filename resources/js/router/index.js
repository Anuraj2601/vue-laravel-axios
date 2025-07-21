import { createRouter, createWebHistory } from 'vue-router';
import Register from '../components/Register.vue';
import Login from '../components/Login.vue';
import Dashboard from '../components/Dashboard.vue';
import Users from '../components/users/users.vue';
import Posts from '../components/posts/posts.vue';
import EditUser from '../components/users/edit.vue';
import AddPost from '../components/posts/add.vue';
import EditPost from '../components/posts/edit.vue';
import MyPosts from '../components/posts/mypost.vue';
import Layout from '../components/layouts/Layout.vue';

const routes = [
  {
    path: '/',
    component: Layout, 
    children: [
      {
        path: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
      },
      {
        path: 'users',
        component: Users,
        meta: { requiresAuth: true },
      },
      {
        path: 'posts',
        component: Posts,
        meta: { requiresAuth: true },
      },
      {
        path: 'edit-user/:id',
        component: EditUser,
        meta: { requiresAuth: true },
      },
      {
        path: 'add-post',
        component: AddPost,
        meta: { requiresAuth: true },
      },
      {
        path: 'edit-post/:id',
        component: EditPost,
        meta: { requiresAuth: true },
      },
      {
        path: 'myposts',
        component: MyPosts,
        meta: { requiresAuth: true },
      },
    ],
  },
  {
    path: '/register',
    component: Register,
  },
  {
    path: '/login',
    component: Login, 
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});


router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token');

  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else {
    next();
  }
});

export default router;
