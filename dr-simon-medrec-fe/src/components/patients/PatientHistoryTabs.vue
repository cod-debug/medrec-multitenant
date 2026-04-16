<template>
    <div class="shadow-2 q-my-md">
        <q-tabs
            v-model="selected_tab"
            align="left"
            class="bg-blue-7 text-white shadow-2"
            :breakpoint="0"
            inline-label
        >
            <q-tab v-for="(tab, index) in available_tabs" :key="index" :name="tab.name" :icon="tab.icon" :label="tab.display" />
        </q-tabs>
        <q-tab-panels v-model="selected_tab" animated>
          <q-tab-panel name="complaints">
            <history-tab-complaints :complaints="visit.complaints" />
          </q-tab-panel>
          <q-tab-panel name="findings">
            <history-tab-findings :findings="visit.findings" :findings_images="visit.findings_images" />
          </q-tab-panel>
          <q-tab-panel name="diagnosis">
            <history-tab-diagnosis :diagnoses="visit.diagnoses" />
          </q-tab-panel>
          <q-tab-panel name="prescriptions">
            <history-tab-prescriptions :prescriptions="visit.prescriptions" :patient="visit.patient" :visit_date="visit.created_at" />
          </q-tab-panel>
          <q-tab-panel name="treatments">
            <history-tab-treatments :treatments="visit.treatments" />
          </q-tab-panel>
          <q-tab-panel name="laboratories">
             <history-tab-laboratories :laboratories="visit.laboratories" :laboratories_images="visit.laboratories_images" />
          </q-tab-panel>
          <q-tab-panel name="referrals">
             <history-tab-referrals :referrals="visit.referrals" :patient="visit.patient" />
          </q-tab-panel>
          <q-tab-panel name="admission_fee">
            <div>
                <p class="text-h5 text-green-10 font-bold">₱ {{ visit.admission_fee }}</p>
            </div>
          </q-tab-panel>
        </q-tab-panels>
    </div>
</template>
<script setup>
import { onMounted, ref } from 'vue';
import HistoryTabComplaints from './history-tabs/HistoryTabComplaints.vue';
import HistoryTabFindings from './history-tabs/HistoryTabFindings.vue';
import HistoryTabDiagnosis from './history-tabs/HistoryTabDiagnosis.vue';
import HistoryTabPrescriptions from './history-tabs/HistoryTabPrescriptions.vue';
import HistoryTabTreatments from './history-tabs/HistoryTabTreatments.vue';
import HistoryTabLaboratories from './history-tabs/HistoryTabLaboratories.vue';
import HistoryTabReferrals from './history-tabs/HistoryTabReferrals.vue';

const { visit } = defineProps({
    visit: {
        type: Object,
        required: true
    },
});
const default_tabs = [
    {
        name: 'complaints',
        icon: 'comment',
        display: 'Complaints',
    },
    {
        name: 'findings',
        icon: 'search',
        display: 'PE Findings',
    },
    {
        name: 'diagnosis',
        icon: 'troubleshoot',
        display: 'Diagnosis',
    },
    {
        name: 'prescriptions',
        icon: 'vaccines',
        display: 'Treatments',
    },
    {
        name: 'treatments',
        icon: 'vaccines',
        display: 'Treatments',
    },
    {
        name: 'laboratories',
        icon: 'science',
        display: 'Laboratories',
    },
    {
        name: 'referrals',
        icon: 'assignment_ind',
        display: 'Referrals',
    },
    {
        name: 'admission_fee',
        icon: 'payments',
        display: 'Admission Fee',
    }
];

const available_tabs = ref([]);
const selected_tab = ref('complaints');
onMounted(() => {
    // show available tabs based on visit data
    if(visit.complaints && visit.complaints.length > 0) {
        available_tabs.value.push(default_tabs.find(tab => tab.name === 'complaints'));
    }
    if((visit.findings && visit.findings.length > 0) || (visit.findings_images && visit.findings_images.length > 0)) {
        available_tabs.value.push(default_tabs.find(tab => tab.name === 'findings'));
    }
    if(visit.diagnoses && visit.diagnoses.length > 0) {
        available_tabs.value.push(default_tabs.find(tab => tab.name === 'diagnosis'));
    }
    if(visit.treatments && visit.treatments.length > 0) {
        available_tabs.value.push(default_tabs.find(tab => tab.name === 'treatments'));
    }
    if(visit.prescriptions && visit.prescriptions.length > 0) {
        available_tabs.value.push(default_tabs.find(tab => tab.name === 'prescriptions'));
    }
    if((visit.laboratories && visit.laboratories.length > 0) || (visit.laboratories_images && visit.laboratories_images.length > 0)) {
        available_tabs.value.push(default_tabs.find(tab => tab.name === 'laboratories'));
    }
    if(visit.referrals && visit.referrals.length > 0) {
        available_tabs.value.push(default_tabs.find(tab => tab.name === 'referrals'));
    }
    if(visit.admission_fee && visit.admission_fee > 0) {
        available_tabs.value.push(default_tabs.find(tab => tab.name === 'admission_fee'));
    }
    if(available_tabs.value.length > 0) {
        selected_tab.value = available_tabs.value[0].name;
    } else {
        selected_tab.value = 'complaints';
    }
});
</script>