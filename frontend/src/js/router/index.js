import {createRouter, createWebHistory} from 'vue-router';
import {useAuthStore} from '../store/auth.js';
import Login from '../views/Login.vue';
import Settings from '../views/Settings.vue';
import Convert from "../views/Convert.vue";
import Registration from "../views/Registration.vue";
import Profile from "../views/Profile.vue";
import EmailVerify from "../views/EmailVerify.vue";
import ForgotPassword from "../views/ForgotPassword.vue";
import ResetPassword from "../views/ResetPassword.vue";
import EmailVerifyNotice from "../views/EmailVerifyNotice.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [

        {path: '/', redirect: '/profile'},

        // --- ОТКРЫТЫЕ РОУТЫ ДЛЯ ГОСТЕЙ ---
        {path: '/login', name: 'Login', component: Login, meta: {guest: true}},// Сюда нельзя залогиненным
        {path: '/register', name: 'Register', component: Registration, meta: {guest: true}},
        {path: '/forgot-password', name: 'ForgotPassword', component: ForgotPassword, meta: {guest: true, auth: false}},
        {
            path: '/reset-password/:token',
            name: 'ResetPassword',
            component: ResetPassword,
            meta: {guest: true, auth: false}
        },
        {path: '/email-verify', name: 'EmailVerify', component: EmailVerify, meta: {auth: true, hidden: true}},

        // --- РОУТЫ ДЛЯ ЛЮБЫХ ЗАЛОГИНЕННЫХ ПОЛЬЗОВАТЕЛЕЙ ---
        {
            path: '/email-verify-notice',
            name: 'EmailVerifyNotice',
            component: EmailVerifyNotice,
            meta: {auth: true, hidden: true, verified: false} // Нужен логин, но подтверждать email не обязательно
        },

        // --- СЕКРЕТНЫЕ РОУТЫ (ТОЛЬКО ДЛЯ ПОДТВЕРЖДЕННЫХ EMAIL) ---
        {path: '/settings', name: 'Maps', component: Settings, meta: {auth: true, verified: true}},
        {path: '/convert', name: 'Converter', component: Convert, meta: {auth: true, verified: true}},
        {path: '/profile', name: 'Profile', component: Profile, meta: {auth: true, hidden: true, verified: true}},

    ]
});

router.beforeEach((to, from, next) => {

    const authStore = useAuthStore();

    const isAuthenticated = authStore.isAuthenticated; // true, если юзер есть в базе браузера
    const isVerified = authStore.isVerified; // true, если email подтвержден

    // 1. Запрещаем авторизованным заходить на Login/Register
    if (to.matched.some(record => record.meta.guest) && isAuthenticated) {
        return next({ name: 'Dashboard' });
    }

    // 2. Если страница требует авторизации, а пользователь — гость
    if (to.matched.some(record => record.meta.auth) && !isAuthenticated) {
        return next({ name: 'Login' });
    }

    // 3. Если почта уже подтверждена, не пускаем на страницу-напоминалку
    if (to.name === 'EmailVerifyNotice' && isVerified) {
        return next({ name: '/profile' }); // Или имя вашего роута профиля
    }

    // 4. Если роут требует подтвержденную почту, а её нет...
    if (to.matched.some(record => record.meta.verified) && !isVerified) {

        // ИСКЛЮЧЕНИЕ: Если пользователь перешел по ссылке из письма на страницу верификации,
        // мы ОСТАНАВЛИВАЕМ проверку и просто пускаем его туда!
        if (to.name === 'EmailVerify') {
            return next();
        }

        // Во всех остальных случаях отправляем на страницу-напоминалку
        return next({ name: 'EmailVerifyNotice' });
    }











    // СИТУАЦИЯ 1: Роут только для гостей (Login/Register), а пользователь уже залогинен
 /*   if (to.matched.some(record => record.meta.guest) && isAuthenticated) {
        return next({ name: 'Profile' });
    }

    // СИТУАЦИЯ 2: Роут требует авторизации, а пользователь — гость
    if (to.matched.some(record => record.meta.auth) && !isAuthenticated) {
        return next({ name: 'Login' });
    }

    // СИТУАЦИЯ 3: Пользователь Пытается зайти на страницу уведомления, но уже ПОДТВЕРДИЛ email
    if (to.name === 'EmailVerifyNotice' && isVerified) {
        return next({ name: 'Profile' }); // Перенаправляем его в профиль
    }

    // СИТУАЦИЯ 4: Роут требует ПОДТВЕРЖДЕННЫЙ email, а у юзера он еще не подтвержден
  /!*  if (to.matched.some(record => record.meta.verified) && !isVerified) {
        return next({ name: 'EmailVerifyNotice' });
    }*!/

    if (to.matched.some(record => record.meta.verified) && !isVerified) {
        if (to.name === 'EmailVerify') {
            return next();
        }
        return next({ name: 'EmailVerifyNotice' });
    }
*/
/*    // СИТУАЦИЯ 1: Роут только для гостей (Login/Register), а пользователь уже залогинен
    if (to.matched.some(record => record.meta.guest) && isAuthenticated) {
        return next({name: 'Profile'}); // Выкидываем его в личный кабинет
    }

    // СИТУАЦИЯ 2: Роут требует авторизации, а пользователь гость
    if (to.matched.some(record => record.meta.auth) && !isAuthenticated) {
        return next({name: 'Login'}); // Отправляем логиниться
    }

    // СИТУАЦИЯ 3: Роут требует ПОДТВЕРЖДЕННЫЙ email, а у юзера он не подтвержден
    if (to.matched.some(record => record.meta.verified) && !isVerified) {
        // Отправляем на страницу-напоминалку "Пожалуйста, подтвердите вашу почту"
        return next({name: 'EmailVerifyNotice'});
    }

    // СИТУАЦИЯ 4: [ДОБАВЛЕНО] Пользователь Пытается зайти на страницу уведомления, но уже ПОДТВЕРДИЛ email
    if (to.name === 'EmailVerifyNotice' && isVerified) {
        return next({ name: 'Profile' }); // Перенаправляем его в профиль
    }

    //Пользователь Пытается зайти на страницу уведомления, но уже ПОДТВЕРДИЛ email
    if (to.name === 'EmailVerifyNotice' && isVerified) {
        return next({ name: 'Profile' }); // Перенаправляем его в профиль
    }*/


    next();
});

export default router;
