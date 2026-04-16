<template>
    <q-dialog v-model="open_modal" persistent>
        <q-card style="width: 400px; max-width: 90vw; padding: 2px;" class="bg-primary-gradient">
            <div class="bg-white text-blue-grey-14">
                <q-card-section>
                    <div class="text-h5">
                        <q-icon name="lock_reset" /> Forgot Password
                    </div>
                </q-card-section>

                <q-separator />

                <q-card-section>
                    <p>
                        Please enter your registered email address. A temporary password will be sent to your email.
                        Use it to log in and make sure to update your password afterward.
                    </p>
                    <q-input color="blue-grey-14" v-model="forgot_password_email" label="Email Address" type="email" autofocus />
                </q-card-section>

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" v-close-popup />
                    <q-btn :label="!is_submitting ? 'Send Temporary Password' : 'Sending Requst...'"
                        :disable="is_submitting"
                        color="indigo-14"
                        @click="submitForgotPassword"
                    />
                </q-card-actions>
            </div>
        </q-card>
    </q-dialog>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useAuthStore } from 'src/stores/auth';
import { ref, getCurrentInstance } from 'vue';

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;

// initialize local variables
const open_modal = ref(false);
const forgot_password_email = ref('');
const is_submitting = ref(false);

// initialize stores
const auth_store = useAuthStore();
const { forgot_password_request } = storeToRefs(auth_store);
const { forgotPassword } = auth_store;

const submitForgotPassword = async () => {
  // execute forgot password request here...
  try {
    is_submitting.value = true;

    const payload = {
        email: forgot_password_email.value,
    }

    await forgotPassword(payload);
    const response = forgot_password_request.value;

    if (response?.success) {
      Notify.create({
        message: response.message,
        position: 'top-right',
        closeBtn: "X",
        timeout: 3000,
        color: 'green-14'
      });
    } else {
      Notify.create({
        message: response.message,
        position: 'top-right',
        closeBtn: "X",
        timeout: 3000,
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
    open_modal.value =  false;
  }
}

defineExpose({
    open_modal,
})
</script>