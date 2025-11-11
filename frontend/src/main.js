import './assets/main.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
import { Quasar, Notify } from 'quasar';
import '@quasar/extras/material-icons/material-icons.css';
import 'quasar/dist/quasar.css';

import router from './router';

createApp(App)
    .use(createPinia())
    .use(router)
    .use(Quasar, {
    plugins: { Notify },
        config: {
          brand: {
            primary: '#757bc8',
            secondary: '#8e94f2',
            accent: '#bbadff',
          }
    }
}).mount('#app')
