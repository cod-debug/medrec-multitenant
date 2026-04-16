<template>
    <q-page class="q-pa-md">
        <q-card>
            <q-card-section>
                <div class="text-h6"><q-icon name="today" /> Today's Patients</div>
            </q-card-section>
            <q-separator />
            <q-card-section>

                <TodaysPatientAssistant v-if="level_of_authorization == 2" />
                <TodaysPatientDoctor v-if="level_of_authorization == 1" />
                
            </q-card-section> 
        </q-card>
    </q-page>
</template>
<script setup>
import { computed } from 'vue';
import { useAuthStore } from 'src/stores/auth';
import TodaysPatientDoctor from 'components/dashboard/TodaysPatientDoctor.vue';
import TodaysPatientAssistant from 'components/dashboard/TodaysPatientAssistant.vue';

const auth_store = useAuthStore();
const level_of_authorization = computed(() => auth_store.getLevelOfAuthorization);
</script>