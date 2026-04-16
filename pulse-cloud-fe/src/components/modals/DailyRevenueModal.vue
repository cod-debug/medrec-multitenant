<template>
    <q-dialog v-model="open_modal" persistent>
        <q-card :style="{ width: '600px', maxWidth: '90%', maxHeight: '80%' }" class="relative">
            <q-card-section class="bg-primary text-white">
                <div class="text-h6"><q-icon name="attach_money" /> Daily Revenue</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
                <div class="flex items-center justify-end total-revenue" v-if="visits.length > 0">
                    <span>Total Revenue for Today: </span>
                    <strong class="text-green-7 q-ml-sm" style="font-size: 14pt;">{{ helpers.formatCurrency(total_revenue) }}</strong>
                </div>
                <table class="custom-table" v-if="visits.length > 0">
                    <thead>
                        <tr>
                            <th class="text-left">Patient Name</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="visit in visits" :key="visit.id">
                            <td>{{ visit.full_name }}</td>
                            <td class="text-right">{{ helpers.formatCurrency(visit.total_fee) }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="text-center">No visits found for today yet.</div>
            </q-card-section>
            <q-btn outline round color="white" icon="close" @click="open_modal = false" class="absolute" style="top: 12px; right: 12px; z-index: 50000;">
                <q-tooltip class="bg-red-14 text-white">Close</q-tooltip>
            </q-btn>
        </q-card>
    </q-dialog>
</template>

<script setup>
import { computed, getCurrentInstance, onMounted, ref, watch } from 'vue';
import { usePatientStore } from '/src/stores/patient';
import { storeToRefs } from 'pinia';
import { useSpinner } from 'src/composables/spinner';

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const spinner = useSpinner();
const patientStore = usePatientStore();
const { getDailyRevenue } = patientStore;
const { get_daily_revenue_request } = storeToRefs(patientStore);
const visits = ref([]);
const total_revenue = ref(0);

const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

async function loadDailyRevenue() {
    try {
        spinner.show('Fetching daily revenue...');
        await getDailyRevenue();
        const response = get_daily_revenue_request.value;
        visits.value = response?.data?.visits || [];
        total_revenue.value = response?.data?.total_revenue || 0;
    } catch (error) {
        helpers.showDebugMessage(error);
    } finally {
        spinner.hide();
    }
}

const open_modal = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

watch(open_modal, (newVal) => {
    if (newVal) {
        loadDailyRevenue();
    }
});

onMounted(() => {
    loadDailyRevenue();
});
</script>

<style scoped>
.custom-table {
    width: 100%;
    border-collapse: collapse;
}
.custom-table th, .custom-table td, .total-revenue {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
}
</style>