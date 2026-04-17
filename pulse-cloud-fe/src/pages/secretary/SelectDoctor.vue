<template>
    <div>
        <div v-if="is_loading">Please wait...</div>
        <div v-else>
            <q-list bordered padding separator v-if="available_doctors.length > 0">
                <q-item v-for="doctor in available_doctors" :key="doctor.id" clickable @click="handleSelectDoctor(doctor)">
                    <q-item-section avatar>
                        <q-avatar circle color="blue-2">
                            <span class="text-caption">
                                {{ doctor.name.split(' ').map(n => n[0]).join('').slice(0, 2) }}
                            </span>
                        </q-avatar>
                    </q-item-section>
                    <q-item-section>
                        <q-item-label>{{ doctor.name }}</q-item-label>
                        <q-item-label caption>{{ doctor.email }}</q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
            <div v-else>
                <div class="text-center q-pa-md">
                    <q-icon name="error" size="2em" color="red" />
                    <div class="text-h6 q-mt-md">No doctors available</div>
                    <div class="text-subtitle2">Please contact administrator for assistance.</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from 'vue';
import { useUsersStore } from 'stores/users';
import { useAuthStore } from 'stores/auth';
import { useSpinner } from 'src/composables/spinner';
import { useRouter } from 'vue-router';

const user_store = useUsersStore();
const auth_store = useAuthStore();
const spinner = useSpinner();
const available_doctors = ref([]);
const router = useRouter();
const is_loading = ref(false);

async function handleSelectDoctor(doctor){
    // save doctor information in auth store just add it to existing user data
    auth_store.setDoctor(doctor);
    router.push({ name: 'patients-today' });
}

async function getDoctors(){
    is_loading.value = true;
    spinner.show('Loading doctors...');
    await user_store.getDoctorsForSecretary();
    const response = user_store.get_doctors_for_secretary_request;
    if(response.success){
        const doctors = response.data?.doctors || [];
        available_doctors.value =  doctors;

        if(doctors.length === 1){
            // if only one doctor is available, select that doctor by default
            doctors[0].only_one_available = true; // add a flag to indicate that this doctor is the only available option
            await handleSelectDoctor(doctors[0]);
        }
    }
    spinner.hide();
    is_loading.value = false;
}

onMounted(() => {
    getDoctors();
});
</script>