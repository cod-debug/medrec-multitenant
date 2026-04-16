<template>
    <div class="camera-container q-px-md q-mb-md">
        <video v-if="isStreaming" ref="videoPlayer" autoplay playsinline width="100%" height="480"></video>
        <div class="camera-lists" v-if="availableCameras.length > 1">
            <div>
              <div class="q-mb-sm">Choose Camera</div>
              <q-option-group
                v-model="selectedCamera"
                :options="availableCameras"
                type="radio"
                @update:model-value="startCamera"
              />
            </div>
        </div>
        <div class="controls">
            <template v-if="!isStreaming">
                <q-btn v-if="!showFileUpload" @click="startCamera" type="button" label="Take Photo" icon="camera_alt" color="primary" />
                <q-btn v-if="showFileUpload" @click="triggerFileInput" type="button" label="Upload File" icon="upload_file" color="secondary" outline />
            </template>
            <template v-else>
                 <q-btn @click="capturePhoto" type="button" label="Capture Photo" icon="camera_alt" color="primary" />
                 <q-btn @click="cancelCamera" type="button" outline label="Cancel" icon="close" color="secondary" />
            </template>
        </div>

        <input 
            ref="fileInput" 
            type="file" 
            accept="image/*" 
            style="display: none;" 
            @change="handleFileSelect"
        />

        <canvas ref="canvas" width="640" height="480" style="display: none;"></canvas>
    </div>
</template>

<script setup>
import { ref, onUnmounted, computed } from 'vue';
import { useQuasar } from 'quasar';

const $q = useQuasar();
const videoPlayer = ref(null);
const canvas = ref(null);
const fileInput = ref(null);
const imgSrc = ref(null);
const isStreaming = ref(false);
const emit = defineEmits(['capture']);
const selectedCamera = ref(null);
const availableCameras = ref([]);

// Get list of available cameras
navigator.mediaDevices.enumerateDevices()
  .then(devices => {
    availableCameras.value = devices
      .filter(device => device.kind === 'videoinput')
      .map((device, index) => ({ label: device.label || `Camera ${index + 1}`, value: device.deviceId }));
    
    if (availableCameras.value.length > 0) {
      selectedCamera.value = availableCameras.value[0].value;
    }
  })
  .catch(err => {
    console.error("Error enumerating devices: ", err);
    alert("Could not access camera devices. Please ensure you've granted permissions.");
  });

// Show file upload option on screens smaller than or equal to landscape tablet (1024px)
const showFileUpload = computed(() => {
  return $q.screen.width <= 1024;
});

// add stopping the stream when component unmounts
onUnmounted(() => {
  if (videoPlayer.value && videoPlayer.value.srcObject) {
    const stream = videoPlayer.value.srcObject;
    const tracks = stream.getTracks();
    tracks.forEach(track => track.stop());
  }
});

// Access the user's camera
const startCamera = async () => {
  try {
    // Stop existing stream if any
    if (videoPlayer.value && videoPlayer.value.srcObject) {
      const tracks = videoPlayer.value.srcObject.getTracks();
      tracks.forEach(track => track.stop());
    }
    
    isStreaming.value = true;
    const stream = await navigator.mediaDevices.getUserMedia({ 
      video: { deviceId: selectedCamera.value ? { exact: selectedCamera.value } : true, width: 640, height: 480 }, 
      audio: false 
    });
    videoPlayer.value.srcObject = stream;
  } catch (err) {
    console.error("Error accessing camera: ", err);
    alert("Could not access camera. Please ensure you've granted permissions.");
    isStreaming.value = false;
  }
};

// Draw the current video frame to the canvas
const capturePhoto = () => {
isStreaming.value = false;
  const context = canvas.value.getContext('2d');
  // Draw the video frame onto the canvas
  context.drawImage(videoPlayer.value, 0, 0, 640, 480);
  
  // Convert canvas to a Base64 URL
  imgSrc.value = canvas.value.toDataURL('image/png');
  
  // upload image value to backend here using imgSrc.value
  emit('capture', imgSrc.value);
  videoPlayer.value.srcObject.getTracks().forEach(track => track.stop());
};

// Cancel the camera stream
function cancelCamera (){
    isStreaming.value = false;
    if (videoPlayer.value && videoPlayer.value.srcObject) {
        const stream = videoPlayer.value.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
    }
};

// Trigger file input click
const triggerFileInput = () => {
  fileInput.value.click();
};

// Handle file selection
const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file && file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = (e) => {
      imgSrc.value = e.target.result;
      emit('capture', imgSrc.value);
    };
    reader.readAsDataURL(file);
  } else {
    alert('Please select a valid image file.');
  }
  // Reset file input
  event.target.value = '';
};
</script>

<style scoped>
.camera-container { display: flex; flex-direction: column; gap: 1rem; }
video { border: 2px solid #333; border-radius: 8px; background: #000; }
img { max-width: 100%; border: 2px solid #42b883; }
.controls {
    display: flex;
    gap: 1rem;
}
.result {
    width: 50%;
}
</style>