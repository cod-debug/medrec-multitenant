<template>
    <div>
        <q-card>
            <q-card-section>
                <div class="flex justify-between items-center">
                    <div class="text-h6"><q-icon name="today" /> Today's Patients</div>
                    <div>
                        <q-btn outline label="View Daily Revenue" color="grey-8" icon="attach_money" @click="open_daily_revenue_modal = true" />
                    </div>
                </div>
            </q-card-section>
            <q-separator />
            <q-card-section>
                <todays-patient-doctor v-if="level_of_authorization == 1" />
                <todays-patient-assistant v-if="level_of_authorization == 2" />
            </q-card-section> 
        </q-card>
        <daily-revenue-modal v-if="open_daily_revenue_modal" v-model="open_daily_revenue_modal" />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from 'src/stores/auth';
import TodaysPatientDoctor from 'components/dashboard/TodaysPatientDoctor.vue';
import TodaysPatientAssistant from 'components/dashboard/TodaysPatientAssistant.vue';
import DailyRevenueModal from 'components/modals/DailyRevenueModal.vue';

const auth_store = useAuthStore();
const open_daily_revenue_modal = ref(false);
const level_of_authorization = computed(() => auth_store.getLevelOfAuthorization);
</script>