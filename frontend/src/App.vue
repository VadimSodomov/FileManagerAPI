<template>
    <QLayout class="shadow-2 rounded-borders" view="hHh LpR lFf">
        <QPageContainer :class="{'page-container': $route.path !== '/login-reg'}">
            <QPage class="page-layout">
                <RouterView/>
            </QPage>
        </QPageContainer>
        <Loader />
    </QLayout>
</template>

<script setup>
import Loader from "@/components/Loader.vue";
</script>

<style scoped>
.page-container {
  padding: 20px;
  background: var(--bg-yellow-light);
}

.page-layout {
  padding: 0;
  background: white;
  border-radius: 20px;
  height: calc(100vh - 40px);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
</style>