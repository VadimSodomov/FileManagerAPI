<template>
  <PageLayout>
    <template #buttons>
      <QBtn label="Загрузить" rounded color="amber" icon="cloud_upload"/>
      <QBtn label="Создать папку" rounded color="primary" icon="create_new_folder" @click="createFolder"/>
    </template>

    <template #content>
      <QBreadcrumbs class="q-mb-md breadcrumbs" v-if="folderData">
        <QBreadcrumbsEl
          :label="folderData?.root?.name"
          icon="folder"
        />
      </QBreadcrumbs>

      <div v-if="folderData?.content?.length" class="section">
        <div class="text-h6 q-mb-md">Папки</div>
        <div class="folders-grid">
          <Folder
            v-for="folder in folderData.content"
            :key="folder.id"
            :folder="folder"
            @click="openFolder(folder)"
            @contextmenu.prevent="showContextMenu(folder, $event)"
          />
        </div>
      </div>

      <div
        v-if="!folderData?.root?.files?.length && !folderData?.content?.length"
        class="empty-state flex flex-center"
      >
        <div class="text-center">
          <div class="text-h6 text-grey-8">Пусто</div>
        </div>
      </div>

      <QMenu
        v-model="showMenu"
        context-menu
        :offset="[10, 10]"
      >
        <QList style="min-width: 160px">
          <QItem clickable v-close-popup @click="openSelectedFolder">
            <QItemSection avatar>
              <QIcon name="folder_open" />
            </QItemSection>
            <QItemSection>Открыть</QItemSection>
          </QItem>

  <!--        TODO в будущем, когда появятся еще другие папки, в которые присоединится-->
  <!--        пользователь по коду, сделать так, чтобы удалять и переименовывать папку мог только владелец-->

          <QItem clickable v-close-popup @click="renameFolder">
            <QItemSection avatar>
              <QIcon name="edit" />
            </QItemSection>
            <QItemSection>Переименовать</QItemSection>
          </QItem>

          <QSeparator />

          <QItem clickable v-close-popup @click="deleteFolder" class="text-negative">
            <QItemSection avatar>
              <QIcon name="delete" color="negative" />
            </QItemSection>
            <QItemSection>Удалить</QItemSection>
          </QItem>
        </QList>
      </QMenu>
    </template>
  </PageLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { API } from '../../index.js'
import { useQuasar } from "quasar";
import PageLayout from "@/layouts/PageLayout.vue";
import { useLoader } from '@/plugins/loader'
import Folder from "@/components/Folder.vue";

const folderData = ref(null);
const $q = useQuasar();

const loader = useLoader()
const showMenu = ref(false)
const selectedFolder = ref(null)

const createFolder = () => {
  $q.dialog({
    title: 'Создать папку',
    message: 'Введите название папки',
    prompt: {
      model: '',
      type: 'text',
      isValid: (val) => val.length > 0 && val.length <= 50
    },
    cancel: true,
    persistent: true
  }).onOk(async (folderName) => {
    try {
      loader.show('Создание папки')
      await API.createFolder(folderName, folderData.value?.root?.id)
      loader.hide()
      await getData()

      $q.notify({
        message: `Папка "${folderName}" успешно создана`,
        type: 'positive',
        position: 'top-right',
        timeout: 3000,
        icon: 'check'
      })

    } catch (error) {
      console.error('Ошибка при создании папки:', error)
      $q.notify({
        message: 'Ошибка при создании папки',
        type: 'negative',
        position: 'top-right',
        timeout: 4000,
        icon: 'warning'
      })
    }
  })
}

function showContextMenu(folder, event) {
  selectedFolder.value = folder
  showMenu.value = true
}

function renameFolder() {
  if (!selectedFolder.value) return

  const folder = selectedFolder.value
  $q.dialog({
    title: 'Переименовать папку',
    message: 'Введите новое название папки',
    prompt: {
      model: folder.name,
      type: 'text',
      isValid: (val) => val && val.trim().length > 0 && val.length <= 50
    },
    cancel: true,
    persistent: true
  }).onOk(async (newName) => {
    try {
      loader.show('Переименование папки')
      await API.renameFolder(newName, folder.id)
      loader.hide()
      await getData()

      $q.notify({
        message: `Папка переименована в "${newName}"`,
        type: 'positive',
        position: 'top-right',
        timeout: 3000
      })
    } catch (error) {
      console.error('Ошибка при переименовании:', error)
      $q.notify({
        message: 'Ошибка при переименовании папки',
        type: 'negative',
        position: 'top-right',
        timeout: 4000
      })
    }
  })
}

function deleteFolder() {
  if (!selectedFolder.value) return

  const folder = selectedFolder.value
  $q.dialog({
    title: 'Удалить папку',
    message: `Вы уверены, что хотите удалить папку "${folder.name}"?`,
    cancel: true,
    persistent: true,
    ok: {
      label: 'Удалить',
      color: 'negative'
    }
  }).onOk(async () => {
    try {
      loader.show('Удаление папки')
      await API.deleteFolder(folder.id)
      loader.hide()
      await getData()

      $q.notify({
        message: `Папка "${folder.name}" удалена`,
        type: 'positive',
        position: 'top-right',
        timeout: 3000
      })
    } catch (error) {
      console.error('Ошибка при удалении:', error)
      $q.notify({
        message: 'Ошибка при удалении папки',
        type: 'negative',
        position: 'top-right',
        timeout: 4000
      })
    }
  })
}

async function getData() {
  try {
    loader.show('Загрузка файлов...')
    const { data } = await API.getRootFolder();
    folderData.value = data;
  } catch (e) {
    $q.notify({
      message: e.response?.data?.message || 'Ошибка загрузки',
      type: 'negative',
      position: 'top-right',
      timeout: 4000,
      icon: 'warning',
      actions: [{ icon: 'close', color: 'white' }]
    });
  } finally {
    loader.hide()
  }
}

onMounted(async () => {
  await getData()
});
</script>

<style scoped>
.section {
  margin-bottom: 20px;
}

.empty-state {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.folders-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 16px;
  padding: 8px 0;
}

</style>