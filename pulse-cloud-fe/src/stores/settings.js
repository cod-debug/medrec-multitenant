import { defineStore, acceptHMRUpdate } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL;

export const useSettingsStore = defineStore('useSettingsStore', {
    state: () => ({
        get_settings_request: null,
        update_settings_request: null,
    }),
    getters: {},
    actions: {
        async getSettings() {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/settings/get`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_settings_request = data
                }
            } catch (error) {
                this.get_settings_request = error.response.data
            }
        },
        async updateSettings(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/settings/update`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })
                
                if ([200, 201].includes(status)) {
                    this.update_settings_request = data
                }
            } catch (error) {
                this.update_settings_request = error.response.data
            }
        }
    },
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useSettingsStore, import.meta.hot))
}
