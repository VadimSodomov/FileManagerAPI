import { ref } from 'vue'

const isLoading = ref(false)
const loaderText = ref('Загрузка данных...')

export function useLoader() {
  const show = (text = 'Загрузка данных...') => {
    loaderText.value = text
    isLoading.value = true
  }

  const hide = () => {
    isLoading.value = false
  }

  return {
    isLoading,
    loaderText,
    show,
    hide
  }
}