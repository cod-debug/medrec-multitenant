import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL;

export const useInpatientStore = defineStore('useInpatientStore', {
    state: () => ({
        search_inpatient_request: null,
        add_and_admit_request: null,
        get_inpatient_list_request: null,
        update_inpatient_request: null,
        view_patient_records_request: null,
    }),
    getters: {},
    actions: {
        async searchInpatient(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/inpatient/search`,
                    params: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.search_inpatient_request = data
                }
            } catch (error) {
                this.search_inpatient_request = error.response.data
            }
        },

        async savePatientInfoAndAdmit(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/inpatient/create`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.add_and_admit_request = data
                }
            } catch (error) {
                this.add_and_admit_request = error.response.data
            }
        },

        async fetchAllInpatients(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/inpatient/list`,
                    params: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_inpatient_list_request = data
                }
            } catch (error) {
                this.get_inpatient_list_request = error.response.data
            }
        },

        async updateInpatient(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/inpatient/update/${payload.id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.update_inpatient_request = data
                }
            } catch (error) {
                this.update_inpatient_request = error.response.data
            }
        },

        async viewPatientRecords(payload) {
            const auth_store = useAuthStore();
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/inpatient/records/${payload.patient_id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.view_patient_records_request = data
                }
            } catch (error) {
                this.view_patient_records_request = error.response.data
            }
        }
    }
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useInpatientStore, import.meta.hot))
}
