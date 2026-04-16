<template>
  <q-page class="q-pa-sm">
    <div class="row">
      <div class="col-12 q-pa-sm">
        <q-card>
          <q-card-section>
            <div class="text-h5 q-mb-lg">
              <q-icon name="backup" class="q-mr-sm" />
              Backup Management
            </div>
            <p class="text-grey-7">
              Manage your database backups. View backup information and download the latest database backup.
            </p>
          </q-card-section>
        </q-card>
      </div>
      
      <!-- Backup Information Card -->
      <div class="col-12 col-md-6 q-pa-sm">
        <backup-info-card 
          :loading="loadingInfo"
          :backup-info="backupInfo"
          @refresh="getBackupInformation"
        />
      </div>
      
      <!-- Backup Actions Card -->
      <div class="col-12 col-md-6 q-pa-sm">
        <backup-actions-card
          :loading="loadingDownload"
          @download="downloadBackup"
          @refresh-info="getBackupInformation"
        />
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue';
import { useBackupStore } from 'stores/backup';
import { Notify } from 'quasar';
import BackupInfoCard from 'src/components/backup/BackupInfoCard.vue';
import BackupActionsCard from 'src/components/backup/BackupActionsCard.vue';
import { useSpinner } from 'src/composables/spinner';

const backupStore = useBackupStore();
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;

const loadingInfo = ref(false);
const loadingDownload = ref(false);
const backupInfo = ref(null);
const spinner = useSpinner();

const getBackupInformation = async () => {
  try {
    spinner.show('Loading backup information...');
    loadingInfo.value = true;
    await backupStore.getBackupInfo();
    
    if (backupStore.backup_info_request?.success) {
      backupInfo.value = backupStore.backup_info_request.data;
      Notify.create({
        message: 'Backup information loaded successfully',
        color: 'green',
        position: 'top-right',
        timeout: 2000,
      });
    } else {
      throw new Error(backupStore.backup_info_request?.message || 'Failed to load backup information');
    }
  } catch (error) {
    helpers.showDebugMessage(error);
    Notify.create({
      message: error.message || 'Failed to load backup information',
      color: 'red-14',
      position: 'top-right',
      closeBtn: 'X',
      timeout: 3000,
    });
  } finally {
    loadingInfo.value = false;
    spinner.hide();
  }
};

const downloadBackup = async () => {
  try {
    spinner.show('Downloading backup...');
    loadingDownload.value = true;
    await backupStore.downloadBackup();
    
    if (backupStore.backup_download_request?.success) {
      Notify.create({
        message: backupStore.backup_download_request?.message || 'Backup downloaded successfully',
        color: 'green',
        position: 'top-right',
        timeout: 3000,
        icon: 'download_done',
      });
    } else {
      throw new Error(backupStore.backup_download_request?.message || 'Failed to download backup');
    }
  } catch (error) {
    helpers.showDebugMessage(error);
    Notify.create({
      message: error.message || 'Failed to download backup',
      color: 'red-14',
      position: 'top-right',
      closeBtn: 'X',
      timeout: 3000,
    });
  } finally {
    loadingDownload.value = false;
    spinner.hide();
  }
};

onMounted(() => {
  getBackupInformation();
});
</script>