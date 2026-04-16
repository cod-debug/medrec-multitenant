<template>
    <q-page class="q-pa-md">
        <template v-if="admission_details && admission_details.patient">
            <basic-information :patient_information="admission_details.patient" :is_loading="is_loading" :on_back="handleBack" />
        </template>
        <div v-if="admission_details" class="q-mt-md">
            <inpatient-admission-details :admission_details="admission_details" />
        </div>
    </q-page>
</template>
<script setup>
import BasicInformation from 'src/components/patients/BasicInformation.vue';
import InpatientAdmissionDetails from 'src/components/inpatient/admission/InpatientAdmissionDetails.vue';
import { getCurrentInstance, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAdmissionStore } from 'src/stores/admissions';
import { storeToRefs } from 'pinia';
import { useSpinner } from 'src/composables/spinner';

const { proxy} = getCurrentInstance();
const helpers = proxy.$helpers;
const spinner = useSpinner();
const router = useRouter();
const admission_store = useAdmissionStore();
const { getAdmissionDetails } = admission_store;
const { admission_details_request } = storeToRefs(admission_store);
const route = useRoute();
const admission_id = route.params.admission_id;
const is_loading = ref(true);
const admission_details = ref(null);

function handleBack() {
    if (window.history.length > 1) {
        router.go(-1);
    } else {
        router.push({ name: 'inpatient-admissions' });
    }
}

async function fetchAdmissionDetails() {
    try {
        spinner.show('Fetching admission details...');
        is_loading.value = true;
        await getAdmissionDetails({ id: admission_id });
        const response = admission_details_request.value;
        if (response?.data && response?.success) {
            admission_details.value = response.data;
            console.log('Admission Details:', response.data);
        } else {
            console.error('Failed to fetch admission details:', response);
        }
    } catch (e) {
        helpers.showDebugMessage(e);
    } finally {
        spinner.hide();
        is_loading.value = false;
    }
}

onMounted(() => {
    fetchAdmissionDetails();
});
</script>