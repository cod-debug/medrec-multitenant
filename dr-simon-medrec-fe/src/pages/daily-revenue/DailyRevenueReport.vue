<template>
    <div>
        <q-card>
            <q-card-section>
                <div class="text-h6"><q-icon name="attach_money" /> Daily Revenue</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <date-range-picker labelDateFrom="Date From *" labelDateTo="Date To *"
                            v-model:from="list_params.date_start" v-model:to="list_params.date_end" outlined stack-label
                            required="true"
                        />
                    </div>
                    <div class="col-12 col-md-6 q-px-md">
                        <div class="flex" style="height: 56px;">
                            <q-btn type="submit" label="Generate Report" color="primary" class="q-px-lg" @click="getReport()" />
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <q-card class="q-my-md" v-if="daily_reports.length > 0">
            <q-card-section>
                <div class="row" v-if="total_revenue">
                    <div class="col-12 col-md-7">
                        <q-card class="bg-blue-1">
                            <q-card-section>
                                <div class="flex justify-between">
                                    <div class="flex items-center q-gutter-sm text-subtitle1 text-grey-10">
                                        <q-avatar size="xl" icon="payments" color="blue-3" class="text-blue-10" />
                                        <div class="flex column">
                                            <div class="text-h6 text-grey-9">TOTAL REVENUE</div>
                                            <div class="flex q-gutter-sm text-grey-8">
                                                <div>{{ daily_reports.length }} day{{ daily_reports.length > 1 ? 's' : '' }} </div>
                                                <div>•</div>
                                                <div>{{ total_visits_count }} visit{{ total_visits_count > 1 ? 's' : '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center q-gutter-md text-subtitle1 text-grey-9">
                                        <strong class="text-blue-8" style="font-size: 20pt;"> {{ helpers.formatCurrency(total_revenue) }}</strong>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="row">
                            <div class="col-12 q-pa-md" v-if="daily_reports && daily_reports.length > 0">
                                <div class="flex q-gutter-md column">
                                    <daily-revenue-item v-for="daily_report in daily_reports" :key="daily_report.id" :daily_report="daily_report" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </div>
</template>
<script setup>
import { ref, getCurrentInstance } from 'vue';
import { usePatientStore } from 'src/stores/patient';
import { storeToRefs } from 'pinia';
import DateRangePicker from 'src/components/reusables/DateRangePicker.vue';
import { useSpinner } from 'src/composables/spinner';
import DailyRevenueItem from './DailyRevenueItem.vue';

const patient_store = usePatientStore();
const { getDailyRevenueReports } = patient_store;
const { get_daily_revenue_report_request } = storeToRefs(patient_store);
const { proxy } = getCurrentInstance();
const spinner = useSpinner();
const helpers = proxy.$helpers;
const daily_reports = ref([]);
const total_revenue = ref(0);
const total_visits_count = ref(0);

const list_params = ref({
    page: 1,
    per_page: 10,
    date_start: null,
    date_end: null,
});

async function getReport() {
    try {
        spinner.show('Generating daily revenue report...');
        await getDailyRevenueReports(list_params.value);
        const response = get_daily_revenue_report_request.value;
        if (response?.data && response?.success) {
            daily_reports.value = response.data.daily_reports;
            total_revenue.value = response.data.total_revenue;
            total_visits_count.value = response.data.daily_reports.reduce((total, report) => total + report.visits.length, 0);
        } else {
            helpers.showDebugMessage(response);
        }
    } catch (error) {
        helpers.showDebugMessage(error);
    } finally {
        spinner.hide();
    }
}
</script>