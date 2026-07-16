import '../../node_modules/vue3-emoji-picker/dist/style.css';
import '../css/app.css';
import './bootstrap.js';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index.js';
import { createPinia } from 'pinia';
import 'viewerjs/dist/viewer.css';
import Viewer from 'v-viewer';

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(Viewer);
app.mount('#app');
