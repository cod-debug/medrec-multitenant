<template>
    <div class="relative">
        <q-form ref="patient_visit_form">
            <div style="margin-bottom: 85px;">
                <basic-information 
                    :patient_information="visit_information?.patient" 
                    :visit_date="visit_information?.visit_date" 
                    :is_loading="is_loading" 
                    back_route_name="patients-today"
                />
                <div class="q-mt-md" v-if="!is_readonly">
                    <q-toggle
                        v-model="is_prescription_only"
                        color="primary"
                        label="Prescription or referral only"
                    />
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 q-pa-sm" v-if="!is_prescription_only">
                        <patient-complaints v-model:complaints="complaints"
                            :is_readonly="is_readonly"
                            @remove="removeInformation"
                        />
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm" v-if="!is_prescription_only">
                        <patient-findings v-model:findings="findings"
                            v-model:findings_images="findings_images"
                            :is_readonly="is_readonly"
                            @remove="removeInformation"
                            @capture="handleCaptureFindingsImage"
                            @removeImage="removeFindingsImage"
                        />
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm" v-if="!is_prescription_only">
                        <patient-diagnosis v-model:diagnoses="diagnoses"
                            :is_readonly="is_readonly"
                            @remove="removeInformation"
                        />
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm">
                        <patient-prescriptions v-model:prescriptions="prescriptions"
                            v-model:medicine_generic_names="medicine_generic_names"
                            v-model:medicine_brand_names="medicine_brand_names"
                            v-model:medicine_dosages="medicine_dosages"
                            v-model:medicine_preparation_methods="medicine_preparation_methods"
                            :is_readonly="is_readonly"
                            @remove="removeInformation"
                            @print="helpers.handlePrint('.print-container-prescription')"
                        />
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm" v-if="!is_prescription_only">
                        <patient-laboratories v-model:laboratories="laboratories"
                        v-model:laboratory_images="laboratory_images"
                        :is_readonly="is_readonly"
                        @remove="removeInformation"
                        @capture="handleCaptureLaboratoryImage"
                        @removeImage="removeLaboratoryImage"
                        />
                    </div>
                    <div class="col-12 col-md-6 q-pa-sm">
                        <patient-referrals v-model:referrals="referrals"
                            :is_readonly="is_readonly"
                            @print="helpers.handlePrint('.print-container-referral')"
                        />
                    </div>
                </div>
                <print-patient-prescriptions ref="print_prescription_ref" :prescriptions="prescriptions.filter(p => !p.is_remarks_only)" :patient="visit_information?.patient" />
                <print-referral ref="print_referral_ref" :referrals="referrals" :patient="visit_information?.patient" />
                <q-card class="q-ma-sm q-mt-lg" v-if="visit_information?.patient">
                    <q-card-section>
                        <patient-history :patient_id="visit_information?.patient?.id" :visit_id="visit_id" />
                    </q-card-section>
                </q-card>
            </div>

            <div class="fixed q-pa-md bg-white shadow-3" style="width: 100%; z-index: 20; bottom: 0;right: 0;">
                <div class="flex justify-end" style="gap: .5rem;">
                    <div class="flex items-end">
                        <q-badge class="flex q-py-sm" style="font-size: 10pt;" v-if="visit_information?.visit_status === 'settled'" :color="helpers.formatVisitStatus(visit_information?.visit_status)?.color">
                            <span>Transaction Status:</span> <strong class="q-px-xs">{{ helpers.formatVisitStatus(visit_information?.visit_status)?.label || 'No status' }}</strong> <q-icon name="check_circle" />
                        </q-badge>
                    </div>
                    <q-input v-model="visit_fee" outlined label="Visit Fee (₱)" type="number" :readonly="is_readonly || visit_information?.visit_status === 'settled'" v-if="level_of_authorization == 1 && visit_information?.visit_status !== 'settled'" />
                    <q-input :model-value="helpers.formatCurrency(visit_fee)" outlined label="Visit Fee (₱)" readonly v-else />
                    <template v-if="level_of_authorization == 1">
                        <q-btn type="button" color="secondary" label="Save and Continue Editing" icon="save" @click="handleSubmit(true)" />
                        <q-btn type="button" color="primary" label="Save" icon="check_circle" @click="handleSubmit(false)" />
                    </template>
                    <template v-if="level_of_authorization == 2 && visit_information?.visit_status == 'for-payment'">
                        <q-btn color="green-14" label="Mark as Paid" icon="check_circle" @click="open_confirmation_modal = true" />
                    </template>
                </div>
            </div>
        </q-form>
        <confirmation-modal
            v-model="open_confirmation_modal"
            title="Confirm Approval"
            :message="`Are you sure you want to mark this visit as settled?`"
            confirm_label="Yes, Approve"
            cancel_label="Cancel"
            confirm_btn_color="green-14"
            @confirm="handleUpdateStatus('settled')"
        />
    </div>
</template>
<script setup>
import { onMounted, ref, getCurrentInstance, computed } from 'vue';
import { usePatientStore } from 'stores/patient';
import { storeToRefs } from 'pinia';
import { useRoute, useRouter } from 'vue-router';
import { useSpinner } from 'src/composables/spinner';
import { Notify } from 'quasar';
import { useAuthStore } from 'src/stores/auth';
import BasicInformation from 'components/patients/BasicInformation.vue';
import ConfirmationModal from 'components/modals/ConfirmationModal.vue';
import PatientHistory from 'src/components/patients/PatientHistory.vue';
import PatientComplaints from 'components/patients-today/PatientComplaints.vue';
import PatientFindings from 'src/components/patients-today/PatientFindings.vue';
import PatientDiagnosis from 'src/components/patients-today/PatientDiagnosis.vue';
import PatientPrescriptions from 'src/components/patients-today/PatientPrescriptions.vue';
import PatientLaboratories from 'components/patients-today/PatientLaboratories.vue';
import PatientReferrals from 'src/components/patients-today/PatientReferrals.vue';
import PrintPatientPrescriptions from 'components/prints/PrintPatientPrescriptions.vue';
import PrintReferral from 'src/components/prints/PrintReferral.vue';

const auth_store = useAuthStore();
const level_of_authorization = computed(() => auth_store.getLevelOfAuthorization);
const open_confirmation_modal = ref(false);
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const spinner = useSpinner();
const medicine_generic_names = ref([]);
const medicine_brand_names = ref([]);
const medicine_dosages = ref([]);
const medicine_preparation_methods = ref([]);
const router = useRouter();
const route = useRoute();
const visit_id = route.params.visit_id;
const is_loading = ref(true);
const patient_store = usePatientStore();
const { viewVisitInformation, saveVisitInformation, removeVisitInformation, updateVisitStatus, uploadLaboratoryImage, deleteLaboratoryImage, uploadFindingsImage, deleteFindingsImage } = patient_store;
const { get_visit_information_request, save_visit_information_request, remove_visit_information_request, update_visit_status_request, upload_laboratory_image_request, delete_laboratory_image_request, upload_findings_image_request, delete_findings_image_request } = storeToRefs(patient_store);
const visit_information = ref(null);
const patient_visit_form = ref(null);

const complaints = ref([
    {
        id: null,
        name: '',
        remarks: ''
    }
]);

const findings = ref([
    {
        id: null,
        name: '',
        remarks: ''
    }
]);

const diagnoses = ref([
    {
        id: null,
        name: '',
        remarks: ''
    }
]);

const prescriptions = ref([
    {
        id: null,
        medicine_generic: '',
        medicine_brand: '',
        dosage: '',
        reminder: '',
        duration: '',
        quantity: '',
        quantity_prefix: '',
        remarks: '',
        is_remarks_only: false
    }
]);

const laboratories = ref([
    {
        id: null,
        name: '',
        remarks: ''
    }
]);

const laboratory_images = ref([]);
const findings_images = ref([]);

const visit_fee = ref(null);
const referrals = ref([
    {
        id: null,
        description: '',
    }
]);

const is_prescription_only = ref(false);

async function getVisitInformation() {
    try {
        spinner.show('Fetching visit information...');
        is_loading.value = true;
        await viewVisitInformation({ visit_id });
        const response = get_visit_information_request.value;
        if (response?.data && response?.success) {
            visit_information.value = response.data.visit;
            medicine_generic_names.value = response.data.generic_medicines;
            medicine_brand_names.value = response.data.medicine_brand_names;
            medicine_dosages.value = response.data.doses;
            medicine_preparation_methods.value = response.data.preparations;

            const mappedComplaints = visit_information.value.complaints.map(complaint => {
                return {
                    id: complaint.id,
                    name: complaint.complaint,
                    remarks: complaint.complaint_remarks
                }
            });

            const mappedFindings = visit_information.value.findings.map(finding => {
                return {
                    id: finding.id,
                    name: finding.finding,
                    remarks: finding.finding_remarks
                }
            });

            const mappedDiagnoses = visit_information.value.diagnoses.map(diagnosis => {
                return {
                    id: diagnosis.id,
                    name: diagnosis.diagnosis,
                    remarks: diagnosis.diagnosis_remarks
                }
            });

            const mappedPrescriptions = visit_information.value.prescriptions.map(prescription => {
                return {
                    id: prescription.id,
                    medicine_generic: prescription.medicine_generic,
                    medicine_brand: prescription.medicine_brand,
                    dosage: prescription.dosage,
                    reminder: prescription.reminder,
                    duration: prescription.duration,
                    quantity: prescription.quantity,
                    quantity_prefix: prescription.quantity_prefix,
                    remarks: prescription.remarks,
                    is_remarks_only: prescription.is_remarks_only == 1,
                }
            });

            const mappedLaboratories = visit_information.value.laboratories.map(laboratory => {
                return {
                    id: laboratory.id,
                    name: laboratory.laboratory_test,
                    remarks: laboratory.laboratory_remarks,
                }
            });

            const mappedReferrals = visit_information.value.referrals.map(referral => {
                return {
                    id: referral.id,
                    description: referral.description,
                }
            });

            complaints.value = visit_information.value.complaints.length > 0 ? mappedComplaints : complaints.value;
            findings.value = visit_information.value.findings.length > 0 ? mappedFindings : findings.value;
            diagnoses.value = visit_information.value.diagnoses.length > 0 ? mappedDiagnoses : diagnoses.value;
            prescriptions.value = visit_information.value.prescriptions.length > 0 ? mappedPrescriptions : prescriptions.value;
            laboratories.value = visit_information.value.laboratories.length > 0 ? mappedLaboratories : laboratories.value;
            visit_fee.value = visit_information.value.visit_fees.length > 0 ? (visit_information.value.visit_fees[0].fee_amount > 0 ? visit_information.value.visit_fees[0].fee_amount : null) : null;
            laboratory_images.value = visit_information.value.laboratories_images || [];
            findings_images.value = visit_information.value.findings_images || [];
            referrals.value = visit_information.value.referrals.length > 0 ? mappedReferrals : referrals.value;
        } else {
            visit_information.value = null;
            Notify.create({
                message: response.message || 'Failed to fetch visit information.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Failed to fetch visit information. Please try again.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
        is_loading.value = false;
    }
}

async function handleSubmit(saveAndContinue = false) {
    try {
        spinner.show('Saving visit information...');
        const form_is_valid = await patient_visit_form.value.validate();
        if (!form_is_valid) {
            Notify.create({
                message: 'Please fill in the required fields.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
            return;
        }

        const payload = {
            complaints: complaints.value,
            findings: findings.value,
            diagnoses: diagnoses.value,
            prescriptions: prescriptions.value,
            visit_fee: visit_fee.value ?? 0,
            laboratories: laboratories.value,
            referrals: referrals.value,
            save_and_continue: saveAndContinue,
            visit_id: visit_id,
            is_save_as_draft: saveAndContinue,
        }

        await saveVisitInformation(payload);
        const response = save_visit_information_request.value;

        if(response?.success){
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });

            if(!saveAndContinue){
                router.push({ name: 'patients-today' });
            } else {
                await getVisitInformation();
            }
        } else {
            Notify.create({
                message: response.message || 'Failed to save visit information.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Please fill in the required fields.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}

const is_readonly = computed(() => {
    return level_of_authorization.value != 1 ? true : false;
});

async function removeInformation(type, key, id = null){
    let shouldRemove = true;
    
    if(id){
        shouldRemove = await handleRemoveVisitInformation(type, id);
    }

    if(shouldRemove){
        switch(type){
            case 'complaint':
                complaints.value.splice(key, 1);
                break;
            case 'finding':
                findings.value.splice(key, 1);
                break;
            case 'diagnosis':
                diagnoses.value.splice(key, 1);
                break;
            case 'prescription':
                prescriptions.value.splice(key, 1);
                break;
            case 'laboratory':
                laboratories.value.splice(key, 1);
                break;
            case 'referral':
                referrals.value.splice(key, 1);
                break;
        }
    }
}

async function handleRemoveVisitInformation(type, id) {
    try {
        spinner.show(`Removing ${type}...`);
        const form_is_valid = await patient_visit_form.value.validate();
        if (!form_is_valid) {
            Notify.create({
                message: 'Please fill in the required fields.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
            return false;
        }

        const payload = {
            type: type,
            id: id,
            visit_id: visit_id,
        }

        await removeVisitInformation(payload);
        const response = remove_visit_information_request.value;    
        if(response?.success){
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            return true;
        } else {
            Notify.create({
                message: response.message || 'Failed to remove visit information.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
            return false;
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Please fill in the required fields.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
        return false;
    } finally {
        spinner.hide();
    }
}

async function handleUpdateStatus(status) {
    try {
        spinner.show(`Updating status to ${status}...`);
        const payload = {
            visit_status: status,
            visit_id: visit_id,
        }

        await updateVisitStatus(payload);
        const response = update_visit_status_request.value;    
        if(response?.success){
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            router.push({ name: 'patients-today' });
        } else {
            Notify.create({
                message: response.message || 'Failed to update visit status.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Failed to update visit status.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}

async function handleCaptureLaboratoryImage(image) {
    try {
        spinner.show('Uploading laboratory image...');
        const timestamp = new Date().getTime();
        const file = helpers.base64ToFile(image, `${timestamp}-laboratory-image.png`)
        const formData = new FormData();
        formData.append('image', file);

        const payload = {
            visit_id: visit_id,
            formData: formData,
        }

        await uploadLaboratoryImage(payload);
        const response = upload_laboratory_image_request.value;
        if(response?.data && response?.success){
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            laboratory_images.value.push(response.data.uploaded_image);
        } else {
            Notify.create({
                message: response.message || 'Failed to capture laboratory image.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });

        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Failed to capture laboratory image.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}

async function removeLaboratoryImage(image, key) {
    try {
        spinner.show('Removing laboratory image...');
        const payload = {
            id: image.id,
        }

        await deleteLaboratoryImage(payload);
        const response = delete_laboratory_image_request.value;
        if(response?.success){
            Notify.create({
                message: response?.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            laboratory_images.value.splice(key, 1);
        } else {
            Notify.create({
                message: response?.message || 'Failed to remove laboratory image.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });

        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Failed to remove laboratory image.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}

async function handleCaptureFindingsImage(image) {
    try {
        spinner.show('Uploading findings image...');
        const timestamp = new Date().getTime();
        const file = helpers.base64ToFile(image, `${timestamp}-findings-image.png`)
        const formData = new FormData();
        formData.append('image', file);

        const payload = {
            visit_id: visit_id,
            formData: formData,
        }

        await uploadFindingsImage(payload);
        const response = upload_findings_image_request.value;
        if(response?.data && response?.success){
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            findings_images.value.push(response.data.uploaded_image);
        } else {
            Notify.create({
                message: response.message || 'Failed to capture findings image.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });

        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Failed to capture findings image.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}

async function removeFindingsImage(image, key) {
    try {
        spinner.show('Removing findings image...');
        const payload = {
            id: image.id,
        }

        await deleteFindingsImage(payload);
        const response = delete_findings_image_request.value;
        if(response?.success){
            Notify.create({
                message: response?.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            findings_images.value.splice(key, 1);
        } else {
            Notify.create({
                message: response?.message || 'Failed to remove findings image.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });

        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'Failed to remove findings image.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}
onMounted(() => {
    getVisitInformation();
});
</script>