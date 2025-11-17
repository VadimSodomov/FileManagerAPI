<template>
  <div class="page-content">
    <div class="page-head">
      <div class="page-head_title">cloudy</div>
      <div class="page-head_btns">
        <slot name="buttons"/>
        <QBtn
          rounded
          icon="logout"
          @click="handleLogout"
        />
      </div>
    </div>

    <div class="page-body">
      <slot name="content"/>
    </div>

  </div>
</template>

<script setup>
import { useQuasar, Dialog  } from 'quasar'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const $q = useQuasar()
const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  Dialog.create({
    title: 'Подтверждение',
    message: 'Вы действительно хотите выйти?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      authStore.clearSession()

      $q.notify({
        message: 'Вы успешно вышли из системы',
        type: 'positive',
        position: 'top-right',
        timeout: 3000,
        icon: 'check'
      })

      await router.push('/login-reg')
    } catch (error) {
      console.error('Ошибка при выходе:', error)
      $q.notify({
        message: 'Ошибка при выходе из системы',
        type: 'negative',
        position: 'top-right',
        timeout: 4000,
        icon: 'warning'
      })
    }
  })
}
</script>

<style scoped>
.page-content {
  padding: 20px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  max-height: calc(100% - 40px);
}

.page-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
  border-bottom: 1px solid var(--color-lighter);
  padding-bottom: 20px;
  margin-bottom: 20px;
}

.page-head_title {
  font-weight: 800;
  font-size: 30px;
  background: var(--gradient-cloudy);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  text-align: center;
  letter-spacing: 3px;
  text-shadow: 0 4px 15px rgba(74, 123, 255, 0.2);
}

.page-head_btns{
  display: flex;
  align-items: center;
  gap: 10px;
}
</style>