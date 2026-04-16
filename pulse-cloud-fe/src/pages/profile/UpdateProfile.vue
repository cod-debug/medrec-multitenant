<template>
    <q-page>
        <q-card>
            <q-card-section>
                <div class="text-h6 text-blue-grey-14">
                    <q-icon name="edit" />
                    <span class="q-mx-md">Update Profile</span>
                </div>
            </q-card-section>
            <q-separator />
            <q-card-section>
                <q-card bordered flat style="width: 500px; max-width: 90%;" class="q-my-md">
                    <q-card-section>
                        <q-form greedy @submit.prevent="submitUpdateProfile">
                            <div class="flex column" style="gap: .3rem;">
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
                                <q-input label="Email"
                                    color="indigo-8"
                                    :model-value="email"
                                    class="q-mb-lg"
                                    readonly
                                    outlined
                                    dense
                                    :loading="is_fetching_user"
                                />
                            </div>
                            <div class="text-right">
                                <q-btn :label="!is_submitting ? 'Submit': 'Submitting...'" type="submit" color="indigo-14" :disable="disable_form" />
                            </div>
                        </q-form>
                    </q-card-section>
                </q-card>
            </q-card-section>
        </q-card>
    </q-page>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useAuthStore } from 'src/stores/auth';
import { useUsersStore } from 'src/stores/users';
import { computed, getCurrentInstance, onMounted, ref } from 'vue';

const { proxy } = getCurrentInstance();
const rules = proxy.$rules;
const helpers = proxy.$helpers;

// initialize user store
const user_store = useUsersStore();
const { update_info_request, get_current_user_request } = storeToRefs(user_store);
const { updateProfileRequest, getCurrentUserRequest } = user_store;

// initialize auth store
const auth_store = useAuthStore();
const { login_request } = storeToRefs(auth_store);

// initialize local variables
const is_submitting = ref(false);
const is_fetching_user = ref(false);
const username = ref('');
const full_name = ref(auth_store.getFullName);
const email = ref('');
const office_name = ref('');

const submitUpdateProfile = async () => {
    try {
        is_submitting.value = true;
        const payload = {
            name: full_name.value,
            username: username.value,
        }
        
        await updateProfileRequest(payload);
        const response = update_info_request.value;

        if(response.success){
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 2000,
                color: 'green-14'
            });

            // set updated name in store
            login_request.value = {
                ...login_request.value,
            }
            login_request.value.name = full_name.value;
            
            // Update auth store with new name
            auth_store.setUserData({
                token: auth_store.token,
                level_of_authorization: auth_store.level_of_authorization,
                full_name: full_name.value,
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
        is_submitting.value = false;
    }
}

const handleGetCurrentUser = async () => {
    try {
        is_fetching_user.value = true;
        await getCurrentUserRequest();
        const response = get_current_user_request.value;
        if(response.success){
            email.value = response.data.email;
            office_name.value = response.data?.office?.name || 'N/A';
            username.value = response.data.username;
        }
    } catch (e) {
        helpers.showDebugMessage(e);
        Notify.create({
            message: `Something went wrong while fetching data. Please contact administrator`,
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14',
        });
    } finally {
        is_submitting.value = false;
        is_fetching_user.value = false;
    }
}

// computed varaibles
const disable_form = computed(() => {
    return is_submitting.value || is_fetching_user.value;
});

// mounted
onMounted(() => {
    handleGetCurrentUser();
});
</script>