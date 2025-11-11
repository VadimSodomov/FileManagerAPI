import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore';
import HomeView from '../views/HomeView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
    {
        path: '/',
        name: 'Home',
        component: HomeView,
        meta: {
            title: 'Главная',
            requiresAuth: true,
        },
    },
    {
        path: '/login-reg',
        name: 'LoginReg',
        component: () => import('../views/LoginRegView.vue'),
        meta: {
            title: 'Вход и регистрация',
            guestOnly: true,
        },
    },
  ],
})

let isSessionLoaded = false;

const ensureSessionLoaded = (auth) => {
    if (!isSessionLoaded) {
        auth.getDataFromStorage?.();
        isSessionLoaded = true;
    }
};

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();
    ensureSessionLoaded(auth);

    if (to.meta?.title) {
        document.title = to.meta.title;
    }

    if (to.meta?.requiresAuth && !auth.isAuthenticated) {
        return next({
            name: 'LoginReg',
            query: { redirect: to.fullPath },
        });
    }

    if (to.meta?.guestOnly && auth.isAuthenticated) {
        return next({ name: 'Home' });
    }

    return next();
});

export default router;
