import {createRouter, createWebHistory} from 'vue-router';
import {useAuthStore} from '../store/auth.js';
import Login from '../views/Auth/Login.vue';
import Settings from '../views/Settings.vue';
import Convert from "../views/Convert.vue";
import Registration from "../views/Auth/Registration.vue";
import Profile from "../views/Auth/Profile.vue";
import EmailVerify from "../views/Auth/EmailVerify.vue";
import ForgotPassword from "../views/Auth/ForgotPassword.vue";
import ResetPassword from "../views/Auth/ResetPassword.vue";
import EmailVerifyNotice from "../views/Auth/EmailVerifyNotice.vue";
import AdminPanel from "../views/Admin/AdminPanel.vue";
import Dashboard from "../views/Dashboard.vue";
import BannedPage from "../views/BannedPage.vue";
import MainLayout from "../views/Layouts/MainLayout.vue";
import Users from "../views/Admin/Users.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [

        {
            path: '/',
            component: MainLayout,
            meta: {auth: true, hidden: true,},
            children: [
                {
                    path: '/settings',
                    name: 'Maps',
                    component: Settings,
                    meta: {auth: true, verified: true, allowedRoles: ['user', 'admin']}
                },
                {
                    path: '/convert',
                    name: 'Converter',
                    component: Convert,
                    meta: {auth: true, verified: true, allowedRoles: ['user', 'admin']}
                },
                {
                    path: '/profile',
                    name: 'Profile',
                    component: Profile,
                    meta: {auth: true, hidden: true, verified: true, allowedRoles: ['user', 'admin']}
                },
                {
                    path: '/dashboard',
                    name: 'Dashboard',
                    component: Dashboard,
                    meta: {auth: true, verified: true, allowedRoles: ['user', 'admin']}
                },
                {
                    path: '/users',
                    name: 'Users',
                    component: Users,
                    meta:
                        {
                            auth: true,
                            verified: true,
                            allowedRoles: ['admin']
                        }
                },
            ]
        },
        {
            path: '/login',
            name: 'Login',
            component: Login,
            meta:
                {
                    guest: true,
                }
        },
        {
            path: '/register',
            name: 'Register',
            component: Registration,
            meta:
                {
                    guest: true
                }
        },
        {
            path: '/forgot-password',
            name: 'ForgotPassword',
            component: ForgotPassword,
            meta:
                {
                    guest: true,
                    auth: false
                }
        },
        {
            path: '/reset-password/:token',
            name: 'ResetPassword',
            component: ResetPassword,
            meta: {guest: true, auth: false}
        },
        {
            path: '/email-verify',
            name: 'EmailVerify',
            component: EmailVerify,
            meta:
                {
                    auth: true,
                    hidden: true
                }
        },

        // --- РОУТЫ ДЛЯ ЛЮБЫХ ЗАЛОГИНЕННЫХ ПОЛЬЗОВАТЕЛЕЙ ---
        {
            path: '/email-verify-notice',
            name: 'EmailVerifyNotice',
            component: EmailVerifyNotice,
            meta:
                {
                    auth: true,
                    hidden: true,
                    verified: false,
                    allowedRoles: ['user', 'admin']
                }
        },
      /*  {
            path: '/admin/panel',
            name: 'AdminPanel',
            component: AdminPanel,
            meta: {
                auth: true,
                verified: true,
                allowedRoles: ['admin']
            }
        },*/
        {
            path: '/banned',
            name: 'BannedPage',
            component: BannedPage,
            meta:
                {
                    auth: true,
                    hideLayout: true,
                    hidden: true
                }
        },
    ]
});

router.beforeEach((to, from, next) => {

    const authStore = useAuthStore();

    const isAuthenticated = authStore.isAuthenticated; // true, если юзер есть в базе браузера
    const isVerified = authStore.isVerified; // true, если email подтвержден
    const isActive = authStore.isActive;
    const userRole = authStore.userRole;

    // console.log(to.name);

    // 1. ПРОВЕРКА НА СТАТУС: Если юзер залогинен, но НЕ активен
    if (isAuthenticated && !isActive && to.name !== 'BannedPage' && to.name !== 'EmailVerify'/* && isVerified */) {
        return next({name: 'BannedPage'});
    }

    if (to.matched.some(record => record.meta.guest) && isAuthenticated) {
        return next({name: 'Profile'});
    }
    if (to.matched.some(record => record.meta.auth) && !isAuthenticated) {
        return next({name: 'Login'});
    }

    // 3. Исключение для страницы верификации
    if (to.matched.some(record => record.meta.verified) && !isVerified) {
        if (to.name === 'EmailVerify') return next();
        return next({name: 'EmailVerifyNotice'});
    }

    // 4. ПРОВЕРКА РОЛЕЙ: Если у роута есть ограничения по ролям
    if (to.meta.allowedRoles) {
        const hasRole = to.meta.allowedRoles.includes(userRole);
        if (!hasRole) {
            //alert('У вас нет прав для доступа к этой странице!');
            return next({name: 'Profile'}); // Возвращаем на базовую страницу
        }
    }

    next();
});

export default router;
