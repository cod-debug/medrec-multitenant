<template>
    <q-card>
        <q-card-section class="bg-secondary text-white">
            <div class="flex justify-between">
                <div class="text-h6" v-if="!is_loading"><q-icon name="account_circle" /> {{ patient_information.full_name }}</div>
                <div class="text-h6 flex items-center" v-else><q-icon name="account_circle" /> <q-skeleton type='text' style="width: 250px;" /></div>
                <div v-if="back_route_name || on_back">
                    <q-btn icon="arrow_left" color="white" class="text-black" size="sm" @click="handleBack">{{ back_route_label }}</q-btn>
                </div>
            </div>
        </q-card-section>
        <q-separator />
        <q-card-section>
            <div class="row">
                <div class="col-12 col-md-6 q-pa-xs" v-if="visit_date">
                    <div class="patient-info-card q-pa-sm">
                        <div class="text-subtitle2">Date of Visit:</div>
                        <div class="text-body1" v-if="!is_loading">{{ helpers.formatDate(visit_date) }}</div>
                        <div class="text-body1" v-else><q-skeleton type='text' /></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 q-pa-xs" v-if="patient_information?.address">
                    <div class="patient-info-card q-pa-sm">
                        <div class="text-subtitle2">Address:</div>
                        <div class="text-body1" v-if="!is_loading">{{ patient_information.address || '--'}}</div>
                        <div class="text-body1" v-else><q-skeleton type='text' /></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 q-pa-xs" v-if="patient_information?.date_of_birth">
                    <div class="patient-info-card q-pa-sm">
                        <div class="text-subtitle2">Birthday:</div>
                        <div class="text-body1" v-if="!is_loading">{{ helpers.formatDate(patient_information.date_of_birth) }}</div>
                        <div class="text-body1" v-else><q-skeleton type='text' /></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                    <div class="patient-info-card q-pa-sm">
                        <div class="text-subtitle2">Sex:</div>
                        <div class="text-body1" v-if="!is_loading">{{ patient_information.gender }}</div>
                        <div class="text-body1" v-else><q-skeleton type='text' /></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                    <div class="patient-info-card q-pa-sm">
                        <div class="text-subtitle2">Age:</div>
                        <template v-if="patient_information?.date_of_birth">
                            <div class="text-body1" v-if="!is_loading">{{ helpers.getAge(patient_information.date_of_birth) }}</div>
                            <div class="text-body1" v-else><q-skeleton type='text' /></div>
                        </template>
                        <template v-else-if="patient_information?.age">
                            <div class="text-body1">{{ patient_information.age }}</div>
                        </template>
                    </div>
                </div>
                <div class="col-12 col-md-6 q-pa-xs" v-if="patient_information?.phone_number">
                    <div class="patient-info-card q-pa-sm">
                        <div class="text-subtitle2">Contact Number:</div>
                        <div class="text-body1" v-if="!is_loading">{{ patient_information.phone_number || '--' }}</div>
                        <div class="text-body1" v-else><q-skeleton type='text' /></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 q-pa-xs" v-if="referred_by">
                    <div class="patient-info-card q-pa-sm">
                        <div class="text-subtitle2">Referred By:</div>
                        <div class="text-body1" v-if="!is_loading">{{ referred_by || '--' }}</div>
                        <div class="text-body1" v-else><q-skeleton type='text' /></div>
                    </div>
                </div>
            </div>
        </q-card-section> 
    </q-card>
</template>

<script setup>
import { getCurrentInstance } from 'vue';
import { useRouter } from 'vue-router';

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const router = useRouter();

const { patient_information, is_loading, back_route_name, on_back } = defineProps({
    patient_information: {
        type: Object,
        default: null,
    },
    visit_date: {
        type: String,
        default: null,
    },
    is_loading: {
        type: Boolean,
        default: false,
    },
    back_route_name: {
        type: String,
        default: null,
    },
    back_route_label: {
        type: String,
        default: 'Back to list',
    },
    on_back: {
        type: Function,
        default: null,
    },
    referred_by: {
        type: String,
        default: null,
    },
});

function handleBack() {
    if (on_back) {
        on_back();
    } else if (back_route_name) {
        router.push({ name: back_route_name });
    }
}
</script>

<style scoped>
.patient-info-card {
    border: 1px solid lightgrey;
    border-radius: 5px;
}
</style>