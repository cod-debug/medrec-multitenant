<template>
    <div>
        <q-btn label="Add Inpatient Admission / Referral" size="sm" icon="person_add" color="primary" @click="open_add_modal = true" />
        <add-inpatient-admissions v-model="open_add_modal" @cancel="open_add_modal = false" />
        <div class="q-mt-md">
            <inpatient-admission-table />
        </div>
    </div>
</template>
<script setup>
import AddInpatientAdmissions from 'src/components/modals/AddInpatientAdmissions.vue';
import InpatientAdmissionTable from 'src/components/inpatient/admission/InpatientAdmissionTable.vue';
import { ref } from 'vue';

const open_add_modal = ref(false);
</script>