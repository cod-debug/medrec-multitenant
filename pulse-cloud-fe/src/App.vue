<template>
  <router-view />
</template>
<script setup>
import { onMounted, onBeforeUnmount, getCurrentInstance } from 'vue'
import { useAuthStore } from 'src/stores/auth'
import { saveToTempStorage } from 'src/utils/storePersistence'

const auth_store = useAuthStore()
const { proxy } = getCurrentInstance();
const settings = proxy.$settings;

// save store to temp localStorage before unload
const handleBeforeUnload = () => {
  if (auth_store.isAuthenticated) {
    const userData = {
      token: auth_store.token,
      level_of_authorization: auth_store.level_of_authorization,
      full_name: auth_store.full_name,
    }
    saveToTempStorage(userData)
  }
}

onMounted(() => {
  // Initialize store from temp storage on app load (page refresh scenario only)
  auth_store.initializeStore()
  

  settings.getSettings();
  // Add beforeunload listener
  window.addEventListener('beforeunload', handleBeforeUnload)
})

onBeforeUnmount(() => {
  // Clean up listener
  window.removeEventListener('beforeunload', handleBeforeUnload)
})
</script>