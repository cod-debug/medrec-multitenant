<template>
  <q-card class="full-height">
    <q-card-section>
      <div class="text-h6 q-mb-md">
        <q-icon name="download" class="q-mr-sm" />
        Backup Actions
      </div>
      
      <div class="q-gutter-md">
        <!-- Download Backup Section -->
        <div class="row q-gutter-sm">
          <div class="col-12">
            <q-banner class="bg-blue-1 text-blue-9" rounded>
              <template v-slot:avatar>
                <q-icon name="info" color="blue" />
              </template>
              <div class="text-body2">
                Download a complete SQL backup of your database. This includes all table structures and data.
              </div>
            </q-banner>
          </div>
          
          <div class="col-12">
            <q-btn 
              color="primary" 
              icon="upload" 
              label="Upload Backup to Google Drive"
              :loading="loading"
              @click="handleDownload"
              class="full-width"
            />
          </div>
        </div>

        <q-separator />
        
        <!-- Backup Features -->
        <div class="text-subtitle2 q-mb-sm">
          <q-icon name="check_circle" class="q-mr-sm" />
          Backup Features
        </div>
        
        <div class="q-gutter-sm">
          <q-item>
            <q-item-section avatar>
              <q-icon name="verified" color="green" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Complete Structure</q-item-label>
              <q-item-label caption>Includes all table definitions, indexes, and constraints</q-item-label>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section avatar>
              <q-icon name="dataset" color="blue" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Full Data Export</q-item-label>
              <q-item-label caption>All record data with proper escaping and formatting</q-item-label>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section avatar>
              <q-icon name="security" color="orange" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Foreign Key Safe</q-item-label>
              <q-item-label caption>Handles foreign key constraints properly during restore</q-item-label>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section avatar>
              <q-icon name="schedule" color="purple" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Timestamped</q-item-label>
              <q-item-label caption>Each backup file includes creation timestamp</q-item-label>
            </q-item-section>
          </q-item>
        </div>
      </div>
    </q-card-section>
  </q-card>
</template>

<script setup>

const emit = defineEmits(['download', 'refresh-info']);

const handleDownload = () => {
  emit('download');
};
</script>

<style scoped>
.full-height {
  height: 100%;
}
</style>