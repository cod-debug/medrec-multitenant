<template>
  <q-page class="flex flex-center" style="height: 95vh;">
    <q-card style="width: 800px; max-width: 90vw;">
      <q-card-section>
        <div class="row">
          <div class="col-12 col-md-6 q-pa-xl">
            <div class="flex justify-center items-center full-height">
              <q-img :src="isometric_image" style="min-width: 200px; min-height: 200px;" />
            </div>
          </div>
          <div class="col-12 col-md-6 q-pa-lg">
            <div class="text-h5">
              Hello, Welcome Back!
            </div>
            <p>Please sign in to your account to continue.</p>
            <q-form autocomplete="off" greedy @submit.prevent="handleSignIn">
              <!-- DISREGARD: just a trick to not auto fill email and password upon initial loading of the login form -->
              <input type="text" name="prevent_autofill_username" autocomplete="off"
                style="position: absolute; top: -9999px;" />
              <input type="password" name="prevent_autofill_password" autocomplete="off"
                style="position: absolute; top: -9999px;" />

              <q-input v-model="login_form.email" :rules="[rules.required, rules.email]" label="Username" :disable="is_logging_in" color="indigo-6"
                autocomplete="off">
                <template v-slot:prepend>
                  <q-icon name="person" />
                </template>
              </q-input>
              <q-input v-model="login_form.password" :rules="[rules.required]" :type="password_input_type" label="Password" :disable="is_logging_in"
                color="indigo-6" autocomplete="off">
                <template v-slot:prepend>
                  <q-icon name="lock" />
                </template>
                <template v-slot:append>
                  <q-icon :name="password_input_type !== 'password' ? 'visibility' : 'visibility_off'"
                    @click="togglePasswordType()" />
                </template>
              </q-input>
              <q-btn type="submit" class="full-width q-my-md bg-primary-gradient" :disable="is_logging_in">
                <q-spinner v-if="is_logging_in" class="q-mr-md" />
                <span>{{ !is_logging_in ? 'SIGN IN' : 'SIGNING IN...' }}</span>
              </q-btn>
              <div class="text-center q-mb-sm">
                <q-btn flat color="indigo-14" label="Forgot Password" @click="handleOpenForgotPassword" />
              </div>
              <p class="text-grey-8 text-center">Don't have an account yet? Please contact administrator.</p>
            </q-form>
          </div>
        </div>
      </q-card-section>
    </q-card>
    <forgot-password-modal ref="forgot_password_modal" />
  </q-page>
</template>

<script setup>
import { getCurrentInstance, onMounted, ref } from 'vue';
import isometric_image from 'assets/images/logo.png';
import { useRouter } from 'vue-router';
import { useAuthStore } from 'src/stores/auth';
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import ForgotPasswordModal from 'src/components/auth/modals/ForgotPasswordModal.vue';

const forgot_password_modal = ref(null);

const { proxy } = getCurrentInstance();
const rules = proxy.$rules;
const helpers = proxy.$helpers;

const router = useRouter();
const auth_store = useAuthStore();
const { login_request } = storeToRefs(auth_store);
const { login } = auth_store;

const is_logging_in = ref(false);
const login_form = ref({
  email: '',
  password: '',
});

const password_input_type = ref('password');

const togglePasswordType = () => {
  if (password_input_type.value === 'password') {
    password_input_type.value = 'text';
  } else {
    password_input_type.value = 'password';
  }
}

const checkAuth = () => {
  if (auth_store.isAuthenticated) {
    // check if token is valid, then redirect to dashboard
    // TODO: API for validating token
    
    Notify.create({
      message: "Already logged in.",
      position: 'top-right',
      closeBtn: "X",
      timeout: 3000,
      color: 'green-14'
    });
    redirectToDashboard();
  }
}

const redirectToDashboard = () => {
  router.push({ name: 'patients-today' });
}

const handleSignIn = async () => {
  // execute login here...
  try {
    is_logging_in.value = true;
    await login(login_form.value);
    const response = login_request.value;

    if (response?.success) {
      const token = response.data.token;
      const level_of_authorization = response.data.level_of_authorization;
      const full_name = response.data.name;
      const required_change_password = response.data.required_change_password;

      auth_store.setUserData({
        token,
        level_of_authorization,
        full_name,
      });

      if(level_of_authorization === 2){
        router.push({ name: 'select-doctor' });
        return false;
      }


      Notify.create({
        message: response.message,
        position: 'top-right',
        closeBtn: "X",
        timeout: 3000,
        color: 'green-14'
      });
      if(required_change_password){
        router.push({ name: 'profile-change-password' });
        return false;
      }
      redirectToDashboard();
    } else {
      Notify.create({
        message: response.message,
        position: 'top-right',
        closeBtn: "X",
        timeout: 3000,
        color: 'red-14',
      });
      login_form.value.password = "";
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
    is_logging_in.value = false;
  }
}

const handleOpenForgotPassword = () => {
  forgot_password_modal.value.open_modal = true;
}
onMounted(() => {
  checkAuth();
});
</script>