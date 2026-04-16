<template>
    <div>
        <div class="row">
            <div class="col-12 col-md-5">
                <q-form @submit="getList(true)" class="q-pb-md">
                    <q-input label="Search Inpatient" outlined dense v-model="list_params.keyword">
                        <template v-slot:append>
                            <q-icon name="search" @click="getList(true)" class="cursor-pointer" />
                        </template>
                    </q-input>
                </q-form>
            </div>
        </div>
        <div v-if="is_submitted">
            <q-table :columns="columns"
                :rows="inpatient_list"
                row-key="full_name"
                :rows-per-page-options="[list_params.per_page]"
                :hide-bottom="inpatient_list.length > 0"
                :loading="fetching_inpatients"
            >
                <template v-slot:body-cell-full_name="props">
                    <q-td :props="props">
                        <strong>
                            <q-item class="text-primary flex items-center" style="font-size: 11pt;" :to="{ name: 'inpatient-details', params: { patient_id: props.row.id } }">
                                {{ props.row.full_name }}
                            </q-item>
                        </strong>
                    </q-td>
                </template>
                <template v-slot:body-cell-birthday="props">
                    <q-td :props="props">
                        <span v-if="props.row.age">{{ props.row.age }} years old</span>
                        <span v-else>--</span>
                    </q-td>
                </template>
                <template v-slot:body-cell-latest_date_referred="props">
                    <q-td :props="props">
                        {{ helpers.formatDate(props.row.latest_date_referred) }}
                    </q-td>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <div class="q-gutter-sm">
                            <q-btn color="primary" icon="edit" size="sm" bordered round @click="() => handleOpenUpdateModal(props.row)">
                                <q-tooltip class="bg-grey-14 text-white">Update Inpatient</q-tooltip>
                            </q-btn>
                        </div>
                    </q-td>
                </template>
                <template v-slot:no-data>
                    No inpatients found.
                </template>
            </q-table>
            <div v-if="inpatient_list.length > 0 && get_inpatient_list_request && get_inpatient_list_request.data && get_inpatient_list_request.data.last_page > 0"
                class="flex justify-between q-my-md">
                <q-pagination v-model="list_params.page"
                    :max="get_inpatient_list_request.data.last_page"
                    direction-links
                    :max-pages="6"
                    flat
                    color="grey" 
                    active-color="indigo-14"
                    @update:model-value="getList()"
                />
                <span>
                    Showing {{ get_inpatient_list_request.data.from }} to {{ get_inpatient_list_request.data.to }} of {{ get_inpatient_list_request.data.total }}
                </span>
            </div>
        </div>
        <update-inpatient-modal v-model="open_update_modal" :selected_inpatient="selected_inpatient" />
    </div>
</template>
<script setup>
import { ref, getCurrentInstance, onMounted, onBeforeUnmount } from 'vue';
import { useInpatientStore } from 'src/stores/inpatient';
import { storeToRefs } from 'pinia';
import UpdateInpatientModal from 'src/components/modals/UpdateInpatientModal.vue';
import { eventBus } from 'src/utils/event-bus';

const open_update_modal = ref(false);
const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;
const inpatient_store = useInpatientStore();
const { fetchAllInpatients } = inpatient_store;
const { get_inpatient_list_request } = storeToRefs(inpatient_store);
const fetching_inpatients = ref(false);
const inpatient_list = ref([]);
const is_submitted = ref(false);
const selected_inpatient = ref({});

const columns = [
    { name: 'full_name', label: 'Inpatient\'s name', field: 'full_name', align: 'left' },
    { name: 'birthday', label: 'Age', field: 'birthday', align: 'left' },
    { name: 'gender', label: 'Sex', field: 'gender', align: 'left' },
    { name: 'admissions_count', label: 'Number of Admissions', field: 'admissions_count', align: 'center' },
    { name: 'latest_date_referred', label: 'Latest Date Referred', field: 'latest_date_referred', align: 'center' },
    { name: 'actions', label: 'Actions', field: 'actions', align: 'right' },
];
const list_params = ref({
    page: 1,
    per_page: 10,
    keyword: '',
});

function handleOpenUpdateModal(inpatient) {
    selected_inpatient.value = inpatient;
    open_update_modal.value = true;
}

async function getList(reset_page = false) {
    try {
        is_submitted.value = true;
        fetching_inpatients.value = true;
        if (reset_page) {
            list_params.value.page = 1;
        }
        await fetchAllInpatients(list_params.value);
        const response = get_inpatient_list_request.value;
        if (response?.data && response?.success) {
            inpatient_list.value = response.data.data;
        } else {
            inpatient_list.value = [];
        }
    } catch (error) {
        helpers.showDebugMessage(error);
    } finally {
        fetching_inpatients.value = false;
    }
}


onMounted(() => {
    eventBus.on('updateInpatientInfo', (data) => {
        if(data){
            const selected_key = inpatient_list.value.findIndex(p => p.id == data.id);
            if(selected_key === -1) return;
            inpatient_list.value[selected_key] = data;
        }
    });
    getList(true);
});

onBeforeUnmount(() => {
    eventBus.off('updateInpatientInfo');
});
</script>
