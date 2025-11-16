import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import router from '@/router';
import api from '../../index.js';

export const useAuthStore = defineStore('authentication', () => {
    const user = ref(null);
    const token = ref(null);

    const isAuthenticated = computed(() => !!user.value && !!token.value);

    const getDataFromStorage = () => {
        token.value = localStorage.getItem('jwt_token');
        try {
            user.value = JSON.parse(localStorage.getItem('auth_user') || 'null');
        } catch {
            user.value = null;
        }
    };

    const setSession = (jwt, userData) => {
        token.value = jwt;
        user.value = userData;

        localStorage.setItem('jwt_token', jwt);
        localStorage.setItem('auth_user', JSON.stringify(userData));
    };

    const clearSession = () => {
        token.value = null;
        user.value = null;

        localStorage.removeItem('jwt_token');
        localStorage.removeItem('auth_user');
    };

    const getAxiosMessage = (err) =>
        err?.response?.data?.message ||
        err?.response?.data?.error ||
        err?.message ||
        'Произошла ошибка';

    const login = async (credentials) => {
        try {
            const { data } = await api.post('/api/login', credentials);
            setSession(data.jwt_token, data.user);
            await router.push('/');
        } catch (err) {
            throw new Error(getAxiosMessage(err) || 'Неверный e-mail или пароль');
        }
    };

    const register = async (payload) => {
        try {
            const { data } = await api.post('/api/register', payload);
            setSession(data.jwt_token, data.user);
            await router.push('/');
        } catch (err) {
            throw new Error(getAxiosMessage(err) || 'Ошибка регистрации');
        }
    };

    return {
        user,
        token,
        isAuthenticated,

        getDataFromStorage,
        setSession,
        clearSession,
        login,
        register,
    };
});