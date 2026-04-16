<script setup>
import moment from "moment";
import { ref, watch } from "vue";

const emits = defineEmits(['update:from', 'update:to'])
const props = defineProps({
  from: String,
  to: String,
  labelDateFrom: {
    type: String,
    default: ""
  },
  labelDateTo: {
    type: String,
    default: ""
  },
  required: {
    type: [Boolean, String],
    default: false
  }
})

const date_format = "MM/DD/YYYY";
const date_start_proxy = ref(null);
const date_end_proxy = ref(null);
const date_start = ref("");
const date_end = ref("");

const requiredIfDefined = (val) => {
  if (props.required && props.required.toString() === 'true') {
    return val?.trim()?.length > 0 || "This field is required";
  }
  return true;
}

const checkDate = (value) => {
  const momentDate = moment(value, date_format, true);

  if (!momentDate.isValid()) {
    return "Invalid date format"
  }
}

const date_start_options = (date) => {
  if (!date) return;

  let newDate = new Date(date);
  if (date_end.value) {
    let maxDate = new Date(date_end.value);
    if (maxDate.getTime() < newDate.getTime()) {
      return "Invalid date";
    }
  } else {
    if (Date.now() < newDate.getTime()) {
      return "Invalid date";
    }
  }

  return true;
}

const date_end_options = (date) => {
  if (!date) return;

  let newDate = new Date(date);
  if (Date.now() < newDate.getTime()) {
    return "Invalid date";
  }

  if (date_start.value) {
    let minDate = new Date(date_start.value);
    if (minDate.getTime() > newDate.getTime()) {
      return "Invalid date";
    }
  }

  return true;
}

watch(date_start, (newValue) => {
  date_start_proxy.value.hide();
  emits('update:from', newValue);
})

watch(date_end, (newValue) => {
  date_end_proxy.value.hide();
  emits('update:to', newValue);
})
</script>

<template>
  <div style="display: flex; gap: 17px;">
    <div style="flex-grow: 1;">
      <q-input
        v-bind="$attrs"
        v-model="date_start"
        :placeholder="`${date_format}`"
        mask="##/##/####"
        :rules="props.required ? [requiredIfDefined, checkDate, date_start_options] : []"
        :label="labelDateFrom"
      >
        <template v-slot:append>
          <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy
              ref="date_start_proxy"
              transition-show="scale"
              transition-hide="scale"
            >
              <q-date
                v-model="date_start"
                :options="date_start_options"
                :mask="date_format"
              >
                <div class="row items-center justify-end">
                  <q-btn v-close-popup label="Close" color="primary" flat />
                </div>
              </q-date>
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>
    </div>
    <div style="flex-grow: 1;">
      <q-input
        v-bind="$attrs"
        v-model="date_end"
        :placeholder="`${date_format}`"
        mask="##/##/####"
        :rules="props.required ? [requiredIfDefined, checkDate, date_end_options] : []"
        :label="labelDateTo"
      >
        <template v-slot:append>
          <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy
              ref="date_end_proxy"
              transition-show="scale"
              transition-hide="scale"
            >
              <q-date
                v-model="date_end"
                :options="date_end_options"
                :mask="date_format"
              >
                <div class="row items-center justify-end">
                  <q-btn v-close-popup label="Close" color="primary" flat />
                </div>
              </q-date>
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>
    </div>
  </div>
</template>
