<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />
        <q-toolbar-title> MEDREC </q-toolbar-title>
        <profile-dropdown />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <sidebar-profile />
      <q-separator />
      <q-list>
        <q-item-label header>Outpatient </q-item-label>
        <EssentialLink v-for="link in linksList" :key="link.title" v-bind="link" />
      </q-list>
      <q-list>
        <q-item-label header> Inpatient </q-item-label>
        <EssentialLink v-for="link in inpatientLinks" :key="link.title" v-bind="link" />
      </q-list>
      <q-list>
        <q-item-label header> Prescription Reference Data </q-item-label>
        <NavigationGroup
          title="Prescription Reference"
          caption="Manage prescription reference data"
          icon="medical_information"
          :sub-items="prescriptionRefLinks"
        />
      </q-list>
      <q-list>
        <q-item-label header> Management </q-item-label>
        <EssentialLink v-for="link in managementLinks" :key="link.title" v-bind="link" />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue';
import EssentialLink from 'components/EssentialLink.vue';
import NavigationGroup from 'components/NavigationGroup.vue';
import SidebarProfile from 'components/SidebarProfile.vue';
import ProfileDropdown from 'src/components/layout/ProfileDropdown.vue';

const linksList = [
  {
    title: 'Today\'s Patients',
    caption: 'View and manage today\'s patient appointments',
    icon: 'today',
    route_name: 'patients-today',
  },
  {
    title: 'Patient Records',
    caption: 'Manage patient records and appointments',
    icon: 'personal_injury',
    route_name: 'patients',
  },
  {
    title: 'Appointment History',
    caption: 'Manage current and previous patient appointments',
    icon: 'person_search',
    route_name: 'visits',
  },
  {
    title: 'Revenue',
    caption: 'View revenue reports',
    icon: 'attach_money',
    route_name: 'daily-revenue',
  },
  {
    title: 'Operation Schedules',
    caption: 'Set and view operation schedules',
    icon: 'pending_actions',
    route_name: 'operation-schedules',
  },
];

const inpatientLinks = [
  {
    title: 'Admissions / Referrals',
    caption: 'Manage inpatient admissions and referrals',
    icon: 'local_hotel',
    route_name: 'inpatient-admissions',
  },
  {
    title: 'Inpatient Records',
    caption: 'Manage inpatient records and appointments',
    icon: 'personal_injury',
    route_name: 'inpatient-records',
  },
  {
    title: 'Admissions / Referrals History',
    caption: 'View inpatient admission and referral history',
    icon: 'history',
    route_name: 'inpatient-admission-history',
  },
];

const prescriptionRefLinks = [
  {
    title: 'Generic Medicines',
    caption: 'Manage generic medicine names',
    icon: 'medication',
    route_name: 'generic-medicines',
  },
  {
    title: 'Brands',
    caption: 'Manage medicine brands',
    icon: 'business',
    route_name: 'medicine-brands',
  },
  {
    title: 'Dosage Values',
    caption: 'Manage dosage values',
    icon: 'medical_services',
    route_name: 'medicine-doses',
  },
  {
    title: 'Quantity Preparations',
    caption: 'Manage quantity preparations',
    icon: 'science',
    route_name: 'medicine-preparations',
  },
];

const managementLinks = [
  {
    title: 'User Management',
    caption: 'Manage system users',
    icon: 'manage_accounts',
    route_name: 'user-management',
  },
  {
    title: 'Settings',
    caption: 'Manage application settings',
    icon: 'settings',
    route_name: 'settings',
  }
];

const leftDrawerOpen = ref(false)

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}
</script>
