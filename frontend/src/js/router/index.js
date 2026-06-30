import {createRouter, createWebHistory} from 'vue-router';
import {useAuthStore} from '../store/auth.js';
import Login from '../views/Login.vue';
import Settings from '../views/Settings.vue';
import Convert from "../views/Convert.vue";
import Registration from "../views/Registration.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/login', name: 'login', component: Login, meta: {guest: true}},
        {path: '/register', name: 'register', component: Registration, meta: {guest: true}},
        {path: '/settings', name: 'settings', component: Settings, meta: {auth: true}},
        {path: '/convert', name: 'convert', component: Convert, meta: {auth: true}},
        {path: '/', redirect: '/convert'}
    ]
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.auth && !authStore.isAuthenticated) {
        return next('/login');
    }

    if (to.meta.guest && authStore.isAuthenticated) {
        return next('/convert');
    }

    next();
});

export default router;
