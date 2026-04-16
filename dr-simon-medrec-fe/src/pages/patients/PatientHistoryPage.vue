<template>
    <div>
        <basic-information :patient_information="patient_information" :is_loading="is_loading" back_route_name="patients" />
        <div class="q-mt-xl">
            <patient-history :patient_id="patient_id" />
        </div>
    </div>
</template>

<script setup>
import { getCurrentInstance, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { usePatientStore } from 'src/stores/patient';
import { storeToRefs } from 'pinia';
import { useSpinner } from 'src/composables/spinner';
import { Notify } from 'quasar';
import BasicInformation from 'src/components/patients/BasicInformation.vue';
import PatientHistory from 'src/components/patients/PatientHistory.vue';

const { proxy} = getCurrentInstance();
const helpers = proxy.$helpers;

const spinner = useSpinner();
const patient_store = usePatientStore();
const { getPatientInformation } = patient_store;
const { get_patient_information_request } = storeToRefs(patient_store);

const route = useRoute();
const patient_id = route.params.patient_id;
const is_loading = ref(true);
const patient_information = ref(null);

async function fetchPatientInformation() {
    try {
        spinner.show('Fetching visit information...');
        is_loading.value = true;
        await getPatientInformation(patient_id);
        const response = get_patient_information_request.value;
        if (response?.data && response?.success) {
            patient_information.value = response.data;
        } else {
            console.error('Failed to fetch patient information:', response);
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Failed to fetch visit information. Please try again.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
        is_loading.value = false;
    }
}

onMounted(() => {
    fetchPatientInformation();
});
</script>