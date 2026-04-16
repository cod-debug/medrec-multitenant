<template>
    <div>
        <div class="row q-mt-sm patient-information">
            <div class="col-7 q-px-sm">
                <div class="flex q-gutter-sm">
                    <div class="label text-bold">Patient Name: </div> 
                    <div class="value">{{ patient.full_name || '--' }}</div>
                </div>
            </div>
            <div class="col-5 q-px-sm">
                <div class="flex q-gutter-sm">
                    <div class="label text-bold">Date: </div> 
                    <div class="value">{{ helpers.formatDate(today) || '--' }}</div>
                </div>
            </div>
        </div>
        <div class="row patient-information">
            <div class="col-7 q-px-sm">
                <div class="flex q-gutter-sm">
                    <div class="label text-bold">Address: </div> 
                    <div class="value">{{ patient.address || '--' }}</div>
                </div>
            </div>
            <div class="col-2 q-px-sm">
                <div class="flex q-gutter-sm">
                    <div class="label text-bold">Age: </div> 
                    <div class="value">{{ helpers.getAge(patient.date_of_birth) }}</div>
                </div>
            </div>
            <div class="col-3 q-px-sm">
                <div class="flex q-gutter-sm">
                    <div class="label text-bold">Sex: </div> 
                    <div class="value">{{ patient.gender || '--' }}</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { getCurrentInstance } from 'vue';

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;

const { patient } = defineProps({
    patient: {
        type: Object,
        default: () => ({})
    },
    visit_date: {
        type: String,
        default: ''
    },
});

const today = new Date();
</script>
<style scoped>
.patient-information {
    font-size: 9pt;
}
.patient-information .value {
    border-bottom: 1px solid rgba(0, 0, 0, 0.8);
    flex-grow: 1;
    text-align: center;
}
</style>