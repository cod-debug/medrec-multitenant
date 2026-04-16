<template>
    <div class="q-pa-md">
        <div class="row q-mb-xl">
            <div class="col-12 col-md-6 q-pa-xs" v-if="admission_details_local">
                <div>
                    <q-input 
                        label="Admission / Referral Date" 
                        outlined 
                        v-model="admission_details_local.admission_date"
                        type="date"
                    />
                </div>
            </div>
            <div class="col-12 col-md-6 q-pa-xs" v-if="admission_details_local">
                <div>
                    <q-input 
                        label="Room Number" 
                        outlined 
                        v-model="admission_details_local.room_number" 
                    />
                </div>
            </div>
            <div class="col-12 col-md-6 q-pa-xs">
                <q-card>
                    <q-card-section class="bg-blue-4 text-white">
                        <div class="text-subtitle2">Diagnosis</div>
                    </q-card-section>
                    <q-card-section>
                        <div class="q-pa-sm bg-grey-2 q-mb-md" style="border-radius: 10px; position: relative;" v-for="(diagnosis, key) in diagnoses" :key="`diagnosis-${diagnosis.id}`">
                            <div class="absolute flex items-center justify-center" style="top: -.5rem; right: -.5rem; z-index: 100;" v-if="!is_readonly">
                                <q-btn color="red-14" icon="delete" size="sm" round
                                    @click="removeInformation('diagnosis', key, diagnosis.id)" v-if="diagnoses.length > 1">
                                    <q-tooltip class="bg-grey-14 text-white">Remove Diagnosis</q-tooltip>
                                </q-btn>
                            </div>
                            <q-input type="textarea" outlined label="Diagnosis" v-model="diagnosis.diagnosis" class="q-mb-sm" :disable="is_readonly" />
                            <q-input type="textarea" outlined label="Remarks" v-model="diagnosis.remarks" :disable="is_readonly" />
                        </div>
                        <div v-if="!is_readonly">
                            <q-btn color="primary" label="Add Diagnosis" icon="add" size="sm" outline @click="addInformation('diagnosis')" />
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-xs">
                <q-card>
                    <q-card-section class="bg-blue-4 text-white">
                        <div class="text-subtitle2">Treatments</div>
                    </q-card-section>
                    <q-card-section>
                        <div class="q-pa-sm q-mb-md relative" style="border-radius: 10px; position: relative;" v-for="(treatment, key) in treatments" :key="`treatment-${treatment.id}`">
                            <div class="absolute flex items-center justify-center" style="top: -.5rem; right: -.5rem; z-index: 100;" v-if="!is_readonly">
                                <q-btn color="red-14" icon="delete" size="sm" round
                                    @click="removeInformation('treatment', key, treatment.id)" v-if="treatments.length > 1">
                                    <q-tooltip class="bg-grey-14 text-white">Remove Treatment</q-tooltip>
                                </q-btn>
                            </div>
                            <q-input type="textarea" outlined label="Treatments" v-model="treatment.treatment" :disable="is_readonly" />
                        </div>
                        <div v-if="!is_readonly">
                            <q-btn color="primary" label="Add Treatment" icon="add" size="sm" outline @click="addInformation('treatment')" />
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-xs">
                <q-card>
                    <q-card-section class="bg-blue-4 text-white">
                        <div class="text-subtitle2">Referred By</div>
                    </q-card-section>
                    <q-card-section>
                        <div class="patient-info-card q-pa-sm">
                            <q-input type="text" outlined label="Referred By" v-model="referred_by" :disable="is_readonly" />
                            <q-input type="text" outlined label="Hospital" v-model="referred_by_hospital" :disable="is_readonly" class="q-mt-md" />
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-xs">
                <q-card>
                    <q-card-section class="bg-blue-4 text-white">
                        <div class="text-subtitle2">Admission Fee</div>
                    </q-card-section>
                    <q-card-section>
                        <div class="patient-info-card q-pa-sm">
                            <q-input type="text" outlined label="Admission / Referral Fee" v-model="admission_fee" :disable="is_readonly" />
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>

        <div class="fixed q-pa-md bg-white shadow-3" style="width: 100%; z-index: 20; bottom: 0;right: 0;">
            <div class="flex justify-end" style="gap: .5rem;" v-if="level_of_authorization == 1 && !is_readonly">
                <q-btn type="button" color="grey-10" label="Back to List" icon="arrow_left" outline :to="{ name: 'inpatient-admissions' }" />
                <q-btn type="button" color="secondary" label="Save and Continue Editing" icon="save" @click="handleSubmit()" />
                <q-btn type="button" color="warning" label="Discharge" icon="how_to_reg" @click="open_confirmation_modal = true" />
            </div>
            <div v-if="admission_details.admission_status === 'discharged'" class="text-red-10 text-right">
                This patient was discharged on <strong>{{ helpers.formatDateTime(admission_details.discharge_date) }}</strong>.
            </div>
        </div>
        <confirmation-modal
            v-model="open_confirmation_modal"
            title="Confirm Discharge"
            :message="`Are you sure you want to discharge <strong>${props.admission_details?.patient?.full_name}</strong>?`"
            confirm_label="Yes, Discharge"
            cancel_label="Cancel"
            @confirm="handleDischarge(false)"
        />
    </div>
</template>
<script setup>
import ConfirmationModal from 'components/modals/ConfirmationModal.vue';
import { computed, ref, onMounted, getCurrentInstance } from 'vue';
import { useAuthStore } from 'stores/auth';
import { useAdmissionStore } from 'stores/admissions';
import { useRouter } from 'vue-router';
import { useSpinner } from 'src/composables/spinner';
import { Notify } from 'quasar';
import moment from 'moment';

const spinner = useSpinner();
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const auth_store = useAuthStore();
const admission_store = useAdmissionStore();
const router = useRouter();

const props = defineProps({
    admission_details: {
        type: Object,
        default: null
    }
});

const level_of_authorization = computed(() => auth_store.getLevelOfAuthorization);
const is_readonly = computed(() => props.admission_details?.admission_status === 'discharged' || level_of_authorization.value !== 1);

const open_confirmation_modal = ref(false);

const referred_by = ref('');
const referred_by_hospital = ref('');
const admission_fee = ref('');

const diagnoses = ref([
    {
        id: null,
        diagnosis: '',
        remarks: ''
    }
]);

const treatments = ref([
    {
        id: null,
        treatment: '',
        remarks: ''
    }
]);

const admission_details_local = ref(null);

async function handleSubmit(){
    try {
        const payload = {
            diagnoses: diagnoses.value,
            treatments: treatments.value,
            referred_by: referred_by.value,
            referred_by_hospital: referred_by_hospital.value,
            admission_fee: admission_fee.value,
            admission_date: admission_details_local.value.admission_date,
            room_number: admission_details_local.value.room_number,
        }
        spinner.show('Saving admission details...');
        await admission_store.updateAdmission(props.admission_details.id, payload);
        
        if (admission_store.update_admission_request?.success) {
            // Refresh the admission details
            await admission_store.getAdmissionDetails({ id: props.admission_details.id });
            if (admission_store.admission_details_request?.success) {
                // Update local data with the response
                const updatedAdmission = admission_store.admission_details_request.data;
                diagnoses.value = updatedAdmission.diagnoses.length > 0 ? updatedAdmission.diagnoses : [{ id: null, diagnosis: '', remarks: '' }];
                treatments.value = updatedAdmission.treatments.length > 0 ? updatedAdmission.treatments : [{ id: null, treatment: '', remarks: '' }];
                referred_by.value = updatedAdmission.referred_by || '';
                referred_by_hospital.value = updatedAdmission.referred_by_hospital || '';
                admission_fee.value = updatedAdmission.admission_fee ? Math.round(updatedAdmission.admission_fee) : '';
            }
            Notify.create({
                message: admission_store.update_admission_request?.message || 'Admission details saved successfully!',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'positive',
            });
        } else {
            Notify.create({
                message: admission_store.update_admission_request?.message || 'Failed to save admission details.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14',
            });
            const message = admission_store.update_admission_request?.message || 'Failed to save admission details.';
            helpers.showDebugMessage(message);
        }
    } catch (error) {
        const message = error.response?.data?.message || 'An error occurred while saving the information.';
        helpers.showDebugMessage(message);
        Notify.create({
            type: 'negative',
            message: message
        });
    }finally {
        spinner.hide();
    }
}

function addInformation(type) {
    if (type === 'diagnosis') {
        diagnoses.value.push({ id: null, diagnosis: '', remarks: '' });
    } else if (type === 'treatment') {
        treatments.value.push({ id: null, treatment: '', remarks: '' });
    }
}

async function handleDischarge(){
    try {
        spinner.show('Processing discharge...');
        const payload = {
            admission_status: 'discharged',
            discharge_date: new Date().toISOString().split('T')[0], // Current date
            diagnoses: diagnoses.value,
            treatments: treatments.value,
            referred_by: referred_by.value,
            referred_by_hospital: referred_by_hospital.value,
            admission_fee: admission_fee.value,
            admission_date: admission_details_local.value.admission_date,
            room_number: admission_details_local.value.room_number,
        }
        
        await admission_store.updateAdmission(props.admission_details.id, payload);
        
        if (admission_store.update_admission_request?.success) {
            open_confirmation_modal.value = false;
            // Navigate back to inpatient admissions list
            router.push({ name: 'inpatient-admissions' });
        } else {
            const message = admission_store.update_admission_request?.message || 'Failed to discharge patient.';
            helpers.showDebugMessage(message);
        }
    } catch (error) {
        const message = error.response?.data?.message || 'An error occurred while discharging the patient.';
        helpers.showDebugMessage(message);
    } finally {
        spinner.hide();
    }
}

async function removeInformation(type, index, id) {
    if (type === 'diagnosis') {
        if (id) {
            await handleDeleteDiagnosis(id);
        }
        diagnoses.value.splice(index, 1);
    } else if (type === 'treatment') {
        if (id) {
            await handleDeleteTreatment(id);
        }
        treatments.value.splice(index, 1);
    }
}

async function handleDeleteTreatment(id) {
    try {
        spinner.show('Removing treatment...');
        await admission_store.removeTreatment(id);
        
        if (admission_store.delete_treatment_request?.success) {
            helpers.showDebugMessage('Treatment removed successfully!');
        } else {
            const message = admission_store.delete_treatment_request?.message || 'Failed to remove treatment.';
            helpers.showDebugMessage(message);
        }
    } catch (error) {
        const message = error.response?.data?.message || 'An error occurred while removing the treatment.';
        helpers.showDebugMessage(message);
    } finally {
        spinner.hide();
    }
}

async function handleDeleteDiagnosis(id) {
    try {
        spinner.show('Removing diagnosis...');
        await admission_store.removeDiagnosis(id);
        
        if (admission_store.delete_diagnosis_request?.success) {
            helpers.showDebugMessage('Diagnosis removed successfully!');
        } else {
            const message = admission_store.delete_diagnosis_request?.message || 'Failed to remove diagnosis.';
            helpers.showDebugMessage(message);
        }
    } catch (error) {
        const message = error.response?.data?.message || 'An error occurred while removing the diagnosis.';
        helpers.showDebugMessage(message);
    } finally {
        spinner.hide();
    }
}

onMounted(() => {
    if (props.admission_details) {
        diagnoses.value = props.admission_details.diagnoses.length > 0 ? props.admission_details.diagnoses : diagnoses.value;
        treatments.value = props.admission_details.treatments.length > 0 ? props.admission_details.treatments : treatments.value;
        referred_by.value = props.admission_details.referred_by || '';
        referred_by_hospital.value = props.admission_details.referred_by_hospital || '';
        admission_fee.value = props.admission_details.admission_fee ? Math.round(props.admission_details.admission_fee) : '';
        admission_details_local.value = props.admission_details;
        admission_details_local.value.admission_date = moment(props.admission_details.admission_date).format('YYYY-MM-DD'); 
    }
});
</script>