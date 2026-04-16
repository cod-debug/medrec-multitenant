<template>
    <q-card>
        <q-card-section>
            <div class="text-h6 text-blue-grey-14">
                <q-icon name="lock" />
                <span class="q-mx-md">Change Password</span>
            </div>
        </q-card-section>
        <q-separator />
        <q-card-section>
            <q-card bordered flat style="width: 500px; max-width: 90%;" class="q-my-md">
                <q-card-section>
                    <q-form greedy @submit.prevent="submitChangePassword">
                        <div class="flex column" style="gap: .3rem;">
                            <q-input label="Current Password" color="indigo-8" :rules="[rules.required]"
                                v-model="change_password_form.temp_password" :disable="disable_form" outlined dense
                                :type="show_current_password ? 'text' : 'password'">
                                <template v-slot:append>
                                    <q-icon :name="show_current_password ? 'visibility_off' : 'visibility'" @click="toggleCurrentPasswordVisibility" />
                                </template>
                            </q-input>

                            <q-input label="New Password" color="indigo-8" :rules="[rules.required]"
                                v-model="change_password_form.new_password" :disable="disable_form" outlined dense
                                :type="show_new_password ? 'text' : 'password'">
                                <template v-slot:append>
                                    <q-icon :name="show_new_password ? 'visibility_off' : 'visibility'" @click="toggleNewPasswordVisibility" />
                                </template>
                            </q-input>

                            <q-input label="Confirm New Password" color="indigo-8" :rules="[rules.required, rules.confirmPassword(change_password_form.new_password)]"
                                v-model="change_password_form.confirm_new_password" :disable="disable_form" outlined dense
                                :type="show_confirm_password ? 'text' : 'password'">
                                <template v-slot:append>
                                    <q-icon :name="show_confirm_password ? 'visibility_off' : 'visibility'" @click="toggleConfirmPasswordVisibility" />
                                </template>
                            </q-input>
                        </div>
                        <div class="text-right">
                            <q-btn :label="!is_submitting? 'Submit': 'Submitting...'" type="submit" color="indigo-14" />
                        </div>
                    </q-form>
                </q-card-section>
            </q-card>
        </q-card-section>
    </q-card>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useAuthStore } from 'src/stores/auth';
import { computed, getCurrentInstance, ref } from 'vue';
import { useRouter } from 'vue-router';

const auth_store = useAuthStore();
const { changePassword } = auth_store;
const { change_password_request } = storeToRefs(auth_store);

const { proxy } = getCurrentInstance();
const rules = proxy.$rules;
const helpers = proxy.$helpers;

const is_submitting = ref(false);
const show_current_password = ref(false);
const show_new_password = ref(false);
const show_confirm_password = ref(false);

const change_password_form = ref({
    temp_password: '',
    new_password: '',
    confirm_new_password: '',
});

const router = useRouter();

const toggleCurrentPasswordVisibility = () => {
    show_current_password.value = !show_current_password.value
}

const toggleNewPasswordVisibility = () => {
    show_new_password.value = !show_new_password.value
}

const toggleConfirmPasswordVisibility = () => {
    show_confirm_password.value = !show_confirm_password.value
}

const submitChangePassword = async () => {
    try {
        is_submitting.value = true;
        await changePassword(change_password_form.value);
        const response = change_password_request.value;

        if (response?.success) {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 2000,
                color: 'green-14'
            });
            
            router.push({ name: 'patients-today' })
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
        is_submitting.value = false;
        change_password_form.value = {
            temp_password: '',
            new_password: '',
            confirm_new_password: '',
        };
    }
}

const disable_form = computed(() => {
    return is_submitting.value;
});
</script>