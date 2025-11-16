<template>
  <div class="folder-item">
    <div class="folder-icon">
      <QIcon name="folder" size="48px" color="amber-7" />
    </div>
    <div class="folder-name">{{ folder.name }}</div>
    <div class="folder-info">
      <small class="text-grey-6">{{ formatItemCount(folder.files.length) }}</small>
    </div>
  </div>
</template>

<script setup>
defineProps({
  folder:{
    type: Object,
    required: true
  }
})

function formatItemCount(count) {
  if (count === 0) return 'Нет элементов'

  const lastDigit = count % 10
  const lastTwoDigits = count % 100

  if (lastTwoDigits >= 11 && lastTwoDigits <= 19) {
    return `${count} элементов`
  }

  if (lastDigit === 1) {
    return `${count} элемент`
  }

  if (lastDigit >= 2 && lastDigit <= 4) {
    return `${count} элемента`
  }

  return `${count} элементов`
}
</script>

<style scoped>
.folder-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px 16px;
  border-radius: 12px;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.folder-item:hover {
  border-color: var(--q-primary);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px);
}

.folder-icon {
  margin-bottom: 12px;
  position: relative;
}

.folder-icon::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  width: 60%;
  height: 8px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  filter: blur(2px);
}

.folder-name {
  font-weight: 500;
  text-align: center;
  margin-bottom: 4px;
  word-break: break-word;
  max-width: 100%;
}

.folder-info {
  text-align: center;
}

</style>