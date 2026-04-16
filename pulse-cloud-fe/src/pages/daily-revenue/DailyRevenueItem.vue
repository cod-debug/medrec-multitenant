<template>
    <div>
        <q-card>
            <q-card-section class="bg-grey-2">
                <div class="flex justify-between">
                    <div class="flex items-center q-gutter-sm text-subtitle1 text-grey-9">
                        <q-icon size="xs" name="calendar_today" color="grey-9" />
                        <div class="text-subtitle2">{{ helpers.formatDate(daily_report.date) }}</div>
                        <div>•</div>
                        <span class="text-caption">{{ daily_report.visits.length }} visit{{ daily_report.visits.length !== 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex items-center q-gutter-md text-subtitle1 text-grey-9">
                        <strong class="text-blue-8" style="font-size: 14pt;"> {{ helpers.formatCurrency(daily_report.daily_total) }}</strong>
                    </div>
                </div>
            </q-card-section>
            <q-separator />
                <div class="table-container">
                    <table class="custom-table">
                        <thead class="bg-blue-1">
                            <tr class="text-grey-9">
                                <th class="text-left">Patient Name</th>
                                <th class="text-right">Total Fee</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="table-body-wrapper">
                        <table class="custom-table">
                            <tbody>
                                <tr class="text-grey-9" v-for="visit in daily_report.visits" :key="visit.id">
                                    <td>{{ visit.full_name }}</td>
                                    <td class="text-right">{{ helpers.formatCurrency(visit.total_fee) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        </q-card>
    </div>
</template>
<script setup>
import { getCurrentInstance } from 'vue';
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const { daily_report } = defineProps({
    daily_report: {
        type: Object,
        required: true
    }
});

</script>
<style scoped>
.custom-table {
    width: 100%;
    border-collapse: collapse;
}
.custom-table tr td, .custom-table tr th {
    padding: 12px 24px;
    border-bottom: 1px solid #e0e0e0;
}
.table-body-wrapper {
    max-height: 350px;
    overflow-y: auto;
}
.custom-table thead th {
    position: sticky;
    top: 0;
    background-color: #e3f2fd;
    z-index: 1;
}
</style>