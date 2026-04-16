<template>
    <q-page class="q-pa-md">
        <q-card>
            <q-card-section>
                <div class="text-h6 text-blue-grey-14">
                    <q-icon name="settings" />
                    <span class="q-mx-md">Application Settings</span>
                </div>
            </q-card-section>
            <q-separator />
            <q-card-section>
                <q-form greedy @submit.prevent="submit" style="max-width: 400px;">
                    <div class="flex column" style="gap: .3rem;">
                        <q-input label="PTR Number"
                            :rules="[rules.required]"
                            v-model="settings.ptr_number"
                            outlined
                            dense
                        />
                    </div>
                    <div>
                        <q-btn label="Save Updates" icon="check_circle" size="sm" type="submit" color="indigo-14" />
                    </div>
                </q-form>
            </q-card-section>
        </q-card>
    </q-page>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useSpinner } from 'src/composables/spinner';
import { useSettingsStore } from 'src/stores/settings';
import { onMounted, ref, getCurrentInstance } from 'vue';

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const rules = proxy.$rules; 
const spinner = useSpinner();
const settings_store = useSettingsStore();
const { getSettings, updateSettings } = settings_store;
const { get_settings_request, update_settings_request } = storeToRefs(settings_store);

const settings = ref({
    ptr_number: '',
    appointment_duration: 30,
});

async function submit(){
    try {
        spinner.show('Updating settings...');
        await updateSettings(settings.value);
        const response = update_settings_request.value;
        if (response?.success) {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 2000,
                color: 'green-14'
            });
        } else {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 2000,
                color: 'red-14',
            });
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: `Something went wrong. Please contact administrator`,
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14',
        });
    } finally {
        spinner.hide();
    }
}

async function fetchSettings(){
    try {
        spinner.show('Fetching settings...');
        await getSettings();
        const response = get_settings_request.value;
        if(response.success){
            if(response && response.data && response.data.settings && response.data.settings.data){
                settings.value = response.data.settings?.data || null;
            } else {
                settings.value = {
                    ptr_number: '',
                }
            }
        } else {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 2000,
                color: 'red-14',
            });
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: `Something went wrong. Please contact administrator`,
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14',
        });
    } finally {
        spinner.hide();
    }
}

onMounted(() => {
    fetchSettings();
});
</script>