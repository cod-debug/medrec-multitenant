<template>
    <div>
        <q-btn label="Add User" :to="{ name: 'user-management-add' }" color="primary" icon="person_add" size="sm" class="q-mb-md" />

        <div class="q-mb-md">
            <q-btn-group>
                <q-btn :color="list_params.user_type === 1 ? 'primary' : 'blue-4'" label="Doctors" icon="timeline" @click="list_params.user_type = 1" />
                <q-btn :color="list_params.user_type === 2 ? 'primary' : 'blue-4'" label="Secretaries" icon="visibility" @click="list_params.user_type = 2" />
            </q-btn-group>
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                <q-form @submit="getList(true)" class="q-pb-md">
                    <q-input label="Search User" outlined dense v-model="list_params.keyword">
                        <template v-slot:append>
                            <q-icon name="search" @click="getList(true)" class="cursor-pointer" />
                        </template>
                    </q-input>
                </q-form>
            </div>
        </div>

        <q-table :columns="columns"
            :rows="user_list"
            row-key="name"
            :rows-per-page-options="[list_params.per_page]"
            :hide-bottom="user_list.length > 0"
            :loading="fetching_user"
        >
            <template v-slot:body-cell-secretary="props">
                <q-td :props="props">
                    <q-badge v-for="secretary in props.row.secretaries" :key="secretary" :label="secretary" color="secondary" class="q-mr-xs" />
                </q-td>
            </template>
            <template v-slot:body-cell-doctor="props">
                <q-td :props="props">
                    <q-badge v-for="doctor in props.row.doctors" :key="doctor" :label="doctor" color="secondary" class="q-mr-xs" />
                </q-td>
            </template>
            <template v-slot:body-cell-actions="props">
                <q-td :props="props">
                    <q-btn outline color="primary" size="sm" icon="person_add_alt" @click="addAsMySecretary(props.row.id)" v-if="list_params.user_type === 2" >
                        <q-tooltip>Add as my secretary</q-tooltip>
                    </q-btn>
                </q-td>
            </template>
        </q-table>
        <div v-if="user_list.length > 0 && user_list_request && user_list_request.data && user_list_request.data.last_page > 0"
            class="flex justify-between q-my-md">
            <q-pagination v-model="list_params.page"
                :max="user_list_request.data.last_page"
                direction-links
                :max-pages="6"
                flat
                color="grey" 
                active-color="indigo-14"
                @update:model-value="getList()"
            />
            <span>
                Showing {{ user_list_request.data.from }} to {{ user_list_request.data.to }} of {{ user_list_request.data.total }}
            </span>
        </div>
    </div>
</template>
<script setup>
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useUsersStore } from 'src/stores/users';
import { onMounted, ref, getCurrentInstance, watch } from 'vue';
import { useSpinner } from 'src/composables/spinner';

// user store
const user_store = useUsersStore();
const { getUserListRequest } = user_store;
const { user_list_request } = storeToRefs(user_store);

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const spinner = useSpinner();

const columns = ref([]);

const columns_map = {
    doctor: [
        { name: 'name', label: 'Full Name', field: 'name', align: 'left' },
        { name: 'email', label: 'Email Address', field: 'email', align: 'left' },
        { name: 'username', label: 'Username', field: 'username', align: 'left' },
        { 
            name: 'secretary', label: 'Secretary', field: 'secretary', align: 'left', 
            style: 'max-width: 100px; word-wrap: break-word; white-space: normal;',
            headerStyle: 'max-width: 100px' 
        },
        { name: 'actions', label: 'Actions', field: 'actions', align: 'left' },
    ],
    secretary: [
        { name: 'name', label: 'Full Name', field: 'name', align: 'left' },
        { name: 'email', label: 'Email Address', field: 'email', align: 'left' },
        { name: 'username', label: 'Username', field: 'username', align: 'left' },
        { 
            name: 'doctor', label: 'Doctors', field: 'doctor', align: 'left', 
            style: 'max-width: 100px; word-wrap: break-word; white-space: normal;',
            headerStyle: 'max-width: 100px' 
        },
        { name: 'actions', label: 'Actions', field: 'actions', align: 'left' },
    ],
};

const list_params = ref({
    per_page: 10,
    page: 1,
    keyword: '',
    user_type: 1,
});
const user_list = ref([]);
const fetching_user = ref(false);

async function getList(is_search = false) {
    try {
        user_list.value = [];
        if(is_search ){
            list_params.value.page = 1;
        }

        fetching_user.value = true;
        await getUserListRequest(list_params.value);

        const response = user_list_request.value;

        if (response.success) {
            user_list.value = response.data.data;
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
        fetching_user.value = false;
    }
}

async function addAsMySecretary(secretary_id) {
    try {
        spinner.show('Adding secretary...');
        const payload = {
            id: secretary_id,
        }
        await user_store.addSecretary(payload);

        const response = user_store.add_secretary_request;
        if (response?.success) {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            getList();
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

watch(() => list_params.value.user_type, (val) => {
    const column_key = val === 1 ? 'doctor' : 'secretary';
    columns.value = columns_map[column_key];
    getList(true);
});

onMounted(() => {
    columns.value = columns_map['doctor'];
    getList(true);
});
</script>
