<template>
    <div class="main-layout">
        <nav v-if="authStore.isAuthenticated">
            <div v-for="route in menuRoutes" :key="route.path">
                <router-link :to="route.path">
                    {{ route.name }}
                </router-link>
            </div>
            <button class="main-button" @click="handleLogout">Выйти</button>
        </nav>
        <router-view></router-view>
    </div>
</template>
<script setup>
import { useAuthStore } from './store/auth.js';
import { useRouter } from 'vue-router';
import { computed } from 'vue'

const authStore = useAuthStore();
const router = useRouter();

const menuRoutes = computed(() => {
    return router.getRoutes().filter(route => route.meta?.auth)
})

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>

<style>
/*
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f3f4f6;
    color: #1f2937;
}

.main-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #f5f5f5;
}

button {
    width: 100%;
    padding: 0.75rem;
    background: #3182ce;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    font-weight: bold;
}

.main-button {
    width: 100%;
    padding: 0.75rem;
    background: #3182ce;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    font-weight: bold;
    margin-top: 6rem;
    margin-bottom: 0;
}
*/

</style>
