<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-12">
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
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <q-btn color="primary" icon="queue" size="sm" bordered round @click="addToQueue(props.row.id)">
                            <q-tooltip class="bg-grey-14 text-white">Add to Queue</q-tooltip>
                        </q-btn>
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
        <q-btn outline label="Cancel" color="grey-8" icon="cancel" @click="handleCancel" class="q-mt-md" />
    </div>
</template>
<script setup>
import { ref, getCurrentInstance } from 'vue';
import { usePatientStore } from 'src/stores/patient';
import { storeToRefs } from 'pinia';
import { eventBus } from 'src/utils/event-bus';
import { Notify } from 'quasar';
import { useSpinner } from 'src/composables/spinner';

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const spinner = useSpinner();

const patient_store = usePatientStore();
const { fetchAllPatients, addPatientToQueue } = patient_store;
const { get_returning_patient_list_request, add_patient_to_queue_request } = storeToRefs(patient_store);
const fetching_patients = ref(false);
const patient_list = ref([]);
const is_submitted = ref(false);
const columns = [
    { name: 'full_name', label: 'Patient\'s name', field: 'full_name', align: 'left' },
    { name: 'phone_number', label: 'Contact Number', field: 'phone_number', align: 'left' },
    { name: 'actions', label: 'Actions', field: 'actions', align: 'right' },
];
const list_params = ref({
    page: 1,
    per_page: 10,
    keyword: '',
});

const emit = defineEmits(['cancel', 'success']);

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
        console.error('Error fetching patient list:', error);
    } finally {
        fetching_patients.value = false;
    }
}

async function addToQueue(patient_id) {
    try {
        spinner.show('Adding patient to queue...');
        await addPatientToQueue({ patient_id: patient_id });
        const response = add_patient_to_queue_request.value;
        if (response?.success) {
            // Optionally, show a success message or update UI
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            eventBus.emit('refreshTable'); 
            handleSuccess();
        } else {
            console.error('Failed to add patient to queue');
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'An error occurred while adding patient to queue.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}

function handleSuccess(){
    // Emit an event to notify parent component of successful addition to queue
    emit('success');
}

function handleCancel() {
    emit('cancel');
}
</script>