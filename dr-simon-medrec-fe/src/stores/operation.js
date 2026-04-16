import { defineStore, acceptHMRUpdate } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL;

export const useOperationStore = defineStore('useOperationStore', {
    state: () => ({
        save_operation_schedule_request: null,
        get_schedules_for_month_request: null,
        update_operation_schedule_request: null,
        delete_operation_schedule_request: null,
    }),
    getters: {},
    actions: {
        async saveOperationSchedule(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/operation/create`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.save_operation_schedule_request = data
                }
            } catch (error) {
                this.save_operation_schedule_request = error.response.data
            }
        },
        async updateOperationSchedule(id, payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/operation/update/${id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.update_operation_schedule_request = data
                }
            } catch (error) {
                this.update_operation_schedule_request = error.response.data
            }
        },
        async deleteOperationSchedule(id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'DELETE',
                    url: `${API_BASE_URL}/api/v1/operation/delete/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.delete_operation_schedule_request = data
                }
            } catch (error) {
                this.delete_operation_schedule_request = error.response.data
            }
        },
        async getSchedulesForMonth(params) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/operation/monthly-schedule`,
                    params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })
                if ([200, 201].includes(status)) {
                    this.get_schedules_for_month_request = data
                }
            } catch (error) {
                this.get_schedules_for_month_request = error.response.data
            }
        }
    }
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useOperationStore, import.meta.hot))
}
