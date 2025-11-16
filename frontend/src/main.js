import './assets/main.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
import { Quasar, Notify, Dialog, } from 'quasar';
import '@quasar/extras/material-icons/material-icons.css';
import 'quasar/dist/quasar.css';

import router from './router';

createApp(App)
    .use(createPinia())
    .use(router)
    .use(Quasar, {
    plugins: { Notify, Dialog, },
        config: {
          brand: {
          primary: '#4A7BFF',
          secondary: '#6B8EFF',
          accent: '#8FB1FF',
          dark: '#2D5BCC',
          positive: '#21BA45',
          negative: '#FF4757',
          info: '#4A7BFF',
          warning: '#FFA502'
        }
    }
}).mount('#app')
