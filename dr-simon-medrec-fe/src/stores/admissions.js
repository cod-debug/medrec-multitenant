import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL;

export const useAdmissionStore = defineStore('useAdmissionStore', {
    state: () => ({
        admission_list_request: null,
        delete_admission_request: null,
        admission_details_request: null,
        delete_diagnosis_request: null,
        delete_treatment_request: null,
        update_admission_request: null,
        
    }),
    getters: {},
    actions: {
        async fetchAdmissionList(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/admission/list`,
                    params: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.admission_list_request = data
                }
            } catch (error) {
                this.admission_list_request = error.response.data
            }
        },

        async deleteAdmission(admission_id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'DELETE',
                    url: `${API_BASE_URL}/api/v1/admission/delete/${admission_id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.delete_admission_request = data
                }
            } catch (error) {
                this.delete_admission_request = error.response.data
            }
        },

        async getAdmissionDetails(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/admission/details/${payload.id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.admission_details_request = data
                }
            } catch (error) {
                this.admission_details_request = error.response.data
            }
        },

        async removeDiagnosis(diagnosis_id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'DELETE',
                    url: `${API_BASE_URL}/api/v1/admission/remove-diagnosis/${diagnosis_id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.delete_diagnosis_request = data
                }
            } catch (error) {
                this.delete_diagnosis_request = error.response.data
            }
        },
        async removeTreatment(treatment_id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'DELETE',
                    url: `${API_BASE_URL}/api/v1/admission/remove-treatment/${treatment_id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.delete_treatment_request = data
                }
            } catch (error) {
                this.delete_treatment_request = error.response.data
            }
        },

        async updateAdmission(admission_id, payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/admission/update/${admission_id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.update_admission_request = data
                }
            } catch (error) {
                this.update_admission_request = error.response.data
            }
        },
    },
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useAdmissionStore, import.meta.hot))
}
