<template>
  <div
    v-if="isLoading"
    class="fullscreen flex flex-center fixed-top-left z-max"
    style="background: rgba(255, 255, 255, 0.8);"
  >
    <div class="text-center">
      <QSpinner
        color="primary"
        size="3em"
      />
      <div class="q-mt-md text-h6">{{ loaderText }}</div>
    </div>
  </div>
</template>

<script setup>
import { useLoader } from '@/plugins/loader'

const { isLoading, loaderText } = useLoader()
</script>