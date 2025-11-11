import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import router from '@/router';
import axios from 'axios';

const API =  'http://127.0.0.1:8080/';

const api = axios.create({
    baseURL: API,
    withCredentials: false,
});

const setAuthHeader = (token) => {
    if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
    }
    else {
        delete api.defaults.headers.common.Authorization;
    }
};

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
        setAuthHeader(token.value);
    };

    const setSession = (jwt, userData) => {
        token.value = jwt;
        user.value = userData;

        localStorage.setItem('jwt_token', jwt);
        localStorage.setItem('auth_user', JSON.stringify(userData));
        setAuthHeader(jwt);
    };

    const clearSession = () => {
        token.value = null;
        user.value = null;

        localStorage.removeItem('jwt_token');
        localStorage.removeItem('auth_user');
        setAuthHeader(null);
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
