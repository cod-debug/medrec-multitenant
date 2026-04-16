<template>
  <q-expansion-item
    :icon="props.icon"
    :label="props.title"
    :caption="props.caption"
    style="border-bottom: 1px solid #EEEEEE;"
    :header-class="isAnySubItemActive ? 'text-primary' : ''"
  >
    <q-list padding>
      <q-item
        v-for="subItem in props.subItems"
        :key="subItem.title"
        clickable
        :to="{ name: subItem.route_name }"
        style="border-bottom: 1px solid #F5F5F5;"
        class="q-pl-lg"
      >
        <q-item-section v-if="subItem.icon" avatar>
          <q-icon :name="subItem.icon" size="sm" />
        </q-item-section>

        <q-item-section>
          <q-item-label>{{ subItem.title }}</q-item-label>
          <q-item-label v-if="subItem.caption" caption>{{ subItem.caption }}</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </q-expansion-item>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const props = defineProps({
  title: {
    type: String,
    required: true,
  },

  caption: {
    type: String,
    default: '',
  },

  icon: {
    type: String,
    default: '',
  },

  subItems: {
    type: Array,
    required: true,
    default: () => [],
  },
})

const isAnySubItemActive = computed(() => {
  return props.subItems.some(item => item.route_name === route.name);
});
</script>
