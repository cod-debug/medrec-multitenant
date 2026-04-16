<template>
    <div class="print-container-prescription">
        <div>
            <print-prescription-header />
            <print-prescription-patient-info :patient="props.patient" />
            <img :src="RxPicture" width="40" class="q-mt-sm q-ml-sm" alt="rx icon" />
        </div>
        <!-- patient information -->
        <div class="print-body grow-1" v-if="props.prescriptions.length > 0 && (props.prescriptions[0].medicine_generic?.length > 0 || props.prescriptions[0].is_remarks_only)">
             <table style="width: 100%; padding: .5rem 0;"
                :style="props.prescriptions.length > 1 && props.prescriptions.length - 1  != key? 'border-bottom: .5px solid black;' : ''"
                v-for="(prescription, key) in props.prescriptions"
                :key="prescription.id">
                <tbody>
                    <tr v-if="prescription.medicine_generic">
                        <td style="width: 70%;"><strong>{{ prescription.medicine_generic }} {{ prescription.dosage }}</strong></td>
                        <td style="width: 20%;">{{ prescription.quantity_prefix }} <span v-if="prescription.quantity">#</span><strong>{{ prescription.quantity }}</strong></td>
                    </tr>
                    <tr v-if="prescription.medicine_brand">
                        <td colspan="2">
                            <span>({{ prescription.medicine_brand }})</span>
                        </td>
                    </tr>
                    <tr v-if="prescription.reminder">
                        <td colspan="2">
                            <span>Sig. {{ prescription.reminder }}</span>
                            <span v-if="prescription.duration"> for {{ prescription.duration }} days</span>
                        </td>
                    </tr>
                    <tr v-if="prescription.is_remarks_only && prescription.remarks">
                        <td colspan="2">
                            <span>{{ prescription.remarks }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="print-body grow-1" v-else>
            <p>No treatments added.</p>
        </div>
        <print-prescription-footer />
    </div>
</template>
<script setup>
import PrintPrescriptionHeader from './PrintPrescriptionHeader.vue';
import PrintPrescriptionPatientInfo from './PrintPrescriptionPatientInfo.vue';
import PrintPrescriptionFooter from './PrintPrescriptionFooter.vue';
import RxPicture from 'assets/images/rx-icon.png';
const props = defineProps({
    prescriptions: {
        type: Array,
        default: () => []
    },
    patient: {
        type: Object,
        default: () => ({})
    }
});
</script>
<style scoped>

.print-container-prescription {
    display: none!important;
}

@media print {
    .print-container-prescription {
        height: 8.5in;
        width: 5.5in;
        padding: .25in;
        border: 1px solid rgba(0, 0, 0, 0.8);
        background-color: white;
        position: relative;
        display: block!important;
    }
     .print-body {
        height: 425.15px!important;
    }
}

.patient-information .value {
    border-bottom: 1px solid rgba(0, 0, 0, 0.8);
    flex-grow: 1;
    text-align: center;
}

.print-container-prescription {
    display: flex;
    flex-direction: column;
}

.print-body {
    height: 425.15px!important;
    margin: 10px 70px;
    margin-top: -5px;
}
</style>