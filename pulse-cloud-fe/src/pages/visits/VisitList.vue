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
            :rows="visit_list"
            row-key="id"
            :rows-per-page-options="[list_params.per_page]" 
            :hide-bottom="visit_list.length > 0"
            :loading="is_loading">
            <template v-slot:body-cell-timestamp="props">
                <q-td :props="props">
                    {{ helpers.formatDateTime(props.row.created_at) }}
                </q-td>
            </template>
            <template v-slot:body-cell-visit_date="props">
                <q-td :props="props">
                    <strong>
                        <q-item clickable class="text-primary flex items-center" style="font-size: 11pt;" flat
                            :to="{ name: 'visits-details', params: { visit_id: props.row.id, patient_id: props.row.patient.id } }">
                            {{ helpers.formatDate(props.row.visit_date) }}
                            <q-tooltip class="bg-secondary text-white">View medical record of <strong>{{
                                    props.row.patient.full_name }}</strong>.</q-tooltip>
                        </q-item>
                    </strong>
                </q-td>
            </template>
            <template v-slot:body-cell-full_name="props">
                <q-td :props="props">
                    <strong>
                        <q-item clickable class="text-primary flex items-center" style="font-size: 11pt;" flat
                            :to="{ name: 'visits-details', params: { visit_id: props.row.id, patient_id: props.row.patient.id } }">
                            {{ props.row.patient.full_name }}
                            <q-tooltip class="bg-secondary text-white">View medical record of <strong>{{
                                    props.row.patient.full_name }}</strong>.</q-tooltip>
                        </q-item>
                    </strong>
                </q-td>
            </template>
            <template v-slot:body-cell-phone_number="props">
                <q-td :props="props">
                    {{ props.row.patient.phone_number }}
                </q-td>
            </template>
            <template v-slot:body-cell-birthday="props">
                <q-td :props="props">
                    {{ helpers.formatDate(props.row.patient.date_of_birth) }} ({{ helpers.getAge(props.row.patient.date_of_birth) }}
                    years old)
                </q-td>
            </template>
            <template v-slot:body-cell-gender="props">
                <q-td :props="props">
                    {{ props.row.patient.gender }}
                </q-td>
            </template>
            <template v-slot:no-data>
                No patient visits found.
            </template>
        </q-table>
        <div v-if="visit_list.length > 0 && get_visit_list_request && get_visit_list_request.data && get_visit_list_request.data.last_page > 0"
            class="flex justify-between q-my-md">
            <q-pagination v-model="list_params.page" :max="get_visit_list_request.data.last_page"
                direction-links :max-pages="6" flat color="grey" active-color="indigo-14"
                @update:model-value="getList()" />
            <span>
                Showing {{ get_visit_list_request.data.from }} to {{
                get_visit_list_request.data.to }}
                of {{ get_visit_list_request.data.total }}
            </span>
        </div>
    </div>
</template>
<script setup>
import { getCurrentInstance, onMounted, ref } from 'vue';
import { usePatientStore } from 'src/stores/patient';
import { storeToRefs } from 'pinia';
import DateRangePicker from 'src/components/reusables/DateRangePicker.vue';
import { useAuthStore } from 'src/stores/auth';

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;

const patient_store = usePatientStore();
const { getVisitList } = patient_store;
const { get_visit_list_request } = storeToRefs(patient_store);

const auth_store = useAuthStore();

const list_params = ref({
    keyword: '',
    page: 1,
    per_page: 10,
    date_start: null,
    date_end: null,
    doctor_id: null,
});

const visit_list = ref([]);
const is_loading = ref(false);

const columns = [
    { name: 'timestamp', label: 'Timestamp', field: 'timestamp', align: 'left' },
    { name: 'full_name', label: 'Patient\'s name', field: 'full_name', align: 'left' },
    { name: 'visit_date', label: 'Visit Date', field: 'visit_date', align: 'left' },
    { name: 'phone_number', label: 'Contact Number', field: 'phone_number', align: 'left' },
    { name: 'birthday', label: 'Date of Birth', field: 'birthday', align: 'left' },
    { name: 'gender', label: 'Sex', field: 'gender', align: 'left' },
];

async function getList(is_search = false) {
    try {
        is_loading.value = true;
        if (is_search) {
            list_params.value.page = 1;
        }

        if(auth_store.level_of_authorization === 2){
            list_params.value.doctor_id = auth_store.doctor?.id;
        }

        await getVisitList(list_params.value);
        const response = get_visit_list_request.value;
        if (response?.data && response?.success) {
            visit_list.value = response.data.data;
        } else {
            visit_list.value = [];
        }
    } catch (error) {
        helpers.showDebugMessage(error);
        visit_list.value = [];
    } finally {
        is_loading.value = false;
    }
}

onMounted(() => {
    getList(true);
});
</script>