<template>
    <div class="auth-form">
        <QForm @submit.prevent="submit">
            <QInput
                v-model="form.email"
                label="E-mail"
                type="email"
                autocomplete="email"
                dense
                outlined
                class="q-mb-md"
                :rules="[rules.required, rules.email]"
            />

            <QInput
                v-model="form.password"
                :type="isShowPassword ? 'text' : 'password'"
                label="Пароль"
                autocomplete="current-password"
                dense
                outlined
                class="q-mb-sm"
                :rules="[rules.required, rules.min6]"
            >
                <template #append>
                    <QIcon :name="isShowPassword ? 'visibility_off' : 'visibility'"
                           class="cursor-pointer"
                           @click="isShowPassword =! isShowPassword" />
                </template>
            </QInput>

            <div class="row items-center q-gutter-sm q-mt-sm">
                <QCheckbox v-model="form.remember"
                           label="Запомнить меня"
                           dense />

                <QSpace />

                <QBtn type="submit"
                      color="primary"
                      label="Войти"
                      :loading="isLoading" />
            </div>
        </QForm>
    </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    isLoading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submit']);

const form = ref({
    email: '',
    password: '',
    remember: true,
});

const isShowPassword = ref(false);

const rules = {
    required: v => !!String(v || '').trim() || 'Обязательное поле',
    email: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v || '') || 'Некорректный e-mail',
    min6: v => (v?.length >= 5) || 'Не менее 5 символов',
};

const submit = () => emit('submit', { ...form.value });
</script>

<style scoped>
.auth-form {
    min-width: 100%;
}
</style>
