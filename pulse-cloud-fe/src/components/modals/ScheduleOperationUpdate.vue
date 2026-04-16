<template>
    <q-dialog v-model="open_modal" persistent>
        <q-card :style="{ width: props.size, maxWidth: '90%' }">
            <q-card-section class="bg-primary text-white">
                <div class="text-h6"><q-icon name="attach_money" /> Schedule an Operation</div>
            </q-card-section>
            <q-card-section>
                <div class=" q-pb-md" v-if="props.selected_date">
                    <strong class="text-grey-8">{{ moment(props.selected_date).format('dddd, MMMM DD, YYYY') }}</strong>
                </div>
                <q-form @submit.prevent="handleSubmit">
                    <q-select
                        v-model="selected_patient"
                        label="Select Patient"
                        :options="patients"
                        option-label="full_name"
                        option-value="id"
                        outlined
                        :rules="[rules.requiredSelect]"
                        use-input
                        @filter="handleFilterPatients"
                        input-debounce="800"
                        :loading="is_filtering"
                        :readonly="is_filtering"
                    />
                    <q-input v-model="schedule_form.operation_type" type="text" label="Operation Type" outlined class="q-mb-md" :readonly="is_filtering" />
                    <q-input v-model="schedule_form.diagnosis" type="textarea" label="Diagnosis" outlined class="q-my-md" :readonly="is_filtering" />
                    <q-input v-model="schedule_form.remarks" type="textarea" label="Remarks" outlined class="q-my-md" :readonly="is_filtering" />
                    <q-btn label="Save Schedule" color="primary" @click="handleSubmit" icon="check_circle" :disable="is_filtering" />
                    <q-btn outline label="Cancel" color="grey-8" icon="cancel" @click="open_modal = false" class="q-ml-sm" :disable="is_filtering" />
                </q-form>
            </q-card-section>
        </q-card>
    </q-dialog>
</template>
<script setup>
import { computed, getCurrentInstance, ref, watch } from 'vue';
import moment from 'moment';
import { usePatientStore } from 'src/stores/patient';
import { useOperationStore } from 'src/stores/operation';
import { storeToRefs } from 'pinia';
import { useSpinner } from 'src/composables/spinner';
import { Notify } from 'quasar';
import { eventBus } from 'src/utils/event-bus';

const operation_store = useOperationStore();
const { updateOperationSchedule } = operation_store;
const { update_operation_schedule_request } = storeToRefs(operation_store);

const patient_store = usePatientStore()
const { fetchFilteredPatients } = patient_store;
const { get_filtered_patients_request } = storeToRefs(patient_store);
const { proxy } = getCurrentInstance();
const rules = proxy.$rules;
const helpers = proxy.$helpers;
const spinner = useSpinner();

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: '620px'
    },
    selected_date: {
        type: String,
        default: null
    },
    selected_schedule: {
        type: Object,
        default: null
    },
    selected_schedule_key: {
        type: [String, Number],
        default: null
    }
});

const schedule_form = ref({
    patient_id: null,
    operation_type: '',
    diagnosis: '',
    remarks: '',
});

const selected_patient = ref(null);
const patients = ref([]);
const is_filtering = ref(false);

const emit = defineEmits(['update:modelValue']);

async function handleFilterPatients(val, update) {
    if (val === '') {
        update();
        return;
    }

    update(() => {
        fetchPatients(val);
    });
}

async function fetchPatients(search = '') {
    try {
        is_filtering.value = true;
        const payload = {
            keyword: search,
        }
        await fetchFilteredPatients(payload);
        const response = get_filtered_patients_request.value;
        if (response?.success) {
            patients.value = response.data;
        } else {
            patients.value = [];
        }
    } catch (error) {
        helpers.showErrorMessage('Error fetching patients:', error);
    } finally {
        is_filtering.value = false;
    }
}

async function handleSubmit() {
    // Logic to handle form submission
    try {
        spinner.show('Updating operation schedule...');
        const payload = {
            ...schedule_form.value,
            patient_id: selected_patient.value.id,
            scheduled_at: props.selected_date,
        }
        await updateOperationSchedule(props.selected_schedule.id, payload);
        const response = update_operation_schedule_request.value;
        if (response?.success) {
            open_modal.value = false;
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });

            eventBus.emit('scheduleOperationUpdated', response.data?.operation_schedule);
        } else {
            helpers.showDebugMessage(response.message || 'Failed to update operation schedule.');
            Notify.create({
                message: response.message || 'Failed to update operation schedule.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (error) {
        helpers.showDebugMessage('Error updating operation schedule:', error);
    } finally {
        spinner.hide();
    }
}

const open_modal = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

watch(() => props.selected_schedule, async (new_schedule) => {
    selected_patient.value = null;
    schedule_form.value = {
        patient_id: null,
        operation_type: '',
        diagnosis: '',
        remarks: '',
    };

    if (new_schedule) {
        await fetchPatients(); 
        selected_patient.value = [...patients.value].find(patient => patient.id === new_schedule?.patient_id) || null;
        schedule_form.value = {
            patient_id: new_schedule ? new_schedule.patient_id : null,
            operation_type: new_schedule ? new_schedule.operation_type : '',
            diagnosis: new_schedule ? new_schedule.diagnosis : '',
            remarks: new_schedule ? new_schedule.remarks : '',
        };
    }
});
</script>