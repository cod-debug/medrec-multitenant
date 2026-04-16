<template>
    <div class="q-gutter-md">
        <div class="flex q-gutter-md items-center">
            <span class="text-h6"><q-icon name="medical_information" /> Medical History</span>
            <span>|</span>
            <span class="text-subtitle text-grey-8">Review patient's past visits and medical records.</span>
        </div>
        <div class="q-px-lg" v-if="visit_records.length > 0">
            <div v-for="visit in visit_records" :key="visit.id" class="bg-blue-grey-1 q-pa-md q-my-md" style="border-radius: 4px;">
                <div class="flex q-gutter-md items-center">
                    <span>
                        <span style="font-size: 13pt;"><q-icon name="label_important" /> {{ helpers.formatDate(visit.visit_date, date_format) }}</span>
                    </span>
                    <span class="text-grey-5">•</span>
                    <span>
                        <div v-for="(fee, index) in visit.visit_fees" :key="index" class="text-capitalize">
                            {{ fee?.fee_description || 'Consultation Fee' }} : <strong>{{ helpers.formatCurrency(fee.fee_amount) }}</strong> 
                        </div>
                    </span>
                    <span class="text-grey-5">•</span>
                    <span>
                        <q-badge :color="helpers.formatVisitStatus(visit.visit_status).color" class="text-white">
                            {{ helpers.formatVisitStatus(visit.visit_status).label }}
                        </q-badge>
                    </span>
                </div>
                <patient-history-tabs :visit="visit" />
            </div>
        </div>
        <div v-else class="q-pa-md text-amber-10 q-my-sm bg-amber-2" style="border-radius: 5px;">
            No recorded visits for this patient yet.
        </div>
    </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useSpinner } from 'src/composables/spinner';
import { usePatientStore } from 'src/stores/patient';
import { getCurrentInstance, onMounted, ref } from 'vue';
import PatientHistoryTabs from './PatientHistoryTabs.vue';

const date_format = {year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'}; 
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const patient_store = usePatientStore();
const { getPatientVisitHistory } = patient_store;
const { get_patient_history_request } = storeToRefs(patient_store);
const spinner = useSpinner();
const visit_records = ref([]);
const is_loading = ref(true);

const { patient_id, visit_id } = defineProps({
    patient_id: {
        type: [String, Number],
        required: true
    },
    visit_id: {
        type: [String, Number],
        required: false,
        default: null
    },
});

async function fetchPatientHistory() {
    try {
        spinner.show('Fetching patient history...');
        is_loading.value = true;
        
        const payload = {
            patient_id: patient_id,
            visit_id: visit_id,
        };

        await getPatientVisitHistory(payload);
        const response = get_patient_history_request.value;
        if (response?.data && response?.success) {
            visit_records.value = response.data;
        } else {
            visit_records.value = [];
        }
    } catch (error) {
        console.error('Error fetching patient history:', error);
    } finally {
        is_loading.value = false;
        spinner.hide();
    }
}

onMounted(() =>{
    fetchPatientHistory();
});
</script>