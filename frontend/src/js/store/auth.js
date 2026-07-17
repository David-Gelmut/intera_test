import {defineStore} from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')) || null,
        isMobileMenuOpen: false,
    }),
    getters: {
        isAuthenticated: (state) => !!state.user,
        isVerified: (state) => state.user && state.user.email_verified_at !== undefined && state.user.email_verified_at !== null,
        userRole: (state) => state.user ? state.user.role : null,
        isActive: (state) => state.user && state.user.status === 'active'
    },
    actions: {
        async getCsrfCookie() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getUser(){
            const userResponse = await axios.get('/api/user');
            this.user = userResponse.data;
            localStorage.removeItem('user');
            localStorage.setItem('user', JSON.stringify(userResponse.data));
        },
        async login(credentials) {

            try {
                await this.getCsrfCookie();
                const response = await axios.post('/api/login', credentials);
                this.user = response.data.user;
                localStorage.setItem('user', JSON.stringify(response.data.user));
            } catch (error) {
                console.error('Ошибка при логине на сервере:', error);
                throw error;
            }
        },
        async logout() {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Ошибка при логауте на сервере:', error);
            } finally {
                this.user = null;
                localStorage.removeItem('user');
            }
        },
        async register(credentials) {
            try {
                await this.getCsrfCookie();
                const response = await axios.post('/api/register', credentials);
                this.user = response.data.user;
                localStorage.setItem('user', JSON.stringify(response.data.user));

            } catch (error) {
                console.error('Ошибка при регистрации на сервере:', error);
                throw error;
            }
        },
        toggleMobileMenu() {
            this.isMobileMenuOpen = !this.isMobileMenuOpen;
        },

        closeMobileMenu() {
            this.isMobileMenuOpen = false;
        },
    }
});
