import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import LoginView from '@/views/LoginView.vue'
import AuthCallbackView from '@/views/AuthCallbackView.vue'
import HomeView from '@/views/HomeView.vue'
import FixedCostsView from '@/views/FixedCosts/IndexView.vue'
import GroupsView from '@/views/Groups/IndexView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: LoginView,
            meta: { requiresAuth: false },
        },
        {
            path: '/auth/callback',
            name: 'auth-callback',
            component: AuthCallbackView,
            meta: { requiresAuth: false },
        },
        {
            path: '/',
            name: 'home',
            component: HomeView,
            meta: { requiresAuth: true },
        },
        {
            path: '/fixed-costs',
            name: 'fixed-costs',
            component: FixedCostsView,
            meta: { requiresAuth: true },
        },
        {
            path: '/groups',
            name: 'groups',
            component: GroupsView,
            meta: { requiresAuth: true },
        },
    ],
})

// 認証ガード
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login')
    } else if (to.path === '/login' && authStore.isAuthenticated) {
        next('/')
    } else {
        next()
    }
})

export default router
