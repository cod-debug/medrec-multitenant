const allowed_roles = [1,2];

const admin_routes = [
    {
        path: 'patients',
        component: () => import('pages/patients/PatientsIndex.vue'),
        children: [
            {
                path: '',
                component: () => import('pages/patients/PatientsPage.vue'),
                name: 'patients',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            },
            {
                path: 'history/:patient_id',
                component: () => import('pages/patients/PatientHistoryPage.vue'),
                name: 'patient-history',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            }
        ],
    },
    {
        path: 'patients-today',
        component: () => import('pages/patients-today/PatientsTodayIndex.vue'),
        children: [
            {
                path: '',
                component: () => import('pages/patients-today/PatientsTodayList.vue'),
                name: 'patients-today',
                meta: {
                    allowed_user_roles: [1, 2],
                },
            },
            {
                path: ':visit_id/:patient_id',
                component: () => import('pages/patients-today/PatientsTodayView.vue'),
                name: 'patients-today-view',
                meta: {
                    allowed_user_roles: [1, 2],
                },
            }
        ]
    },
    {
        path: 'visits',
        component: () => import('pages/visits/VisitIndex.vue'),
        children: [
            {
                path: '',
                component: () => import('pages/visits/VisitList.vue'),
                name: 'visits',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            },
            {
                path: ':visit_id/:patient_id',
                component: () => import('pages/visits/VisitDetails.vue'),
                name: 'visits-details',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            }
        ],
    },
    {
        path: 'user-management',
        component: () => import('pages/user-management/UserManagementIndex.vue'),
        children: [
            {
                path: '',
                component: () => import('pages/user-management/UserManagementList.vue'),
                name: 'user-management',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            },
            {
                path: 'add',
                component: () => import('pages/user-management/UserAdd.vue'),
                name: 'user-management-add',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            }
        ],
    },
    {
        path: 'daily-revenue',
        component: () => import('pages/daily-revenue/DailyRevenueIndex.vue'),
        children: [
            {
                path: '',
                component: () => import('pages/daily-revenue/DailyRevenueReport.vue'),
                name: 'daily-revenue',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            },
            {
                path: 'add',
                component: () => import('pages/user-management/UserAdd.vue'),
                name: 'user-management-add',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            }
        ],
    },
    {
        path: 'operation-schedules',
        component: () => import('pages/operation-schedule/OperationScheduleIndex.vue'),
        children: [
            {
                path: '',
                component: () => import('pages/operation-schedule/OperationSchedule.vue'),
                name: 'operation-schedules',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            }
        ],
    },
    {
        path: 'generic-medicines',
        component: () => import('pages/prescription-ref/GenericMedicinesPage.vue'),
        name: 'generic-medicines',
        meta: {
            allowed_user_roles: allowed_roles,
        },
    },
    {
        path: 'medicine-brands',
        component: () => import('pages/prescription-ref/MedicineBrandsPage.vue'),
        name: 'medicine-brands',
        meta: {
            allowed_user_roles: allowed_roles,
        },
    },
    {
        path: 'medicine-doses',
        component: () => import('pages/prescription-ref/MedicineDosesPage.vue'),
        name: 'medicine-doses',
        meta: {
            allowed_user_roles: allowed_roles,
        },
    },
    {
        path: 'medicine-preparations',
        component: () => import('pages/prescription-ref/MedicinePreparationsPage.vue'),
        name: 'medicine-preparations',
        meta: {
            allowed_user_roles: allowed_roles,
        },
    },
    {
        path: 'backup-management',
        component: () => import('pages/backup-management/BackupManagementPage.vue'),
        name: 'backup-management',
        meta: {
            allowed_user_roles: allowed_roles,
        },
    },
    {
        path: 'inpatients',
        component: () => import('pages/inpatient/InpatientIndex.vue'),
        meta: {
            allowed_user_roles: allowed_roles,
        },
        children: [
            {
                path: 'records',
                component: () => import('pages/inpatient/InpatientRecords.vue'),
                name: 'inpatient-records',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            },
            {
                path: 'details/:patient_id',
                component: () => import('pages/inpatient/InpatientDetails.vue'),
                name: 'inpatient-details',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            },
            {
                path: 'admissions',
                component: () => import('pages/inpatient/InpatientAdmissions.vue'),
                name: 'inpatient-admissions',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            },
            {
                path: 'admission-history',
                component: () => import('pages/inpatient/InpatientAdmissionHistory.vue'),
                name: 'inpatient-admission-history',
                meta: {
                    allowed_user_roles: allowed_roles,
                },
            },
        ]
    },
    {
        path: 'admission-details/:admission_id',
        component: () => import('pages/inpatient/InpatientAdmissionDetails.vue'),
        name: 'inpatient-admission-details',
        meta: {
            allowed_user_roles: allowed_roles,
        },
    },
    {
        path: 'settings',
        component: () => import('pages/settings/SettingsPage.vue'),
        name: 'settings',
        meta: {
            allowed_user_roles: allowed_roles,
        },
    },
];

export default admin_routes;