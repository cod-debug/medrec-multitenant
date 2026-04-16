<template>
    <div>
        <div class="row">
            <div class="col-sm-11 col-md-6">
                <q-form @submit="getList(true)" class="q-pb-md">
                    <q-input label="Search Admission" outlined dense v-model="list_params.keyword">
                        <template v-slot:append>
                            <q-icon name="search" @click="getList(true)" class="cursor-pointer" />
                        </template>
                    </q-input>
                </q-form>
            </div>
            <div class="col-sm-1 q-px-sm">
                <q-btn icon="refresh" @click="getList(true)" color="secondary">
                    <q-tooltip>Refresh list of admissions / referrals.</q-tooltip>
                </q-btn>
            </div>
        </div>
        <div>
            <q-table 
                :columns="columns"
                :rows="admission_list"
                row-key="id"
                :rows-per-page-options="[list_params.per_page]"
                :hide-bottom="admission_list.length > 0"
                :loading="fetching_admissions"
            >
                <template v-slot:body-cell-timestamp="props">
                    <q-td :props="props">
                        {{ helpers.formatDate(props.row.admission_date) }}
                    </q-td>
                </template>
                <template v-slot:body-cell-patient_full_name="props">
                    <q-td :props="props">
                        <q-item clickable class="text-primary flex items-center" style="font-size: 11pt;" :to="{name: 'inpatient-admission-details', params: { admission_id: props.row.id }}">
                            <strong class="text-primary">{{ props.row.patient?.full_name || 'N/A' }}</strong>
                        </q-item>
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
                        <div>{{ props.row.patient?.gender || '--' }}</div>
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <q-btn 
                            color="red-14" 
                            icon="delete" 
                            size="sm" 
                            round 
                            @click="openDeleteConfirmationModal(props.row)"
                        >
                            <q-tooltip class="bg-red-14 text-white">Delete Admission</q-tooltip>
                        </q-btn>
                    </q-td>
                </template>
                <template v-slot:no-data>
                    No admissions found.
                </template>
            </q-table>
        </div>
        <div 
            v-if="admission_list.length > 0 && admission_list_request && admission_list_request.data && admission_list_request.data.last_page > 0"
            class="flex justify-between q-my-md"
        >
            <q-pagination 
                v-model="list_params.page"
                :max="admission_list_request.data.last_page"
                direction-links
                :max-pages="6"
                flat
                color="grey" 
                active-color="indigo-14"
                @update:model-value="getList()"
            />
            <span>
                Showing {{ admission_list_request.data.from }} to {{ admission_list_request.data.to }} of {{ admission_list_request.data.total }}
            </span>
        </div>
        <confirmation-modal
            v-model="open_confirmation_modal"
            title="Confirm Deletion"
            :message="`Are you sure you want to delete this admission for <strong>${selected_admission?.patient?.full_name}</strong>?`"
            confirm_label="Yes, Delete"
            cancel_label="Cancel"
            @confirm="handleConfirmDelete"
        />
    </div>
</template>
<script setup>
import { ref, onMounted, getCurrentInstance, watch } from 'vue';
import { useAdmissionStore } from 'src/stores/admissions';
import { storeToRefs } from 'pinia';
import ConfirmationModal from 'src/components/modals/ConfirmationModal.vue';
import { useSpinner } from 'src/composables/spinner';
import { Notify } from 'quasar';

const props = defineProps({
    admission_status: {
        type: String,
        default: 'admitted'
    }
});

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const admission_store = useAdmissionStore();
const { fetchAdmissionList, deleteAdmission } = admission_store;
const { admission_list_request, delete_admission_request } = storeToRefs(admission_store);
const fetching_admissions = ref(false);
const admission_list = ref([]);
const spinner = useSpinner();

const columns = [
    { name: 'patient_full_name', label: 'Patient Name', field: 'patient_full_name', align: 'left' },
    { name: 'age', label: 'Age', field: 'age', align: 'left' },
    { name: 'gender', label: 'Sex', field: 'gender', align: 'left' },
    { name: 'timestamp', label: 'Admission / Referral Date', field: 'timestamp', align: 'left' },
    { name: 'actions', label: 'Actions', field: 'actions', align: 'center' },
];

const list_params = ref({
    keyword: '',
    page: 1,
    per_page: 10,
    status: props.admission_status,
});

const open_confirmation_modal = ref(false);
const selected_admission = ref(null);

function openDeleteConfirmationModal(admission) {
    selected_admission.value = admission;
    open_confirmation_modal.value = true;
}

async function handleConfirmDelete() {
    try {
        const admission = selected_admission.value;

        spinner.show(`Deleting admission for ${admission.patient?.full_name}...`);
        await deleteAdmission(admission.id);
        const response = delete_admission_request.value;
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
            selected_admission.value = null;
        } else {
            Notify.create({
                message: response.message || 'Failed to delete admission',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'An error occurred while deleting admission.',
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
            status: list_params.value.status,
        };

        fetching_admissions.value = true;
        await fetchAdmissionList(payload);
        const response = admission_list_request.value;
        if (response?.success) {
            admission_list.value = response.data.data;
        } else {
            admission_list.value = [];
        }
    } catch (error) {
        console.error('Error fetching admission list:', error);
        admission_list.value = [];
    } finally {
        fetching_admissions.value = false;
    }
}

// Watch for changes in the admission_status prop
watch(() => props.admission_status, (newStatus) => {
    list_params.value.status = newStatus;
    getList(true);
});

onMounted(() => {
    getList();
});
</script>
