<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-5">
                <q-form @submit="getList(true)" class="q-pb-md">
                    <q-input label="Search Patient" outlined dense v-model="list_params.keyword">
                        <template v-slot:append>
                            <q-icon name="search" @click="getList(true)" class="cursor-pointer" />
                        </template>
                    </q-input>
                </q-form>
            </div>
        </div>
        <div v-if="is_submitted">
            <q-table :columns="columns"
                :rows="patient_list"
                row-key="full_name"
                :rows-per-page-options="[list_params.per_page]"
                :hide-bottom="patient_list.length > 0"
                :loading="fetching_patients"
            >
                <template v-slot:body-cell-full_name="props">
                    <q-td :props="props">
                        <strong>
                            <q-item clickable class="text-primary flex items-center" style="font-size: 11pt;" flat :to="{ name: 'patient-history', params: { patient_id: props.row.id } }">
                                {{ props.row.full_name }}
                                <q-tooltip class="bg-secondary text-white">View medical record of  <strong>{{ props.row.full_name }}</strong>.</q-tooltip>
                            </q-item>
                        </strong>
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <div class="q-gutter-sm">
                            <q-btn color="primary" icon="edit" size="sm" bordered round @click="() => handleOpenUpdateModal(props.row)">
                                <q-tooltip class="bg-grey-14 text-white">Update Patient</q-tooltip>
                            </q-btn>
                        </div>
                    </q-td>
                </template>
                <template v-slot:body-cell-birthday="props">
                    <q-td :props="props">
                        {{ helpers.formatDate(props.row.date_of_birth) }} ({{ helpers.getAge(props.row.date_of_birth) }} years old)
                    </q-td>
                </template>
                <template v-slot:no-data>
                    No returning patients found.
                </template>
            </q-table>
            <div v-if="patient_list.length > 0 && get_returning_patient_list_request && get_returning_patient_list_request.data && get_returning_patient_list_request.data.last_page > 0"
                class="flex justify-between q-my-md">
                <q-pagination v-model="list_params.page"
                    :max="get_returning_patient_list_request.data.last_page"
                    direction-links
                    :max-pages="6"
                    flat
                    color="grey" 
                    active-color="indigo-14"
                    @update:model-value="getList()"
                />
                <span>
                    Showing {{ get_returning_patient_list_request.data.from }} to {{ get_returning_patient_list_request.data.to }} of {{ get_returning_patient_list_request.data.total }}
                </span>
            </div>
        </div>
        <update-patient-modal v-model="open_update_modal" :selected_patient="selected_patient" />
    </div>
</template>
<script setup>
import { ref, getCurrentInstance, onMounted, onBeforeUnmount } from 'vue';
import { usePatientStore } from 'src/stores/patient';
import { storeToRefs } from 'pinia';
import UpdatePatientModal from 'src/components/modals/UpdatePatientModal.vue';
import { eventBus } from 'src/utils/event-bus';

const open_update_modal = ref(false);
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const patient_store = usePatientStore();
const { fetchAllPatients } = patient_store;
const { get_returning_patient_list_request } = storeToRefs(patient_store);
const fetching_patients = ref(false);
const patient_list = ref([]);
const is_submitted = ref(false);
const selected_patient = ref({});

const columns = [
    { name: 'full_name', label: 'Patient\'s name', field: 'full_name', align: 'left' },
    { name: 'visits_count', label: 'Number of Visits', field: 'visits_count', align: 'center' },
    { name: 'phone_number', label: 'Contact Number', field: 'phone_number', align: 'left' },
    { name: 'birthday', label: 'Date of Birth', field: 'birthday', align: 'left' },
    { name: 'gender', label: 'Sex', field: 'gender', align: 'left' },
    { name: 'actions', label: 'Actions', field: 'actions', align: 'right' },
];
const list_params = ref({
    page: 1,
    per_page: 10,
    keyword: '',
});

function handleOpenUpdateModal(patient) {
    selected_patient.value = patient;
    open_update_modal.value = true;
}

async function getList(reset_page = false) {
    try {
        is_submitted.value = true;
        fetching_patients.value = true;
        if (reset_page) {
            list_params.value.page = 1;
        }
        await fetchAllPatients(list_params.value);
        const response = get_returning_patient_list_request.value;
        if (response?.data && response?.success) {
            patient_list.value = response.data.data;
        } else {
            patient_list.value = [];
        }
    } catch (error) {
        helpers.showDebugMessage(error);
    } finally {
        fetching_patients.value = false;
    }
}


onMounted(() => {
    eventBus.on('updatePatientInfo', (data) => {
        if(data){
            const selected_key = patient_list.value.findIndex(p => p.id == data.id);
            if(selected_key === -1) return;
            patient_list.value[selected_key] = data;
        }
    });
    getList(true);
});

onBeforeUnmount(() => {
    eventBus.off('updatePatientInfo');
});
</script>