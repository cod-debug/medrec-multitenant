import admin_routes from './admin'
import common_routes from './common-routes'

const routes = [
  {
    path: '/',
    component: () => import('layouts/PublicLayout.vue'),
    children: [
      { 
        path: '',
        component: () => import('src/pages/LoginPage.vue'),
        name: 'login',
        meta: {
          not_required_auth: true,
        },
      }
    ],
  },
  {
    path: '/admin',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      ...admin_routes,
      ...common_routes,
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
