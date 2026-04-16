<template>
    <div>
        <div class="row">
            <div class="col-sm-11 col-md-6">
                <q-form @submit="getList(true)" class="q-pb-md">
                    <q-input label="Search Patient" outlined dense v-model="list_params.keyword">
                        <template v-slot:append>
                            <q-icon name="search" @click="getList(true)" class="cursor-pointer" />
                        </template>
                    </q-input>
                </q-form>
            </div>
            <div class="col-sm-1 q-px-sm">
                <q-btn icon="refresh" @click="getList(true)" color="secondary">
                    <q-tooltip class="">Refresh list of patient's for today.</q-tooltip>
                </q-btn>
            </div>
        </div>
        <div>
            <q-table :columns="columns"
                :rows="patient_list"
                row-key="name"
                :rows-per-page-options="[list_params.per_page]"
                :hide-bottom="patient_list.length > 0"
                :loading="fetching_patients"
            >
                <template v-slot:body-cell-full_name="props">
                    <q-td :props="props">
                        <strong><q-item clickable class="text-primary flex items-center" style="font-size: 11pt;" :to="{ name: 'patients-today-view', params: { patient_id: props.row.id, visit_id: props.row.visit_id } }" flat>{{ props.row.full_name }}</q-item></strong>
                    </q-td>
                </template>
                <template v-slot:body-cell-visit_status="props">
                    <q-td :props="props" @click="openVisitDetails(props.row)" style="cursor: pointer;">
                        <q-badge :color="helpers.formatVisitStatus(props.row.visit_status).color" class="text-white">
                            {{ helpers.formatVisitStatus(props.row.visit_status).label }}
                        </q-badge>
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <q-btn color="red-14" icon="delete" size="sm" bordered round @click="openRemoveConfirmationModal(props.row)" v-if="props.row.visit_status === 'queued'">
                            <q-tooltip class="bg-grey-14 text-white">Remove {{ props.row.full_name }} from Queue</q-tooltip>
                        </q-btn>
                        <span v-else class="text-grey-7">No available actions</span>
                    </q-td>
                </template>
                <template v-slot:no-data>
                    No patients for today.
                </template>
            </q-table>
        </div>
        <div v-if="patient_list.length > 0 && get_todays_patient_list_request && get_todays_patient_list_request.data && get_todays_patient_list_request.data.last_page > 0"
            class="flex justify-between q-my-md">
            <q-pagination v-model="list_params.page"
                :max="get_todays_patient_list_request.data.last_page"
                direction-links
                :max-pages="6"
                flat
                color="grey" 
                active-color="indigo-14"
                @update:model-value="getList()"
            />
            <span>
                Showing {{ get_todays_patient_list_request.data.from }} to {{ get_todays_patient_list_request.data.to }} of {{ get_todays_patient_list_request.data.total }}
            </span>
        </div>
        <confirmation-modal
            v-model="open_confirmation_modal"
            title="Confirm Removal"
            :message="`Are you sure you want to remove <strong>${selected_visit?.full_name}</strong> from the queue?`"
            confirm_label="Yes, Remove"
            cancel_label="Cancel"
            @confirm="handleConfirmRemoveFromQueue"
        />
    </div>
</template>
<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue';
import { usePatientStore } from 'src/stores/patient';
import { storeToRefs } from 'pinia';
import { eventBus } from 'src/utils/event-bus';
import ConfirmationModal from '../modals/ConfirmationModal.vue';
import { useSpinner } from 'src/composables/spinner';
import { Notify } from 'quasar';
import { useRouter } from 'vue-router';

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const patient_store = usePatientStore();
const { fetchTodaysPatientList, removeQueue } = patient_store;
const { get_todays_patient_list_request, remove_queue_request } = storeToRefs(patient_store);
const fetching_patients = ref(false);
const patient_list = ref([]);
const spinner = useSpinner();
const router = useRouter();

const columns = [
    { name: 'full_name', label: 'Patient\'s name', field: 'full_name', align: 'left' },
    { name: 'phone_number', label: 'Contact Number', field: 'phone_number', align: 'left' },
    { name: 'visit_status', label: 'Status', field: 'visit_status', align: 'left' },
    { name: 'actions', label: 'Actions', field: 'actions', align: 'left' },
];

const list_params = ref({
    keyword: '',
    page: 1,
    per_page: 10,
});

const open_confirmation_modal = ref(false);
const selected_visit = ref(null);

function openRemoveConfirmationModal(visit) {
    // Store the patient ID in a ref or state variable to use when confirming removal
    selected_visit.value = visit;
    open_confirmation_modal.value = true;
}

async function handleConfirmRemoveFromQueue() {
    // Implement the logic to remove the patient from the queue here
    // You can use the patient ID stored in a ref when the delete button is clicked
    try {
        const visit = selected_visit.value;

        const payload = {
            visit_id: visit.visit_id,
        };

        spinner.show(`Removing ${visit.full_name} from queue...`);
        await removeQueue(payload);
        const response = remove_queue_request.value;
        if (response?.success) {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            getList(true);
            open_confirmation_modal.value = false;
            selected_visit.value = null;
        } else {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
            patient_list.value = [];
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'An error occurred while removing patient from queue.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }

}

async function getList(is_search = false) {
    try {
        if(is_search) {
            list_params.value.page = 1; // reset to first page when searching
        }

        const payload = {
            keyword: list_params.value.keyword,
            page: list_params.value.page,
            per_page: list_params.value.per_page,
        };

        fetching_patients.value = true;
        await fetchTodaysPatientList(payload);
        const response = get_todays_patient_list_request.value;
        if (response?.success) {
            patient_list.value = response.data.data;
        } else {
            patient_list.value = [];
        }
    } catch (error) {
        console.error('Error fetching today\'s patient list:', error);
    } finally {
        fetching_patients.value = false;
    }
}

function openVisitDetails(data) {
    router.push({ name: 'patients-today-view', params: { patient_id: data.id, visit_id: data.visit_id } });
}

onMounted(() => {
    getList();
});

eventBus.on('refreshTable', () => {
    getList(true);
});
</script>