<template>
    <q-dialog v-model="open_modal" persistent>
        <q-card :style="{ width: props.size, maxWidth: '90%' }">
            <q-card-section class="bg-primary text-white">
                <div class="text-h6">Update Patient Information</div>
            </q-card-section>
            <q-card-section>
                <q-form @submit.prevent="handleUpdate">
                    <q-input v-model="form.full_name" label="Full Name" outlined :rules="[rules.required]" />
                    <q-input v-model="form.address" label="Address" outlined :rules="[rules.required]" />
                    <q-input v-model="form.date_of_birth" label="Date of Birth" type="date" outlined :rules="[rules.required]" />
                    <q-select v-model="form.gender" label="Sex" outlined :rules="[rules.selectRequired]" :options="['Male', 'Female']" />
                    <q-input v-model="form.phone_number" label="Contact Number" outlined :rules="[rules.required]" />
                </q-form>
            </q-card-section>
            <q-card-actions>
                <q-btn label="Update" color="primary" @click="handleUpdate" icon="check_circle" />
                <q-btn label="Close" color="grey-9" outline @click="open_modal = false" icon="cancel" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
<script setup>
import { computed, ref, getCurrentInstance, watch } from 'vue';
import { usePatientStore } from 'src/stores/patient';
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useSpinner } from 'src/composables/spinner';
import moment from 'moment';
import { eventBus } from 'src/utils/event-bus';

const spinner = useSpinner();

const patient_store = usePatientStore();
const { updatePatient } = patient_store;
const { update_patient_request } = storeToRefs(patient_store);

const { proxy } = getCurrentInstance();
const rules = proxy.$rules;
const helpers = proxy.$helpers;

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: '620px'
    },
    selected_patient: {
        type: Object,
        required: true
    }
});

const form = ref({});

// methods
async function handleUpdate() {
    try {
        spinner.show('Updating patient information...');
        await updatePatient(form.value);
        const response = update_patient_request.value;
        if (response?.success) {
            open_modal.value = false;
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            
            emit('update:modelValue', false);
            eventBus.emit('updatePatientInfo', response.data.patient);
        } else {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (error) {
        helpers.showDebugMessage(error);
        Notify.create({
            message: 'An error occurred while updating patient information.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}


const open_modal = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

watch(() => props.selected_patient, (new_patient) => {
    if (new_patient) {
        form.value = {
            id: new_patient.id,
            full_name: new_patient.full_name,
            address: new_patient.address,
            date_of_birth: moment(new_patient.date_of_birth).format('YYYY-MM-DD'),
            gender: new_patient.gender,
            phone_number: new_patient.phone_number
        };
    }
});
</script>