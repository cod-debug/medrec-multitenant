const allowed_roles_admin_and_employee = [2];

const secretary_routes = [
    {
        path: 'select-doctor',
        component: () => import('pages/secretary/SelectDoctorIndex.vue'),
        children: [
            {
                path: '',
                component: () => import('pages/secretary/SelectDoctor.vue'),
                name: 'select-doctor',
                meta: {
                    allowed_user_roles: allowed_roles_admin_and_employee,
                }
            }
        ]
    }
];

export default secretary_routes;
