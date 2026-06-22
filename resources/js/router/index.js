import {createRouter, createWebHistory} from 'vue-router';
import {useAuthStore} from '../store/auth';
import Login from '../views/Login.vue';
import Settings from '../views/Settings.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/login', name: 'login', component: Login, meta: {guest: true}},
        {path: '/settings', name: 'settings', component: Settings, meta: {auth: true}},
        {path: '/', redirect: '/settings'}
    ]
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.auth && !authStore.isAuthenticated) {
        return next('/login');
    }

    if (to.meta.guest && authStore.isAuthenticated) {
        return next('/settings');
    }

    next();
});

export default router;
