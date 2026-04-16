<template>
  <q-card class="full-height">
    <q-card-section>
      <div class="text-h6 q-mb-md">
        <q-icon name="info" class="q-mr-sm" />
        Database Information
      </div>
      
      <div v-if="loading" class="text-center q-py-lg">
        <q-spinner-grid color="primary" size="50px" />
        <div class="text-grey-6 q-mt-md">Loading backup information...</div>
      </div>
      
      <div v-else-if="backupInfo" class="q-gutter-sm">
        <!-- Database Overview -->
        <q-item>
          <q-item-section avatar>
            <q-avatar color="green" text-color="white" icon="table_chart" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Total Tables</q-item-label>
            <q-item-label caption>{{ backupInfo.total_tables }} tables</q-item-label>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section avatar>
            <q-avatar color="orange" text-color="white" icon="folder_zip" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Estimated Size</q-item-label>
            <q-item-label caption>{{ backupInfo.estimated_size }} ({{ backupInfo.estimated_size_bytes }} bytes)</q-item-label>
          </q-item-section>
        </q-item>

        <q-item>
          <q-item-section avatar>
            <q-avatar color="purple" text-color="white" icon="schedule" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Last Check</q-item-label>
            <q-item-label caption>{{ formatDateTime(backupInfo.backup_timestamp) }}</q-item-label>
          </q-item-section>
        </q-item>

        <q-separator class="q-my-md" />
        
        <!-- Tables Breakdown -->
        <div class="text-subtitle2 q-mb-sm">
          <q-icon name="list" class="q-mr-sm" />
          Tables Breakdown
        </div>
        
        <q-scroll-area style="height: 200px;" class="q-pr-sm">
          <div v-for="table in backupInfo.tables" :key="table.name" class="q-mb-sm">
            <q-item dense>
              <q-item-section avatar>
                <q-icon name="table_view" color="grey-6" />
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ table.name }}</q-item-label>
                <q-item-label caption>
                  {{ formatNumber(table.rows) }} rows • {{ table.size }}
                </q-item-label>
              </q-item-section>
            </q-item>
          </div>
        </q-scroll-area>
      </div>
      
      <div v-else class="text-center q-py-lg">
        <q-icon name="error_outline" size="48px" color="grey-5" />
        <div class="text-grey-6 q-mt-md">No backup information available</div>
      </div>
    </q-card-section>
    
    <q-card-actions align="right">
      <q-btn 
        flat 
        color="primary" 
        icon="refresh" 
        label="Refresh" 
        :loading="loading"
        @click="$emit('refresh')"
      />
    </q-card-actions>
  </q-card>
</template>

<script setup>

const { backupInfo } = defineProps({
  backupInfo: {
    type: Object,
    default: null
  }
});

const formatDateTime = (dateTime) => {
  if (!dateTime) return 'N/A';
  return new Date(dateTime).toLocaleString();
};

const formatNumber = (num) => {
  if (!num) return '0';
  return new Intl.NumberFormat().format(num);
};
</script>

<style scoped>
.full-height {
  height: 100%;
}
</style>