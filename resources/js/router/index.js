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
import store from '../store';

const routes = [
  {
    path: '/',
    redirect: to => {
      if (!store.getters['auth/isAuthenticated']) {
        return '/login';
      }
      return '/dashboard';
    },
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
        meta: { requiresAuth: true, permission: 'manage_users'},
      },
      {
        path: 'posts',
        component: Posts,
        meta: { requiresAuth: true},
      },
      {
        path: 'edit-user/:id',
        component: EditUser,
        meta: { requiresAuth: true, permission: 'manage_users' },
      },
      {
        path: 'add-post',
        component: AddPost,
        meta: { requiresAuth: true, permission: 'add_post' },
      },
      {
        path: 'edit-post/:id',
        component: EditPost,
        meta: { requiresAuth: true, permission: 'edit_post' },
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


router.beforeEach(async (to, from, next) => {
  const requiresAuth = to.meta.requiresAuth;
  const requiredPermission = to.meta.permission;

  if(requiresAuth && !store.getters['auth/isAuthenticated']) {
      return next('/login');
  }else if(requiredPermission && !store.getters['auth/hasPermission']) {
      return next('/unauthorized');
  }
  next();
});

export default router;
