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
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()

    // 認証が必要なページへのアクセス
    if (to.meta.requiresAuth) {
        // ユーザー情報がない場合は取得を試みる
        if (!authStore.user && !authStore.loading) {
            const success = await authStore.fetchUser()
            if (!success) {
                // 認証失敗 → ログイン画面へ
                next('/login')
                return
            }
        }
        next()
    } else if (to.path === '/login' && authStore.isAuthenticated) {
        // ログイン済みでログイン画面にアクセス → ホームへ
        next('/')
    } else {
        next()
    }
})

export default router