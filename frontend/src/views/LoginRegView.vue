<template>
    <div class="row items-center justify-center vh-100">
        <QCard class="auth-card">
            <QCardSection class="auth-card__section">
                <QTabs class="q-ma-md"
                       v-model="tab"
                       active-color="primary"
                       indicator-color="primary">
                    <QTab name="login">
                        Вход
                    </QTab>

                    <QTab name="reg">
                        Регистрация
                    </QTab>
                </QTabs>

                <QTabPanels v-model="tab"
                            class="auth-card__tab-panels">
                    <QTabPanel name="login"
                               class="q-pa-md panel-content">
                        <AuthComponent :isLoading="isLoading"
                                       @submit="onLogin" />
                    </QTabPanel>

                    <QTabPanel name="reg"
                               class="q-pa-md panel-content">
                        <RegComponent :isLoading="isLoading"
                                      @submit="onReg" />
                    </QTabPanel>
                </QTabPanels>
            </QCardSection>
        </QCard>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useQuasar } from 'quasar';
import { useAuthStore } from '@/stores/authStore.js';
import AuthComponent from "@/components/AuthComponent.vue";
import RegComponent from "@/components/RegComponent.vue";

const tab = ref('login');

const $q = useQuasar();
const auth = useAuthStore();
const isLoading = ref(false);

const onLogin = async (payload) => {
    isLoading.value = true;
    try {
        await auth.login({ email: payload.email, password: payload.password });
    } catch (e) {
        $q.notify({
            message: e.message,
            type: 'negative',
            position: 'top-right',
            timeout: 4000,
            icon: 'warning',
            actions: [{ icon: 'close', color: 'white' }]
        });
    } finally {
        isLoading.value = false;
    }
};

const onReg = async (payload) => {
    isLoading.value = true;
    try {
        await auth.register({ email: payload.email, password: payload.password });
    } catch (e) {
        $q.notify({
            message: e.message,
            type: 'negative',
            position: 'top-right',
            timeout: 4000,
            icon: 'warning',
            actions: [{ icon: 'close', color: 'white' }]
        });
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.vh-100 {
    min-height: 100vh;
}

.auth-card {
    width: 100%;
    max-width: 450px;
    height: 390px;
    border-radius: 10px;
}

.auth-card__section {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.auth-card__tab-panels {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.panel-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
</style>
