<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-6">
                <q-card flat bordered>
                    <q-card-section>
                        <div class="text-subtitle2 q-mb-md"><q-icon name="label_important" />  Fill in the form below to add a new user.</div>
                        <q-form @submit.prevent="handleSubmit" ref="register_user_form">
                            <q-input label="Full Name"
                                color="indigo-8"
                                :rules="[rules.required]"
                                v-model="full_name"
                                :disable="disable_form" 
                                outlined
                                dense
                            />
                            <q-input label="Username"
                                color="indigo-8"
                                :rules="[rules.required]"
                                v-model="username"
                                :disable="disable_form"
                                outlined
                                dense
                            />
                            <q-select label="Role"
                                color="indigo-8"
                                :rules="[rules.requiredSelect]"
                                v-model="level_of_authorization"
                                :disable="disable_form"
                                outlined
                                dense
                                :options="[
                                    { label: 'Doctor', value: 1 },
                                    { label: 'Secretary', value: 2 },
                                ]"
                            />
                            <q-input label="Email Address"
                                color="indigo-8"
                                :rules="[rules.email, rules.required]"
                                v-model="email"
                                :disable="disable_form"
                                outlined
                                dense
                            />
                            <q-input label="Password"
                                color="indigo-8"
                                :rules="[rules.required]"
                                v-model="password"
                                :disable="disable_form"
                                outlined
                                dense
                                type="password"
                            />
                            <q-input label="Confirm Password"
                                color="indigo-8"
                                :rules="[rules.required, rules.confirmPassword(password)]"
                                v-model="confirm_password"
                                :disable="disable_form"
                                outlined
                                dense
                                type="password"
                            />
                            <div class="flex q-gutter-md">
                                <q-btn :label="!disable_form ? 'Submit': 'Submitting...'" type="submit" color="indigo-14" :disable="disable_form" />
                                <q-btn outline label="Cancel" color="grey-8" :to="{ name: 'user-management' }" :disable="disable_form" />
                            </div>
                        </q-form>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </div>
</template>
<script setup>
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useUsersStore } from 'src/stores/users';
import { getCurrentInstance, ref } from 'vue';
import { useSpinner } from 'src/composables/spinner';
import { useRouter } from 'vue-router';

// initialize spinner
const spinner = useSpinner();

const { proxy } = getCurrentInstance();
const rules = proxy.$rules;
const helpers = proxy.$helpers;
const router = useRouter();

const user_store = useUsersStore();
const { registerUser } = user_store;
const { register_user_request } = storeToRefs(user_store);

const register_user_form = ref(null);
const full_name = ref('');
const username = ref('');
const password = ref('');
const confirm_password = ref('');
const email = ref('');
const level_of_authorization = ref('');
const disable_form = ref(false);

async function handleSubmit() {
    try {
        // validate form first before submitting
        const is_valid = await register_user_form.value.validate();
        if (!is_valid) {
            return;
        }
        disable_form.value = true;
        const payload = {
            name: full_name.value,
            username: username.value,
            email: email.value,
            password: password.value,
            confirm_password: confirm_password.value,
            level_of_authorization: parseInt(level_of_authorization.value.value),
        }

        spinner.show('Processing request...');
        await registerUser(payload);

        const response = register_user_request.value;
        if (response?.success) {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            router.push({ name: 'user-management' });
        } else {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: 'An error occurred while processing your request.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        disable_form.value = false;
        spinner.hide();
    }
}
</script>