<template>
    <div>
        <template v-if="!show_add_form">
            <span class="text-grey-7">Search if patient exists</span>
            <q-form @submit.prevent="handleSearchPatient">
                <q-input label="Search Patient" v-model="search" :rules="[rules.required]" outlined>
                    <template v-slot:append>
                        <q-btn color="secondary" type="submit" @click="handleSearchPatient" icon="person_search" v-if="search.trim() !== ''" />
                    </template>
                </q-input>
            </q-form>
        </template>

        <template v-if="has_searched && patients.length === 0 && !show_add_form">
            <div class="q-pa-md text-amber-10 q-mb-md bg-amber-2" style="border-radius: 5px;">
                No patients found. You can proceed to add this patient as a new inpatient. <q-btn color="amber-10" size="sm" label="Add New Inpatient" @click="handleProceed(null)" />
            </div>
        </template>

        <q-btn color="amber-10" label="Add New Patient" icon="add_circle" @click="handleProceed(null)" v-if="has_searched && !show_add_form && patients.length > 0" class="q-mb-md" size="sm" />

        <div v-if="has_searched && patients.length > 0 && !proceed_to_admission" class="q-mb-md">
            <span class="text-grey-7">Select patient from search results</span>
            <q-list bordered padding>
                <q-item v-for="patient in patients" :key="patient.id" clickable @click="handleProceed(patient)">
                    <q-item-section>
                        <q-item-label>{{ patient.full_name }}</q-item-label>
                        <q-item-label caption>{{ helpers.formatDate(patient.date_of_birth) }} • {{ helpers.getAge(patient.date_of_birth) }} years old</q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
        </div>
        
        <div class="q-pa-md text-amber-10 q-mb-md bg-amber-2" style="border-radius: 5px;" v-if="form.patient_id">
            Existing Patient found. You can proceed to admit this patient as an inpatient.
        </div>
        <q-form class="q-gutter-sm" v-if="show_add_form">
            <q-input label="Full Name" v-model="form.name" :rules="[rules.required]" outlined ref="full_name_input" :disable="form.patient_id !== null" />
            <q-input label="Age" v-model="form.age" type="number" :rules="[rules.required]" outlined :disable="form.patient_id !== null" />
            <q-select label="Sex" v-model="form.gender" :options="['Male', 'Female']" :rules="[rules.required]" outlined :disable="form.patient_id !== null" />
            <q-input label="Room Number" v-model="form.room_number" type="text" outlined class="q-mb-lg" />
            <q-input type="date" label="Admission Date" v-model="form.admission_date" :rules="[rules.required]" outlined />
        </q-form>

        <div class="flex q-gutter-md q-mb-md">
            <q-btn type="button" :label="form.patient_id ? 'Admit Patient' : 'Save Information'" icon="check_circle" color="primary" v-if="proceed_to_admission" @click="handleSubmitAdmission" />
            <q-btn outline type="button" label="Cancel" color="grey-8" icon="cancel" @click="handleCancel" />
            <q-btn outline type="button" label="Search Again" color="secondary" icon="undo" @click="handleSearchAgain" v-if="proceed_to_admission" />
        </div>
    </div>
</template>
<script setup>
import { ref, getCurrentInstance, nextTick } from 'vue';
import { useSpinner } from 'src/composables/spinner';
import { useInpatientStore } from 'src/stores/inpatient';
import { storeToRefs } from 'pinia';
import moment from 'moment';
import { Notify } from 'quasar';
import { useRouter } from 'vue-router';

const inpatient_store = useInpatientStore();
const { search_inpatient_request, add_and_admit_request } = storeToRefs(inpatient_store);
const { searchInpatient } = inpatient_store;
const spinner = useSpinner();
const { proxy } = getCurrentInstance();
const rules = proxy.$rules;
const helpers = proxy.$helpers;
const proceed_to_admission = ref(false);
const patients = ref([]);
const is_loading = ref(false);
const has_searched = ref(false);
const search = ref('');
const full_name_input = ref(null);
const show_add_form = ref(false);
const router = useRouter();

const emit = defineEmits(['cancel', 'success']);

function handleCancel() {
    emit('cancel');
}

const form = ref({
    name: '',
    gender: '',
    admission_date: moment().format('YYYY-MM-DD'),
    admit_patient: true,
    patient_id: null,
    age: '',
    room_number: '',
});

async function handleSearchPatient(){
    try {
        spinner.show('Searching patient...');
        is_loading.value = true;
        const payload = {
            search: search.value,
        };

        await searchInpatient(payload);
        const response = search_inpatient_request.value;
        if (response?.data && response?.success) {
            patients.value = response.data;
        } else {
            patients.value = [];
        }
    } catch (error) {
        console.error('Error fetching patient history:', error);
    } finally {
        is_loading.value = false;
        spinner.hide();
        has_searched.value = true;
    }
}

async function handleProceed(patient) {
    show_add_form.value = true;
    proceed_to_admission.value = true;

    if (patient) {
        form.value = {
            name: patient.full_name,
            date_of_birth: moment(patient.date_of_birth).format('YYYY-MM-DD'),
            gender: patient.gender,
            admission_date: moment().format('YYYY-MM-DD'),
            admit_patient: true,
            patient_id: patient.id,
            age: patient.age,
            room_number: patient.room_number,
        }
    } else {
        form.value = {
            name: search.value,
            age: '',
            room_number: '',
            gender: '',
            admission_date: moment().format('YYYY-MM-DD'),
            admit_patient: true,
            patient_id: null,
        }
        await nextTick();
        full_name_input.value.focus();
    }
}

async function handleSubmitAdmission() {
    try {
        spinner.show('Submitting admission...');
        const payload = {
            full_name: form.value.name,
            gender: form.value.gender,
            age: form.value.age,
            room_number: form.value.room_number,    
            admission_date: form.value.admission_date,
            admit_patient: form.value.admit_patient,
            patient_id: form.value.patient_id,
        };

        await inpatient_store.savePatientInfoAndAdmit(payload);
        const response = add_and_admit_request.value;
        if(response && response.success) {
            Notify.create({
                message: 'Patient admitted successfully.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });

            router.push({ name: 'inpatient-admission-details', params: { admission_id: response.data.admission.id } });
        } else {
            Notify.create({
                message: response.message || 'Failed to admit patient. Please try again.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (error) {
        console.error('Error submitting admission:', error);
    } finally {
        spinner.hide();
    }
}

function handleSearchAgain(){
    has_searched.value = false;
    patients.value = [];
    form.value.patient_id = null;
    search.value = '';
    show_add_form.value = false;
    proceed_to_admission.value = false;
}
</script>