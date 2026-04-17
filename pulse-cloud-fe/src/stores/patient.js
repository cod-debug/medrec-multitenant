import { defineStore, acceptHMRUpdate } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL;

export const usePatientStore = defineStore('usePatientStore', {
    state: () => ({
        add_patient_and_queue_request: null,
        get_todays_patient_list_request: null,
        get_returning_patient_list_request: null,
        add_patient_to_queue_request: null,
        remove_queue_request: null,
        get_visit_information_request: null,
        save_visit_information_request: null,
        remove_visit_information_request: null,
        update_visit_status_request: null,
        get_patient_information_request: null,
        get_patient_history_request: null,
        get_visit_list_request: null,
        get_daily_revenue_request: null,
        get_daily_revenue_report_request: null,
        upload_laboratory_image_request: null,
        delete_laboratory_image_request: null,
        upload_findings_image_request: null,
        delete_findings_image_request: null,
        get_filtered_patients_request: null,
        update_patient_request: null,
    }),
    getters: {},
    actions: {
        async saveAndQueueRequest(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/patient/add-and-queue`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.add_patient_and_queue_request = data
                }
            } catch (error) {
                this.add_patient_and_queue_request = error.response.data
            }
        },
        async addPatientToQueue(payload){
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/patient/queue`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.add_patient_to_queue_request = data
                }
            } catch (error) {
                this.add_patient_to_queue_request = error.response.data
            }
        },
        async fetchTodaysPatientList(params) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/patient/today`,
                    params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_todays_patient_list_request = data
                }
            } catch (error) {
                this.get_todays_patient_list_request = error.response.data
            }
        },
        async fetchAllPatients(params) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/patient/paginated`,
                    params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_returning_patient_list_request = data
                }
            } catch (error) {
                this.get_returning_patient_list_request = error.response.data
            }
        },
        async fetchFilteredPatients(params) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/patient/all`,
                    params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_filtered_patients_request = data
                }
            } catch (error) {
                this.get_filtered_patients_request = error.response.data;
            }
        },
        async removeQueue(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/patient/remove-queue`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.remove_queue_request = data
                }
            } catch (error) {
                this.remove_queue_request = error.response.data
            }
        },
        async viewVisitInformation(params){
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/visit/${params.visit_id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_visit_information_request = data
                }
            } catch (error) {
                this.get_visit_information_request = error.response.data
            }
        },
        async saveVisitInformation(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/visit/save/${payload.visit_id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.save_visit_information_request = data
                }
            } catch (error) {
                this.save_visit_information_request = error.response.data
            }
        },
        async removeVisitInformation(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/visit/remove-info/${payload.id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.remove_visit_information_request = data
                }
            } catch (error) {
                this.remove_visit_information_request = error.response.data
            }
        },
        async updateVisitStatus(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/visit/status/${payload.visit_id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.update_visit_status_request = data
                }
            } catch (error) {
                this.update_visit_status_request = error.response.data
            }
        },
        async getPatientInformation(id){
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/patient/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_patient_information_request = data
                }
            } catch (error) {
                this.get_patient_information_request = error.response.data
            }
        },
        async getPatientVisitHistory(params){
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/patient/history/${params.patient_id}`,
                    params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_patient_history_request = data
                }
            } catch (error) {
                this.get_patient_history_request = error.response.data
            }
        },
        async getVisitList(params){
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/visit/list`,
                    params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_visit_list_request = data
                }
            } catch (error) {
                this.get_visit_list_request = error.response.data
            }
        },
        async getDailyRevenue(payload){
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    params: payload,
                    url: `${API_BASE_URL}/api/v1/visit/revenue`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_daily_revenue_request = data
                }
            } catch (error) {
                this.get_daily_revenue_request = error.response.data
            }
        },
        async getDailyRevenueReports(params){
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/visit/revenue/daily`,
                    params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.get_daily_revenue_report_request = data
                }
            } catch (error) {
                this.get_daily_revenue_report_request = error.response.data
            }
        },
        async uploadLaboratoryImage(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/visit/upload-laboratory-image/${payload.visit_id}`,
                    data: payload.formData,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                        'Content-Type': 'multipart/form-data',
                    },
                })

                if ([200, 201].includes(status)) {
                    this.upload_laboratory_image_request = data
                }
            } catch (error) {
                this.upload_laboratory_image_request = error.response.data
            }
        },
        async deleteLaboratoryImage(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/visit/delete-laboratory-image/${payload.id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                        'Content-Type': 'multipart/form-data',
                    },
                })

                if ([200, 201].includes(status)) {
                    this.delete_laboratory_image_request = data
                }
            } catch (error) {
                this.delete_laboratory_image_request = error.response.data
            }
        },
        async uploadFindingsImage(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/visit/upload-findings-image/${payload.visit_id}`,
                    data: payload.formData,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                        'Content-Type': 'multipart/form-data',
                    },
                })

                if ([200, 201].includes(status)) {
                    this.upload_findings_image_request = data
                }
            } catch (error) {
                this.upload_findings_image_request = error.response.data
            }
        },
        async deleteFindingsImage(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/visit/delete-findings-image/${payload.id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                        'Content-Type': 'multipart/form-data',
                    },
                })

                if ([200, 201].includes(status)) {
                    this.delete_findings_image_request = data
                }
            } catch (error) {
                this.delete_findings_image_request = error.response.data
            }
        },
        async updatePatient(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/patient/update/${payload.id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.update_patient_request = data
                }
            } catch (error) {
                this.update_patient_request = error.response.data
            }
        },
    },
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(usePatientStore, import.meta.hot))
}
