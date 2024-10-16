import { createRouter, createWebHistory } from 'vue-router';

import LoginView from '../views/LoginView.vue';
import RegisterView from '../views/RegisterView.vue';
import HomeView from '../views/HomeView.vue';

import ProfileView from '../views/ProfileView.vue';
import DownloadPage from '../views/DownloadPage.vue';
import ForgotPassView from '../views/ForgotPassView.vue';

import ForumsHubView from '../views/ForumsHubView.vue';
import PostDetails from '../components/PostDetails.vue'; 
import EditProfileView from '../views/EditProfileView.vue'

import UserManagementView from '../components/UserManagementView.vue'




const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/login'
    },
    {
      path: '/UserManagementView',
      name: 'UserManagementView',
      component: UserManagementView
    },
    {
      path: '/EditProfileView',
      name: 'EditProfileView',
      component: EditProfileView,
    },
    {
      path: '/post-details/:postId',
      name: 'PostDetails',
      component: PostDetails,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/ForgotPass',
      name: 'ForgotPass',
      component: ForgotPassView
    },
    {
      path: '/home',
      name: 'home',
      component: HomeView
    },
    {
      path: '/profile',
      name: 'profile',
      component: ProfileView
    },
    {
      path: '/DownloadPage',
      name: 'DownloadPage',
      component: DownloadPage
    },
    {
      path: '/forums_hub',
      name: 'forumshub',
      component: ForumsHubView
    },
  ],
});


router.beforeEach((to, from, next) => {
  if (localStorage.getItem('authToken') && (to.path === '/login' || to.path === '/register')) {
    next({ path: '/home' });
  } else {
    next();
  }
});


export default router;