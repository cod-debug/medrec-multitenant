<template>
    <div>
        <basic-information :patient_information="patient_information" :visit_date="visit_information?.visit_date" :is_loading="is_loading" back_route_name="visits" />
        <div class="q-mt-md q-pa-md" v-if="visit_information">
            <div class="flex q-gutter-md items-center">
                <span class="text-h6"><q-icon name="medical_information" /> Visit Details</span>
                <span>|</span>
                <span class="text-subtitle text-grey-8">Review patient's visit details.</span>
            </div>
            
            <patient-history-tabs :visit="visit_information" />
            <patient-history :visit_id="visit_id" :is_loading="is_loading" :patient_id="patient_information?.id" />
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
import PatientHistoryTabs from 'src/components/patients/PatientHistoryTabs.vue';
import PatientHistory from 'src/components/patients/PatientHistory.vue';

const { proxy} = getCurrentInstance();
const helpers = proxy.$helpers;

const spinner = useSpinner();
const patient_store = usePatientStore();
const { getPatientInformation, viewVisitInformation } = patient_store;
const { get_patient_information_request, get_visit_information_request } = storeToRefs(patient_store);

const route = useRoute();
const patient_id = route.params.patient_id;
const visit_id = route.params.visit_id;
const is_loading = ref(true);
const patient_information = ref(null);
const visit_information = ref(null);

async function fetchPatientInformation() {
    try {
        spinner.show('Fetching patient information...');
        is_loading.value = true;
        await getPatientInformation(patient_id);
        const response = get_patient_information_request.value;
        if (response?.data && response?.success) {
            patient_information.value = response.data;
        } else {
            helpers.showDebugMessage(response);
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Failed to fetch patient information. Please try again.',
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

async function getVisitInformation() {
    try {
        spinner.show('Fetching visit information...');
        is_loading.value = true;
        await viewVisitInformation({ visit_id });
        const response = get_visit_information_request.value;
        if (response?.data && response?.success) {
            visit_information.value = response.data.visit;
        } else {
            helpers.showDebugMessage(response);
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
    getVisitInformation();
});
</script>