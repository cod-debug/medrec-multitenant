const allowed_roles_admin_and_employee = [1, 2];

const common_routes = [
    {
        path: '/profile',
        component: () => import('src/pages/profile/ProfileIndex.vue'),
        meta: {
            breadcrumb: 'Profile',
        },
        children: [
            {
                path: 'change-password',
                name: 'profile-change-password',
                component: () => import('src/pages/profile/ChangePassword.vue'),
                meta: {
                    allowed_user_roles: allowed_roles_admin_and_employee,
                    breadcrumb: 'Change Password',
                },
            },
            {
                path: 'update',
                name: 'profile-update',
                component: () => import('src/pages/profile/UpdateProfile.vue'),
                meta: {
                    allowed_user_roles: allowed_roles_admin_and_employee,
                    breadcrumb: 'Update',
                },
            },
        ]
    }
];

export default common_routes;