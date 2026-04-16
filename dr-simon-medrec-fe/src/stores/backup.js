import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL;

export const useBackupStore = defineStore('useBackupStore', {
    state: () => ({
        backup_info_request: null,
        backup_download_request: null,
    }),
    getters: {},
    actions: {
        async getBackupInfo() {
            const auth_store = useAuthStore();
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/backup/info`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                });

                if ([200, 201].includes(status)) {
                    this.backup_info_request = data;
                }
            } catch (error) {
                this.backup_info_request = error.response?.data || {
                    success: false,
                    message: 'Failed to get backup information'
                };
            }
        },

        async downloadBackup() {
            const auth_store = useAuthStore();
            try {
                const response = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/backup/download`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                    responseType: 'blob/json',
                });

                if (response.status === 200) {
                    // Create blob link to download
                    if(response.headers['content-disposition'] && response.headers['content-disposition'].includes('filename=')) {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        
                        // Extract filename with better header handling
                        let filename = this.extractFilenameFromResponse(response);
                        
                        link.setAttribute('download', filename);
                        document.body.appendChild(link);
                        link.click();
                        
                        // Cleanup
                        link.remove();
                        window.URL.revokeObjectURL(url);
                        
                        this.backup_download_request = {
                            success: true,
                            message: `Backup downloaded successfully: ${filename}`
                        };
                    } else {
                        console.log(response);
                        this.backup_download_request = JSON.parse(response.data) || {
                            success: true,
                            message: 'Backup uploaded to Google Drive successfully'
                        }
                    }
                } else {
                    throw new Error('Download failed');
                }
            } catch (error) {
                this.backup_download_request = error.response?.data || {
                    success: false,
                    message: 'Failed to download backup'
                };
            }
        },

        extractFilenameFromResponse(response) {
            // Generate timestamp-based fallback filename
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 10); // format YYYY-MM-DDTHH-MM-SS
            let defaultFilename = `medrec-${timestamp}.sql`;
            
            // Try different header variations
            const contentDispositionHeaders = [
                response.headers['content-disposition'],
                response.headers['Content-Disposition'],
                response.headers['CONTENT-DISPOSITION']
            ];
            
            for (const header of contentDispositionHeaders) {
                if (header) {
                    // Try multiple filename extraction patterns
                    const patterns = [
                        /filename="([^"]+)"/i,
                        /filename=([^;,\s]+)/i,
                        /filename\*?="?([^"';,\s]+)"?/i
                    ];
                    
                    for (const pattern of patterns) {
                        const match = header.match(pattern);
                        if (match && match[1]) {
                            console.log('Extracted filename:', match[1]);
                            return match[1].trim();
                        }
                    }
                }
            }
            
            return defaultFilename;
        },
        resetRequests() {
            this.backup_info_request = null;
            this.backup_download_request = null;
        }
    }
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useBackupStore, import.meta.hot));
}