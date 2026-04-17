<template>
    <q-form @submit.prevent="submitForm" class="q-gutter-sm">
        <q-input v-model="form.full_name" label="Full Name" outlined :rules="[rules.required]" />
        <q-input v-model="form.address" label="Address" outlined :rules="[rules.required]" />
        <q-input v-model="form.date_of_birth" label="Date of Birth" type="date" outlined :rules="[rules.required]" />
        <q-select v-model="form.gender" label="Sex" outlined :rules="[rules.selectRequired]" :options="['Male', 'Female']" />
        <q-input v-model="form.phone_number" label="Contact Number" outlined :rules="[rules.required]" />
        <div class="flex q-gutter-md q-mb-md">
            <q-btn type="submit" label="Add to Queue" icon="check_circle" color="primary" />
            <q-btn outline label="Cancel" color="grey-8" icon="cancel" @click="handleCancel" />
        </div>
    </q-form>
</template>

<script setup>
import { reactive, getCurrentInstance } from 'vue';
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useSpinner } from 'src/composables/spinner';
import { usePatientStore } from 'src/stores/patient';
import { eventBus } from 'src/utils/event-bus';
import { useAuthStore } from 'src/stores/auth';

const spinner = useSpinner();

const patient_store = usePatientStore();
const { saveAndQueueRequest } = patient_store;
const { add_patient_and_queue_request } = storeToRefs(patient_store);
const auth_store = useAuthStore();

const { proxy } = getCurrentInstance();

const rules = proxy.$rules;
const helpers = proxy.$helpers;

const form = reactive({
    full_name: '',
    address: '',
    date_of_birth: '',
    gender: '',
    phone_number: '',
    doctor_id: auth_store.doctor?.id || null, // associate patient with the currently logged-in doctor, default to null if not available
});

const emit = defineEmits(['cancel', 'success']);

async function submitForm() {
    try {

        spinner.show('Saving patient and adding to queue...');
        await saveAndQueueRequest(form);

        const response = add_patient_and_queue_request.value;
        if (response?.success) {
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
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
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

function handleCancel() {
    emit('cancel');
}

function handleSuccess(){
    emit('success');
}
</script>