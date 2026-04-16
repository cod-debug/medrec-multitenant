import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL;

export const useMedicineStore = defineStore('useMedicineStore', {
    state: () => ({
        // Generic Medicine
        save_generic_name_request: null,
        generic_names_list_request: null,
        generic_name_get_request: null,
        generic_name_update_request: null,
        generic_name_delete_request: null,
        
        // Medicine Brand
        medicine_brand_create_request: null,
        medicine_brand_list_request: null,
        medicine_brand_get_request: null,
        medicine_brand_update_request: null,
        medicine_brand_delete_request: null,
        
        // Medicine Dose
        medicine_dose_create_request: null,
        medicine_dose_list_request: null,
        medicine_dose_get_request: null,
        medicine_dose_update_request: null,
        medicine_dose_delete_request: null,
        
        // Medicine Preparation
        medicine_preparation_create_request: null,
        medicine_preparation_list_request: null,
        medicine_preparation_get_request: null,
        medicine_preparation_update_request: null,
        medicine_preparation_delete_request: null,
    }),
    getters: {},
    actions: {
        // ======================
        // Generic Medicine Actions
        // ======================
        async saveMedicineGenericName(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/medicine/save-generic-name`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.save_generic_name_request = data
                }
            } catch (error) {
                this.save_generic_name_request = error.response.data
            }
        },

        async listGenericMedicineNames(params = {}) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/medicine/generic-names`,
                    params: params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.generic_names_list_request = data
                }
            } catch (error) {
                this.generic_names_list_request = error.response.data
            }
        },

        async getGenericMedicineName(id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/medicine/generic-name/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.generic_name_get_request = data
                }
            } catch (error) {
                this.generic_name_get_request = error.response.data
            }
        },

        async updateGenericMedicineName(id, payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/medicine/generic-name/${id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.generic_name_update_request = data
                }
            } catch (error) {
                this.generic_name_update_request = error.response.data
            }
        },

        async deleteGenericMedicineName(id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'DELETE',
                    url: `${API_BASE_URL}/api/v1/medicine/generic-name/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.generic_name_delete_request = data
                }
            } catch (error) {
                this.generic_name_delete_request = error.response.data
            }
        },

        // ======================
        // Medicine Brand Actions
        // ======================
        async createMedicineBrand(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/medicine-brand/create`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_brand_create_request = data
                }
            } catch (error) {
                this.medicine_brand_create_request = error.response.data
            }
        },

        async listMedicineBrands(params = {}) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/medicine-brand/list`,
                    params: params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_brand_list_request = data
                }
            } catch (error) {
                this.medicine_brand_list_request = error.response.data
            }
        },

        async getMedicineBrand(id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/medicine-brand/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_brand_get_request = data
                }
            } catch (error) {
                this.medicine_brand_get_request = error.response.data
            }
        },

        async updateMedicineBrand(id, payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/medicine-brand/update/${id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_brand_update_request = data
                }
            } catch (error) {
                this.medicine_brand_update_request = error.response.data
            }
        },

        async deleteMedicineBrand(id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'DELETE',
                    url: `${API_BASE_URL}/api/v1/medicine-brand/delete/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_brand_delete_request = data
                }
            } catch (error) {
                this.medicine_brand_delete_request = error.response.data
            }
        },

        // ======================
        // Medicine Dose Actions
        // ======================
        async createMedicineDose(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/medicine-dose/create`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_dose_create_request = data
                }
            } catch (error) {
                this.medicine_dose_create_request = error.response.data
            }
        },

        async listMedicineDoses(params = {}) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/medicine-dose/list`,
                    params: params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_dose_list_request = data
                }
            } catch (error) {
                this.medicine_dose_list_request = error.response.data
            }
        },

        async getMedicineDose(id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/medicine-dose/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_dose_get_request = data
                }
            } catch (error) {
                this.medicine_dose_get_request = error.response.data
            }
        },

        async updateMedicineDose(id, payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/medicine-dose/update/${id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_dose_update_request = data
                }
            } catch (error) {
                this.medicine_dose_update_request = error.response.data
            }
        },

        async deleteMedicineDose(id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'DELETE',
                    url: `${API_BASE_URL}/api/v1/medicine-dose/delete/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_dose_delete_request = data
                }
            } catch (error) {
                this.medicine_dose_delete_request = error.response.data
            }
        },

        // ======================
        // Medicine Preparation Actions
        // ======================
        async createMedicinePreparation(payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'POST',
                    url: `${API_BASE_URL}/api/v1/medicine-preparation/create`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_preparation_create_request = data
                }
            } catch (error) {
                this.medicine_preparation_create_request = error.response.data
            }
        },

        async listMedicinePreparations(params = {}) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/medicine-preparation/list`,
                    params: params,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_preparation_list_request = data
                }
            } catch (error) {
                this.medicine_preparation_list_request = error.response.data
            }
        },

        async getMedicinePreparation(id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'GET',
                    url: `${API_BASE_URL}/api/v1/medicine-preparation/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_preparation_get_request = data
                }
            } catch (error) {
                this.medicine_preparation_get_request = error.response.data
            }
        },

        async updateMedicinePreparation(id, payload) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'PUT',
                    url: `${API_BASE_URL}/api/v1/medicine-preparation/update/${id}`,
                    data: payload,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_preparation_update_request = data
                }
            } catch (error) {
                this.medicine_preparation_update_request = error.response.data
            }
        },

        async deleteMedicinePreparation(id) {
            const auth_store = useAuthStore()
            try {
                const { data, status } = await axios({
                    method: 'DELETE',
                    url: `${API_BASE_URL}/api/v1/medicine-preparation/delete/${id}`,
                    headers: {
                        Authorization: `Bearer ${auth_store.getToken}`,
                    },
                })

                if ([200, 201].includes(status)) {
                    this.medicine_preparation_delete_request = data
                }
            } catch (error) {
                this.medicine_preparation_delete_request = error.response.data
            }
        },
    }
})

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useMedicineStore, import.meta.hot))
}
