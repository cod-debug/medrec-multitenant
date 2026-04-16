<template>
    <div>
        <q-card>
            <q-card-section class="flex justify-between items-center">
                <div class="text-h6"><q-icon name="pending_actions" /> Operation Schedules</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
                <div class="row">
                    <div class="col-12 col-md-5 col-lg-4 col-xl-3 q-pa-md">
                        <q-date v-model="date"
                            :events="events"
                            :event-color="(d) => d == date ? 'orange' : 'secondary'"
                            today-btn
                            @navigation="getSchedulesForActiveMonth"
                        >
                            <template v-slot:append>
                                <q-icon name="event" class="cursor-pointer" />
                            </template>
                        </q-date>
                    </div>
                    <div class="col-12 col-md-7 col-lg-8 col-xl-9 q-pa-md">
                        <q-card>
                            <q-card-section class="flex column q-gutter-md items-center justify-center text-center" v-if="selected_events.length === 0">                                <div class="w-full">
                                    <img :src="schedule_operation_icon" alt="Schedule operation icon" width="200px" height="200px" style="opacity: .7;" />
                                </div>
                                <div style="width: 420px; max-width: 90%;" v-if="date">
                                    <div class="text-h6 text-grey-8">No operations scheduled for {{ moment(date).format('dddd, MMMM DD, YYYY') }}</div>
                                    <p class="text-grey-7" v-if="moment(date).isSameOrAfter(today, 'day')">You can schedule an operation for this date using the button below</p>
                                </div>
                                <div v-else class="text-subtitle2 text-grey-7">Please select a date to view scheduled operations.</div>
                            </q-card-section>

                            <q-card-section v-else>
                                <div class="text-h6 text-grey-9">Operation scheduled for {{ moment(date).format('dddd, MMMM DD, YYYY') }}</div>
                                <q-separator class="q-my-md" />
                                <div class="q-mt-md" v-for="(schedule, key) in selected_events" :key="`schedule-${key}`">
                                    <q-separator v-if="key > 0" />
                                    <q-card class="q-pa-md q-mb-md bg-blue-1">
                                        <q-card-section>
                                            <div class="flex items-start q-gutter-md">
                                                <div style="width: 150px;">
                                                    <img :src="schedule_operation_icon" alt="Patient Avatar" style="width: 100%;" />
                                                </div>
                                                <div class="flex q-gutter-sm column text-grey-9" style="font-size: large;">
                                                    <div><strong>Patient Name:</strong> {{ schedule?.patient?.full_name }}</div>
                                                    <div><strong>Operation Type:</strong> {{ schedule.operation_type }}</div>
                                                    <div><strong>Diagnosis:</strong> {{ schedule.diagnosis || '--' }}</div>
                                                    <div><strong>Remarks:</strong> {{ schedule.remarks || '--' }}</div>
                                                </div>
                                            </div>
                                        </q-card-section>
                                        <div class="absolute q-gutter-sm" style="top: 1rem; right: 1rem;" v-if="!is_past_date">
                                            <q-btn color="primary" round size="sm" icon="edit" @click="handleUpdateScheduleOperation(schedule, key)" />
                                            <q-btn color="red" round size="sm" icon="delete" @click="handleDeleteScheduleOperation(schedule, key)" />
                                        </div>
                                    </q-card>
                                </div>
                            </q-card-section>
                            <q-card-section v-if="date">
                                <div class="text-center q-mb-md">
                                    <div>
                                        <q-btn label="Schedule Operation" color="primary" icon="add" @click="handleOpenScheduleOperation" v-if="moment(date).isSameOrAfter(today, 'day')" />
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <schedule-operation-add v-model="open_add_schedule_operation_modal" :selected_date="date" v-if="date" />
        <schedule-operation-update v-model="open_update_schedule_operation_modal"
            :selected_date="date"
            :selected_schedule="selected_schedule"
            :selected_schedule_key="selected_schedule_key"
        />
        
        <confirmation-modal
            v-model="open_delete_confirmation_modal"
            title="Confirm Removal"
            :message="`Are you sure you want to remove this schedule for <strong>${selected_schedule?.patient?.full_name}</strong>?`"
            confirm_label="Yes, Remove"
            cancel_label="Cancel"
            @confirm="handleRemoveScheduleOperation"
        />
    </div>
</template>

<script setup>
import { ref, watch, onMounted, getCurrentInstance, onBeforeUnmount, computed } from 'vue';
import ScheduleOperationAdd from 'components/modals/ScheduleOperationAdd.vue';
import ScheduleOperationUpdate from 'components/modals/ScheduleOperationUpdate.vue';
import ConfirmationModal from 'components/modals/ConfirmationModal.vue';
import moment from 'moment';
import schedule_operation_icon from 'assets/images/schedule-operation-icon.png';
import { useOperationStore } from 'src/stores/operation';
import { storeToRefs } from 'pinia';
import { Notify } from 'quasar';
import { useSpinner } from 'src/composables/spinner';
import { eventBus } from 'src/utils/event-bus';

const spinner = useSpinner();

const { proxy } = getCurrentInstance();
const helpers = proxy.$helpers;

const operation_store = useOperationStore();
const { getSchedulesForMonth, deleteOperationSchedule } = useOperationStore();
const { get_schedules_for_month_request, delete_operation_schedule_request } = storeToRefs(operation_store);

const today = moment().format('YYYY/MM/DD');
const date = ref(null);
const open_add_schedule_operation_modal = ref(false);
const events = ref([]);
const selected_events = ref([]);
const schedules_for_active_month = ref([]);
const previous_schedules_for_month = ref([]);
const open_update_schedule_operation_modal = ref(false);
const selected_schedule = ref(null);
const selected_schedule_key = ref(null);
const open_delete_confirmation_modal = ref(false);

function handleOpenScheduleOperation() {
    open_add_schedule_operation_modal.value = true;
}

function handleUpdateScheduleOperation(schedule, key) {
    selected_schedule.value = schedule;
    selected_schedule_key.value = key;
    open_update_schedule_operation_modal.value = true;
}

async function getSchedulesForActiveMonth(d = null){
    try {
        const valid_date = date.value || today;
        let active_month = moment(valid_date).format('YYYY/MM/DD');
    
        if(d){
            const updated_month = `${d.year}-${d.month}-01`;
            active_month = moment(updated_month).format('YYYY/MM/DD');
        }

        const payload = {
            active_month,
        }

        spinner.show('Fetching operation schedules for active month...');
        await getSchedulesForMonth(payload);
        const response = get_schedules_for_month_request.value;
        if (response?.success) {
            const scheduled_dates = response.data.schedules.map(schedule => moment(schedule.scheduled_at).format('YYYY/MM/DD'));
            schedules_for_active_month.value = response.data.schedules;
            events.value = scheduled_dates.map(date => moment(date).format('YYYY/MM/DD'));
        } else {
            helpers.showDebugMessage(response.message || 'Failed to save operation schedule.');
            Notify.create({
                message: response.message || 'Failed to save operation schedule.',
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (error) {
        helpers.showDebugMessage('Error fetching operation schedules for active month:', error);
        Notify.create({
            message: 'Error fetching operation schedules for active month.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}

function updateSelectedEvent(dateValue = '') {
    const selected_date = dateValue || date.value;
    let localized_selected_events = schedules_for_active_month.value.filter(schedule => moment(schedule.scheduled_at).isSame(selected_date, 'day'));

    if(localized_selected_events.length === 0 && previous_schedules_for_month.value.length > 0){
        localized_selected_events = previous_schedules_for_month.value.filter(schedule => moment(schedule.scheduled_at).isSame(selected_date, 'day'));
    }
    
    selected_events.value = localized_selected_events;
}

function handleDeleteScheduleOperation(schedule) {
    // Logic to handle deleting a schedule operation
    selected_schedule.value = schedule;
    open_delete_confirmation_modal.value = true;
}

async function handleRemoveScheduleOperation(){
    try {
        spinner.show('Removing schedule operation...');
        await deleteOperationSchedule(selected_schedule.value.id);
        const response = delete_operation_schedule_request.value;
        if (response?.success) {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'green-14'
            });
            selected_events.value = selected_events.value.filter(schedule => schedule.id !== selected_schedule.value.id);
            open_delete_confirmation_modal.value = false;
            selected_schedule.value = null;
            selected_schedule_key.value = null;
            await getSchedulesForActiveMonth();
        } else {
            Notify.create({
                message: response.message,
                position: 'top-right',
                closeBtn: "X",
                timeout: 3000,
                color: 'red-14'
            });
        }
    } catch (error) {
        helpers.showDebugMessage(error);
        Notify.create({
            message: 'An error occurred while removing the schedule operation.',
            position: 'top-right',
            closeBtn: "X",
            timeout: 3000,
            color: 'red-14'
        });
    } finally {
        spinner.hide();
    }
}

const is_past_date = computed(() => {
    return moment(date.value).isBefore(moment(), 'day');
});

watch(date, (new_date) => {
    if(new_date){
        updateSelectedEvent(new_date);
    }
});

onMounted(() => {
    getSchedulesForActiveMonth();

    eventBus.on('scheduleOperationAdded', async (operation_details) => {
        await getSchedulesForActiveMonth();
        if(operation_details){
            selected_events.value = schedules_for_active_month.value.filter(schedule => moment(schedule.scheduled_at).isSame(date.value, 'day'));
        }
    });

    eventBus.on('scheduleOperationUpdated', async (operation_details) => {
        if(operation_details){
            const key = selected_schedule_key.value;
            selected_events.value[key] = operation_details;
        }
    });
});

onBeforeUnmount(() => {
    eventBus.off('scheduleOperationAdded');
    eventBus.off('scheduleOperationUpdated');
});
</script>