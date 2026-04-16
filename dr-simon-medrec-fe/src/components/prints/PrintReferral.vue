<template>
    <div class="print-container-referral">
        <div>
            <print-prescription-header />
            <print-prescription-patient-info :patient="props.patient" />
            <img :src="RxPicture" width="60" class="q-mt-sm q-ml-sm" alt="rx icon" />
        </div>
        <!-- patient information -->
        <div class="print-body grow-1">
            <p v-html="helpers.nl2br(props.referrals[0]?.description || '')"></p>
        </div>
        <print-prescription-footer />
    </div>
</template>
<script setup>
import PrintPrescriptionHeader from './PrintPrescriptionHeader.vue';
import PrintPrescriptionPatientInfo from './PrintPrescriptionPatientInfo.vue';
import PrintPrescriptionFooter from './PrintPrescriptionFooter.vue';
import RxPicture from 'assets/images/rx-icon.png';
import { getCurrentInstance } from 'vue';

const { proxy } = getCurrentInstance()
const helpers = proxy.$helpers;
const props = defineProps({
    referrals: {
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

.print-container-referral {
    display: none!important;
}

@media print {
    .print-container-referral {
        height: 8.5in;
        width: 11in;
        padding: .25in;
        border: 1px solid rgba(0, 0, 0, 0.8);
        background-color: white;
        position: relative;
        display: block!important;
    }
     .print-body {
        height: 415.15px!important;
    }
}

.patient-information .value {
    border-bottom: 1px solid rgba(0, 0, 0, 0.8);
    flex-grow: 1;
    text-align: center;
}

.print-container-referral {
    display: flex;
    flex-direction: column;
}

.print-body {
    height: 415.15px!important;
    margin: 10px 70px;
    font-size: 12pt;
}
</style>