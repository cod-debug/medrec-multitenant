<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-6 q-pa-sm">
                <q-card style="height: 100%;">
                    <q-card-section class="bg-deep-orange-1 text-grey-8">
                        <div class="flex q-gutter-sm items-center text-subtitle2 text-grey-9">
                            <q-icon name="comment" color="deep-orange-6" /> <span>Complaints</span>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <ul>
                            <li v-for="(complaint, index) in visit.complaints" :key="index">
                                {{ complaint?.complaint || 'N/A' }}
                                <ul v-if="complaint.complaint_remarks">
                                    <li>{{ complaint.complaint_remarks }}</li>
                                </ul>
                            </li>
                        </ul>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-sm">
                <q-card style="height: 100%;">
                    <q-card-section class="bg-blue-1 text-grey-8">
                        <div class="flex q-gutter-sm items-center text-subtitle2 text-grey-9">
                            <q-icon name="search" color="blue-6" /> <span>PE Findings</span>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <ul>
                            <li v-for="(finding, index) in visit.findings" :key="index">
                                {{ finding?.finding || 'N/A' }}
                                <ul v-if="finding.finding_remarks">
                                    <li>{{ finding.finding_remarks }}</li>
                                </ul>
                            </li>
                        </ul>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-sm" v-if="visit.findings_images && visit.findings_images.length > 0">
                <q-card style="height: 100%;">
                    <q-card-section class="bg-amber-1 text-grey-8">
                        <div class="flex q-gutter-sm items-center text-subtitle2 text-grey-9">
                            <q-icon name="search" color="amber-6" /> <span>Findings Images</span>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <div class="q-pa-md">
                            <q-carousel
                            animated
                            v-model="findings_slides"
                            :arrows="visit.findings_images.length > 1"
                            :navigation="visit.findings_images.length > 1"
                            navigation-size="sm"
                            infinite
                            >
                                <q-carousel-slide :name="index + 1" :img-src="findings_image.image_url" v-for="(findings_image, index) in visit.findings_images" :key="index" />
                            </q-carousel>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-sm">
                <q-card style="height: 100%;">
                    <q-card-section class="bg-deep-purple-1 text-grey-8">
                        <div class="flex q-gutter-sm items-center text-subtitle2 text-grey-9">
                            <q-icon name="troubleshoot" color="deep-purple-6" /> <span>Diagnoses</span>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <ul>
                            <li v-for="(diagnosis, index) in visit.diagnoses" :key="index">
                                {{ diagnosis?.diagnosis || 'N/A' }}
                                <ul v-if="diagnosis.diagnosis_remarks">
                                    <li>{{ diagnosis.diagnosis_remarks }}</li>
                                </ul>
                            </li>
                        </ul>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-sm">
                <q-card style="height: 100%;">
                    <q-card-section class="bg-green-1 text-grey-8">
                        <div class="flex q-gutter-sm items-center text-subtitle2 text-grey-9">
                            <q-icon name="vaccines" color="green-6" /> <span>Treatments</span>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <ul>
                            <li v-for="(treatment, index) in visit.treatments" :key="index">
                                {{ treatment?.treatment || 'N/A' }}
                                <ul v-if="treatment.treatment_remarks">
                                    <li>{{ treatment.treatment_remarks }}</li>
                                </ul>
                            </li>
                        </ul>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-sm" v-if="visit.laboratories && visit.laboratories.length > 0">
                <q-card style="height: 100%;">
                    <q-card-section class="bg-amber-1 text-grey-8">
                        <div class="flex q-gutter-sm items-center text-subtitle2 text-grey-9">
                            <q-icon name="science" color="amber-6" /> <span>Laboratories</span>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <ul v-if="visit?.laboratories.length > 0">
                            <li v-for="(laboratory, index) in visit.laboratories" :key="index">
                                {{ laboratory?.laboratory_test || 'N/A' }}
                                <ul v-if="laboratory.laboratory_remarks">
                                    <li>{{ laboratory.laboratory_remarks }}</li>
                                </ul>
                            </li>
                        </ul>
                        <span v-else class="text-grey-8">--</span>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-sm" v-if="visit.laboratories_images && visit.laboratories_images.length > 0">
                <q-card style="height: 100%;">
                    <q-card-section class="bg-amber-1 text-grey-8">
                        <div class="flex q-gutter-sm items-center text-subtitle2 text-grey-9">
                            <q-icon name="science" color="amber-6" /> <span>Laboratory Images</span>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        
                        <div class="q-pa-md">
                            <q-carousel
                            animated
                            v-model="laboratory_slides"
                            :arrows="visit.laboratories_images.length > 1"
                            :navigation="visit.laboratories_images.length > 1"
                            navigation-size="sm"
                            infinite
                            >
                                <q-carousel-slide :name="index + 1" :img-src="lab_image.image_url" v-for="(lab_image, index) in visit.laboratories_images" :key="index" />
                            </q-carousel>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-md-6 q-pa-sm" v-if="visit.laboratories && visit.laboratories.length > 0">
                <q-card style="height: 100%;">
                    <q-card-section class="bg-blue-1 text-grey-8">
                        <div class="flex q-gutter-sm items-center text-subtitle2 text-grey-9">
                            <q-icon name="attach_money" color="blue-6" /> <span>Fees</span>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <div v-for="(fee, index) in visit.visit_fees" :key="index" class="text-capitalize">
                            {{ fee?.fee_description || 'Consultation Fee' }} : <strong>{{ helpers.formatCurrency(fee.fee_amount) }}</strong> 
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </div>
</template>

<script setup>
import { getCurrentInstance, ref } from 'vue';
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const { visit } = defineProps({
    visit: {
        type: Object,
        required: true
    }
});
const laboratory_slides = ref(1);
const findings_slides = ref(1);
</script>