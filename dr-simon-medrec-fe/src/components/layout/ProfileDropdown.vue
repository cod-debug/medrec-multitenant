<template>
    <div>
        <q-btn-dropdown icon="account_circle" color="blue-1" flat class="profile-settings" :label="full_name" no-caps>
            <q-list>
                <q-item clickable v-close-popup style="font-size: small;" :to="{ name: 'profile-update' }"
                    class="bg-grey-1 text-grey-10">
                    <q-item-section>
                        <q-item-label><q-icon name="edit" /> &nbsp; Update Profile</q-item-label>
                    </q-item-section>
                </q-item>
                <q-item clickable v-close-popup style="font-size: small;" :to="{ name: 'profile-change-password' }"
                    class="bg-grey-1 text-grey-10">
                    <q-item-section>
                        <q-item-label><q-icon name="lock" /> &nbsp; Change Password</q-item-label>
                    </q-item-section>
                </q-item>
            </q-list>
        </q-btn-dropdown>
        <q-btn @click="confirmLogout" style="font-size: small;" class="bg-blue-10"  flat icon="power_settings_new">
            <q-tooltip class="bg-red-10 text-white">Logout</q-tooltip>
        </q-btn>
    </div>
</template>

<script setup>
import { useSpinner } from 'src/composables/spinner';
import { useAuthStore } from 'src/stores/auth';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { computed } from 'vue';

const spinner = useSpinner();

const auth_store = useAuthStore();
const { logout } = auth_store;
const router = useRouter();

const confirmLogout = () => {
    handleLogout();
}

const { login_request } = storeToRefs(auth_store);

const full_name = computed(() => {
  return login_request.value?.name || auth_store.getFullName;
});

const handleLogout = async () => {
    spinner.show();
    await logout();
    // Clear auth store data
    auth_store.clearUserData();
    router.push({ name: 'login' });
    spinner.hide();
}
</script>
