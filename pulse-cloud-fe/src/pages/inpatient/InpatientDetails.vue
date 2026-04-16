<template>
    <div>
        <q-btn label="Back to Records List" size="sm" icon="arrow_back" color="primary" class="q-mb-md" :to="{ name: 'inpatient-records' }" />
        <basic-information :patient_information="patient_information" v-if="patient_information" />
        
        <div class="q-px-lg" v-if="admission_records.length > 0">
            <div v-for="admission in admission_records" :key="admission.id" class="bg-blue-grey-1 q-pa-md q-my-md" style="border-radius: 4px;">
                <div class="flex q-gutter-md items-center">
                    <span>
                        <span style="font-size: 13pt;"><q-icon name="label_important" /> {{ helpers.formatDate(admission.admission_date) }}</span>
                    </span>
                    <span class="text-grey-5">•</span>
                    <span>
                        <q-badge :color="helpers.formatAdmissionStatus(admission.admission_status).color" class="text-white">
                            {{ helpers.formatAdmissionStatus(admission.admission_status).label }}
                        </q-badge>
                    </span>
                    <template v-if="admission.admission_status == 'discharged'">
                        <span class="text-grey-5">•</span>
                        <span>
                            Discharged on <strong>{{ helpers.formatDateTime(admission.discharge_date) }}</strong>
                        </span>
                    </template>
                    <template v-if="admission.referred_by">
                        <span class="text-grey-5">•</span>
                        <span>
                            Referred by <strong>{{ admission.referred_by }}</strong>
                        </span>
                    </template>
                    <template v-if="admission.referred_by_hospital">
                        <span class="text-grey-5">•</span>
                        <span>
                            <strong>{{ admission.referred_by_hospital }}</strong>
                        </span>
                    </template>
                </div>
                <patient-history-tabs :visit="admission" />
            </div>
        </div>
    </div>
</template>
<script setup>
import BasicInformation from 'src/components/patients/BasicInformation.vue';
import PatientHistoryTabs from 'src/components/patients/PatientHistoryTabs.vue';
import { ref, getCurrentInstance, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useSpinner } from 'src/composables/spinner';
import { useInpatientStore } from 'src/stores/inpatient';

const { proxy} = getCurrentInstance();
const helpers = proxy.$helpers;
const spinner = useSpinner();
const inpatient_store = useInpatientStore();
const { viewPatientRecords } = inpatient_store;
const { view_patient_records_request } = storeToRefs(inpatient_store);
const patient_information = ref(null);
const admission_records = ref([]);
const route = useRoute();
const patient_id = route.params.patient_id;

async function getData(){
    try {
        spinner.show('Fetching visit information...');
        await viewPatientRecords({ patient_id: patient_id });
        const response = view_patient_records_request.value;
        console.log(response.data);
        if (response?.data && response?.success) {
            patient_information.value = response.data;
            admission_records.value = response.data.admissions || [];
        } else {
            console.error('Failed to fetch visit information:', response);
        }
    } catch (e) {
        helpers.showDebugMessage(e);
    } finally {
        spinner.hide();
    }
}

onMounted(() => {
    getData();
});
</script>