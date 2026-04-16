<template>
    <div>
        <div class="row">
            <div class="col-12 col-lg-6 q-pa-md" v-if="prescriptions.some(i => !i.is_remarks_only)">
                <q-btn label="Print" @click="helpers.handlePrint(`.print-container-prescription-preview-${formatted_visit_date}`)" icon="print" class="q-mb-md" color="primary" />
                <print-patient-prescriptions-visible :prescriptions="prescriptions.filter(i => !i.is_remarks_only)" :patient="patient" :visit_date="visit_date" />
            </div>
            <div class="col-12 col-lg-6 q-pa-md" v-if="prescriptions.some(i => i.is_remarks_only)">
                <p><strong>Remarks:</strong></p>
                <ul>
                    <li v-for="prescription in prescriptions.filter(i => i.is_remarks_only)" :key="prescription.id">
                        <p>{{ prescription.remarks }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { getCurrentInstance } from 'vue';
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
import PrintPatientPrescriptionsVisible from 'src/components/prints/PrintPatientPrescriptionsVisible.vue';

const { prescriptions, patient, visit_date } = defineProps({
    prescriptions: {
        type: Array,
        required: true
    },
    patient: {
        type: Object,
        required: true
    },
    visit_date: {
        type: String,
        required: true
    }
});
const formatted_visit_date = visit_date ? visit_date.slice(0,10) : '';
</script>