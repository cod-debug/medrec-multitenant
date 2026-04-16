<template>
  <div>
    <!-- Zoom Dialog -->
    <q-dialog v-model="zoomOpen" maximized>
      <div class="flex flex-center" style="background-color: rgba(0,0,0, .8);">
        <q-img
          :src="zoomSrc"
          fit="contain"
          style="max-width: 90vw; max-height: 90vh"
          @click="zoomOpen = false"
        />
      </div>
    </q-dialog>
  </div>
</template>
<script setup>
import { ref } from 'vue'

const zoomOpen = ref(false)
const zoomSrc = ref('')

function openZoom(src) {
  zoomSrc.value = src
  zoomOpen.value = true
}

defineExpose({
  openZoom
})
</script>