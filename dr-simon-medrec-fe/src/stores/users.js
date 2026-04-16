import { defineStore, acceptHMRUpdate } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL;

export const useUsersStore = defineStore('useUsersStore', {
    state: () => ({
        register_user_request: null,
        user_list_request: null,
        archive_user_request: null,
        unarchive_user_request: null,
        delete_user_request: null,
        update_info_request: null,
        get_current_user_request: null,
        get_officemate_request: null,
    }),
    getters: {},
    actions: {
        async registerUser(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/user/register`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.register_user_request = data
                }
            } catch (error) {
                this.register_user_request = error.response.data
            }
        },
        async getUserListRequest(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/user/list`,
                    params: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.user_list_request = data;
                } else {
                    this.user_list_request = data;
                }
            } catch (error) {
                this.user_list_request = error.response.data;
            }
        },
        async archiveUserRequest(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/user/archive/${payload.id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.archive_user_request = data;
                } else {
                    this.archive_user_request = data;
                }
            } catch (error) {
                this.archive_user_request = error.response.data;
            }
        },
        async unarchiveUserRequest(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/user/unarchive/${payload.id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.unarchive_user_request = data;
                } else {
                    this.unarchive_user_request = data;
                }
            } catch (error) {
                this.unarchive_user_request = error.response.data;
            }
        },
        async deleteUserRequest(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/user/delete/${payload.params.id}`,
                    data: payload.data,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.delete_user_request = data;
                } else {
                    this.delete_user_request = data;
                }
            } catch (error) {
                this.delete_user_request = error.response.data;
            }
        },
        async getOfficemateList(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/user/officemate/list/${payload.id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_officemate_request = data;
                } else {
                    this.get_officemate_request = data;
                }
            } catch (error) {
                this.get_officemate_request = error.response.data;
            }
        },
        async updateProfileRequest(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/user/update-info`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.update_info_request = data;
                } else {
                    this.update_info_request = data;
                }
            } catch (error) {
                this.update_info_request = error.response.data;
            }
        },
        async getCurrentUserRequest(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/user/current-user`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_current_user_request = data;
                } else {
                    this.get_current_user_request = data;
                }
            } catch (error) {
                this.get_current_user_request = error.response.data;
            }
        },
    },
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useUsersStore, import.meta.hot))
}
