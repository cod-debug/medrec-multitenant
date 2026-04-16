import { route } from 'quasar/wrappers'
import {
    createRouter,
    createMemoryHistory,
    createWebHistory,
    createWebHashHistory,
} from 'vue-router'
import routes from './routes'
import { Notify } from 'quasar'
import { useAuthStore } from 'src/stores/auth'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default route(function () {
    const createHistory = process.env.SERVER
        ? createMemoryHistory
        : process.env.VUE_ROUTER_MODE === 'history'
          ? createWebHistory
          : createWebHashHistory

    const Router = createRouter({
        scrollBehavior: () => ({ left: 0, top: 0 }),
        routes,
        history: createHistory(process.env.MODE === 'ssr' ? void 0 : process.env.VUE_ROUTER_BASE),
    })

    Router.beforeEach((to, from, next) => {
        const auth_store = useAuthStore()
        const access_token = auth_store.getToken
        const level_of_authorization = auth_store.getLevelOfAuthorization

        const not_requires_auth = to.meta?.not_required_auth || false
        const user_allowed = to.meta.allowed_user_roles
            ? to.meta.allowed_user_roles.includes(level_of_authorization)
            : false

        // if auth is required and no token, redirect to login
        if (!not_requires_auth && !access_token) {
            Notify.create({
                message: `Unauthorized user detected.`,
                position: 'top-right',
                closeBtn: 'X',
                timeout: 2000,
                color: 'red',
            })

            return next({ name: 'login' })
        }

        // if route requires auth and user is not allowed, redirect to respective dashboard
        if (!not_requires_auth && !user_allowed) {
            return next({ name: 'patients-today' });
        }

        return next()
    })

    return Router
})
