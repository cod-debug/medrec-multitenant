<template>
    <div>
        <div v-if="referrals.length > 0 && referrals[0].description">
            <q-btn label="print" @click="helpers.handlePrint('.print-container-referral')" icon="print" color="primary" />
            <p class="q-mt-md">
                {{ referrals[0].description }}
            </p>
            <print-referral :referrals="referrals" :patient="patient" />
        </div>
        <em class="text-grey-7" v-else>No referrals available</em>
    </div>
</template>

<script setup>
import { getCurrentInstance } from 'vue';
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;

import PrintReferral from 'src/components/prints/PrintReferral.vue';
const { referrals, patient } = defineProps({
    referrals: {
        type: Array,
        required: true
    },
    patient: {
        type: Object,
        required: true
    }
});
</script>