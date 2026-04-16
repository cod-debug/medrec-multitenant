import { defineStore, acceptHMRUpdate } from 'pinia'
import axios from 'axios'
import { loadFromTempStorage, clearTempStorage } from 'src/utils/storePersistence'

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL;

export const useAuthStore = defineStore('useAuthStore', {
    state: () => ({
        token: null,
        level_of_authorization: null,
        full_name: null,
        login_request: null,
        logout_request: null,
        change_password_request: null,
        forgot_password_request: null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
        getToken: (state) => state.token,
        getLevelOfAuthorization: (state) => state.level_of_authorization,
        getFullName: (state) => state.full_name,
    },
    actions: {
        // Set user data (stored only in-memory in Pinia store)
        setUserData(data) {
            this.token = data.token
            this.level_of_authorization = data.level_of_authorization
            this.full_name = data.full_name
        },
        // Clear user data
        clearUserData() {
            this.token = null
            this.level_of_authorization = null
            this.full_name = null
        },
        // Load state from temp storage (only used during page refresh)
        initializeStore() {
            // Try loading from temp storage (page refresh scenario)
            const userData = loadFromTempStorage()
            if (userData) {
                this.token = userData.token
                this.level_of_authorization = userData.level_of_authorization
                this.full_name = userData.full_name
                // Clear temp storage after loading
                clearTempStorage()
            }
        },
        async login(payload) {
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/login`,
                    data: payload,
                })

                if ([200, 201].includes(status)) {
                    this.login_request = data
                }
            } catch (error) {
                this.login_request = error.response.data
            }
        },
        async logout() {
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/user/logout`,
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.logout_request = data
                }
            } catch (error) {
                this.logout_request = error.response.data
            }
        },
        async changePassword(payload) {
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/user/update-password`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.change_password_request = data
                }
            } catch (error) {
                this.change_password_request = error.response.data
            }
        },
        async forgotPassword(payload) {
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/forgot-password`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.forgot_password_request = data
                }
            } catch (error) {
                this.forgot_password_request = error.response.data
            }
        },
    },
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useAuthStore, import.meta.hot))
}
