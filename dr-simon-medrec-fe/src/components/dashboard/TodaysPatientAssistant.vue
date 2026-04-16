<template>
    <div>
        <q-btn icon="person_add" label="Add Patient to Queue" color="primary" class="q-mb-md" size="sm" @click="handleOpenAddPatientModal()" />
        <AddNewPatient ref="add_new_patient_modal" v-model="open_new_patient_modal" @success="getList(true)" />
        <TodaysPatientTable @refreshTable="getList(true)" />
    </div>
</template>
<script setup>
import { ref } from 'vue';
import TodaysPatientTable from './TodaysPatientTable.vue';
import AddNewPatient from '../dashboard/modals/AddNewPatient.vue';
const open_new_patient_modal = ref(false);

function handleOpenAddPatientModal() {
    open_new_patient_modal.value = true;
}
</script>