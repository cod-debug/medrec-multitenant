<template>
    <div>
        <q-form @submit.prevent="getList(true)" class="q-pb-md">
            <div class="row">
                <div class="col-12 col-md-5">
                    <q-input label="Search Patient" outlined v-model="list_params.keyword">
                        <template v-slot:append>
                            <q-icon name="search" @click="getList(true)" class="cursor-pointer" />
                        </template>
                    </q-input>
                </div>
                <div class="col-12 col-md-5 q-px-md">
                    <date-range-picker labelDateFrom="Date From *" labelDateTo="Date To *"
                        v-model:from="list_params.date_start" v-model:to="list_params.date_end" outlined stack-label />
                </div>
                <div class="col-12 col-md-2">
                    <div class="flex" style="height: 56px;">
                        <q-btn type="submit" label="Apply Filter" color="primary" class="q-px-lg" @click="getList(true)" />
                    </div>
                </div>
            </div>
        </q-form>
        
        <q-table :columns="columns"
            :rows="admission_list"
            row-key="id"
            :rows-per-page-options="[list_params.per_page]" 
            :hide-bottom="admission_list.length > 0"
            :loading="is_loading">
            <template v-slot:body-cell-timestamp="props">
                <q-td :props="props">
                    {{ helpers.formatDateTime(props.row.created_at) }}
                </q-td>
            </template>
            <template v-slot:body-cell-full_name="props">
                <q-td :props="props">
                    <strong>
                        <q-item clickable class="text-primary flex items-center" style="font-size: 11pt;" flat
                            :to="{ name: 'inpatient-admission-details', params: { admission_id: props.row.id, patient_id: props.row.patient.id } }">
                            {{ props.row.patient.full_name }}
                            <q-tooltip class="bg-secondary text-white">View medical record of <strong>{{
                                    props.row.patient.full_name }}</strong>.</q-tooltip>
                        </q-item>
                    </strong>
                </q-td>
            </template>
            <template v-slot:body-cell-age="props">
                <q-td :props="props">
                        <span v-if="props.row.patient.age">{{ props.row.patient.age }} years old</span>
                        <span v-else>--</span>
                </q-td>
            </template>
            <template v-slot:body-cell-gender="props">
                <q-td :props="props">
                    {{ props.row.patient.gender }}
                </q-td>
            </template>
            <template v-slot:body-cell-admission_date="props">
                <q-td :props="props">
                    <strong>
                        <q-item clickable class="text-primary flex items-center" style="font-size: 11pt;" flat
                            :to="{ name: 'inpatient-admission-details', params: { admission_id: props.row.id, patient_id: props.row.patient.id } }">
                            {{ helpers.formatDate(props.row.admission_date) }}
                            <q-tooltip class="bg-secondary text-white">View medical record of <strong>{{
                                    props.row.patient.full_name }}</strong>.</q-tooltip>
                        </q-item>
                    </strong>
                </q-td>
            </template>
            <template v-slot:body-cell-discharge_date="props">
                <q-td :props="props">
                    <strong>
                        <q-item v-if="props.row.discharge_date" clickable class="text-primary flex items-center" style="font-size: 11pt;" flat
                            :to="{ name: 'inpatient-admission-details', params: { admission_id: props.row.id, patient_id: props.row.patient.id } }">
                            {{ helpers.formatDate(props.row.discharge_date) }}
                            <q-tooltip class="bg-secondary text-white">View medical record of <strong>{{
                                    props.row.patient.full_name }}</strong>.</q-tooltip>
                        </q-item>
                    </strong>
                </q-td>
            </template>
            <template v-slot:body-cell-admission_status="props">
                <q-td :props="props">
                    <q-badge :color="helpers.formatAdmissionStatus(props.row.admission_status).color" class="text-white">
                        {{ helpers.formatAdmissionStatus(props.row.admission_status).label }}
                    </q-badge>
                </q-td>
            </template>
            <template v-slot:no-data>
                No patient admissions found.
            </template>
        </q-table>
        <div v-if="admission_list.length > 0 && admission_list_request && admission_list_request.data && admission_list_request.data.last_page > 0"
            class="flex justify-between q-my-md">
            <q-pagination v-model="list_params.page" :max="admission_list_request.data.last_page"
                direction-links :max-pages="6" flat color="grey" active-color="indigo-14"
                @update:model-value="getList()" />
            <span>
                Showing {{ admission_list_request.data.from }} to {{
                admission_list_request.data.to }}
                of {{ admission_list_request.data.total }}
            </span>
        </div>
    </div>
</template>

<script setup>
import DateRangePicker from 'src/components/reusables/DateRangePicker.vue';
import { ref, getCurrentInstance, onMounted } from 'vue';
import { useAdmissionStore } from 'src/stores/admissions';
import { storeToRefs } from 'pinia';

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const admission_store = useAdmissionStore();
const { fetchAdmissionList } = admission_store;
const { admission_list_request } = storeToRefs(admission_store);

const list_params = ref({
    keyword: '',
    page: 1,
    per_page: 10,
    date_start: null,
    date_end: null,
});

const is_loading = ref(false);
const admission_list = ref([]);

const columns = [
    { name: 'full_name', label: 'Patient\'s name', field: 'full_name', align: 'left' },
    { name: 'age', label: 'Age', field: 'age', align: 'left' },
    { name: 'gender', label: 'Sex', field: 'gender', align: 'left' },
    { name: 'admission_date', label: 'Admission Date', field: 'admission_date', align: 'left' },
    { name: 'discharge_date', label: 'Discharge Date', field: 'discharge_date', align: 'left' },
    { name: 'admission_status', label: 'Status', field: 'admission_status', align: 'left' },
];

async function getList(is_search = false) {
    try {
        is_loading.value = true;
        if (is_search) {
            list_params.value.page = 1;
        }
        await fetchAdmissionList(list_params.value);
        const response = admission_list_request.value;
        if (response?.data && response?.success) {
            admission_list.value = response.data.data;
        } else {
            admission_list.value = [];
        }
    } catch (error) {
        helpers.showDebugMessage(error);
        admission_list.value = [];
    } finally {
        is_loading.value = false;
    }
}

onMounted(() => {
    getList(true);
});
</script>