import '../css/app.css';
import './bootstrap.js';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index.js';
import { createPinia } from 'pinia';

const app = createApp(App);

//app.config.globalProperties.$http = axios;

app.use(createPinia());
app.use(router);
app.mount('#app');
