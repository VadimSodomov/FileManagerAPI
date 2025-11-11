<template>
    <div class="reg-form">
        <QForm @submit.prevent="submit">
            <QInput
                v-model="form.email"
                label="E-mail"
                type="email"
                dense
                outlined
                class="q-mb-sm"
                :rules="[rules.required, rules.email]"
                autocomplete="email"
            />

            <QInput
                v-model="form.password"
                :type="isShowPassword ? 'text' : 'password'"
                label="Пароль"
                dense
                outlined
                class="q-mb-sm"
                :rules="[rules.required, rules.min8Strong]"
                autocomplete="new-password"
            >
                <template #append>
                    <QIcon :name="isShowPassword ? 'visibility_off' : 'visibility'"
                           class="cursor-pointer"
                           @click="isShowPassword =! isShowPassword" />
                </template>
            </QInput>

            <QInput
                v-model="form.password2"
                :type="isShowPassword ? 'text' : 'password'"
                label="Повторите пароль"
                dense
                outlined
                class="q-mb-md"
                :rules="[rules.required, same]"
                autocomplete="new-password"
            />

            <div class="row justify-end">
                <QBtn
                    type="submit"
                    color="primary"
                    label="Зарегистрироваться"
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
    password2: '',
});

const isShowPassword = ref(false);

const rules = {
    required: v => !!String(v || '').trim() || 'Обязательное поле',
    email: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v || '') || 'Некорректный e-mail',
    min8Strong: v => (v?.length >= 5 && /[A-Za-z]/.test(v) && /\d/.test(v)) || 'Мин. 5 символов, буквы и цифры'
};

const same = v => v === form.value.password || 'Пароли не совпадают';

const submit = () => emit('submit', {
    email: form.value.email,
    password: form.value.password,
});
</script>

<style scoped>
.reg-form {
    min-width: 100%;
}
</style>
